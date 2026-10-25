<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentTransactionUsecase extends Usecase
{
    /**
     * Ambil semua transaksi konfirmasi pembayaran dengan filter status & pencarian.
     */
    public function getAll(array $filterData = []): array
    {
        try {
            $hasKeywords = ! empty($filterData['keywords']);

            $query = DB::table(DatabaseConst::PAYMENT_TRANSACTIONS().' as pt')
                ->whereNull('pt.deleted_at')
                ->when($hasKeywords, function ($q) use ($filterData) {
                    $keywords = $filterData['keywords'];
                    $q->leftJoin(DatabaseConst::USER().' as u', 'pt.user_id', '=', 'u.id');

                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('pt.order_id', 'like', '%'.$keywords.'%')
                            ->orWhere('u.name', 'like', '%'.$keywords.'%')
                            ->orWhere('u.email', 'like', '%'.$keywords.'%');
                    });
                })
                ->when($filterData['status'] ?? false, function ($q, $status) {
                    if (in_array($status, ['verification', 'waiting_approval'])) {
                        return $q->whereIn('pt.status', ['waiting_approval', 'verification']);
                    }

                    return $q->where('pt.status', $status);
                })
                ->when($filterData['category'] ?? false, function ($q, $category) {
                    return $q->where('pt.payment_category', $category);
                })
                ->when($filterData['exclude_unapproved_manual'] ?? false, function ($q) use ($filterData) {
                    if (($filterData['status'] ?? '') === 'paid') {
                        return;
                    }

                    return $q->where(function ($sub) {
                        $sub->where('pt.payment_category', '!=', 'manual_transfer')
                            ->orWhere('pt.status', '=', 'paid');
                    });
                });

            // Fast count without unnecessary joins when no keyword filter
            $total = (clone $query)->count('pt.id');

            // Apply relations and select only required display columns (skip heavy json payloads)
            if (! $hasKeywords) {
                $query->leftJoin(DatabaseConst::USER().' as u', 'pt.user_id', '=', 'u.id');
            }

            $page = (int) ($filterData['page'] ?? request()->get('page', 1));
            $perPage = 20;

            $items = $query->select([
                'pt.id',
                'pt.order_id',
                'pt.user_id',
                'pt.cefr',
                'pt.tier',
                'pt.payment_category',
                'pt.payment_channel',
                'pt.amount',
                'pt.status',
                'pt.created_at',
                'pt.paid_at',
                'pt.expires_at as expired_at',
                'pt.transfer_proof_url as proof_url',
                'u.name as student_name',
                'u.email as student_email',
            ])
                ->orderBy('pt.created_at', 'desc')
                ->forPage($page, $perPage)
                ->get();

            // Map class name from cache without database join
            $classNames = Cache::remember('course_classes_cefr_map', now()->addDay(), function () {
                return DB::table(DatabaseConst::COURSE_CLASSES())->pluck('name', 'cefr')->toArray();
            });

            foreach ($items as $item) {
                $item->class_name = $classNames[$item->cefr] ?? 'Kelas '.$item->cefr;
            }

            $paginator = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return Response::buildSuccess(['list' => $paginator], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil rincian transaksi berdasarkan ID.
     */
    public function getByID(string $id): array
    {
        try {
            $data = DB::table(DatabaseConst::PAYMENT_TRANSACTIONS().' as pt')
                ->leftJoin(DatabaseConst::USER().' as u', 'pt.user_id', '=', 'u.id')
                ->leftJoin(DatabaseConst::COURSE_CLASSES().' as cc', 'pt.cefr', '=', 'cc.cefr')
                ->select(
                    'pt.*',
                    'pt.expires_at as expired_at',
                    'pt.cefr as cefr_level',
                    'pt.tier as tier_name',
                    'pt.payment_channel as payment_channel_name',
                    'pt.transfer_proof_url as proof_url',
                    'u.name as student_name',
                    'u.name as user_name',
                    'u.email as student_email',
                    'u.email as user_email',
                    'cc.name as class_name'
                )
                ->whereNull('pt.deleted_at')
                ->where('pt.id', $id)
                ->first();

            if (! $data) {
                return Response::buildErrorNotFound('Transaksi tidak ditemukan');
            }

            return Response::buildSuccess(collect($data)->toArray(), ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * ACC Pembayaran Siswa (Otomatis Aktifkan Akses Kelas).
     */
    public function approve(string $id): array
    {
        DB::beginTransaction();
        try {
            $tx = DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (! $tx) {
                DB::rollback();

                return Response::buildErrorNotFound('Transaksi tidak ditemukan.');
            }

            if ($tx->status === 'paid') {
                DB::rollback();

                return Response::buildError(ResponseConst::HTTP_BAD_REQUEST, 'Transaksi ini sudah lunas sebelumnya.');
            }

            // 1. Update status transaksi menjadi paid
            $adminId = Auth::user()?->id;
            DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->where('id', $id)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'approved_by' => $adminId,
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            // 2. Berikan akses kelas kepada siswa (user_class_access)
            $existingAccess = DB::table(DatabaseConst::USER_CLASS_ACCESS())
                ->where('user_id', $tx->user_id)
                ->where('cefr', $tx->cefr)
                ->first();

            if ($existingAccess) {
                DB::table(DatabaseConst::USER_CLASS_ACCESS())
                    ->where('id', $existingAccess->id)
                    ->update([
                        'tier' => $tx->tier,
                        'status' => 'active',
                        'granted_by' => 'admin_acc',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table(DatabaseConst::USER_CLASS_ACCESS())
                    ->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => $tx->user_id,
                        'cefr' => $tx->cefr,
                        'tier' => $tx->tier,
                        'granted_by' => 'admin_acc',
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return Response::buildSuccess(message: 'Pembayaran berhasil di-ACC! Akses kelas siswa telah aktif.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Tolak Pembayaran Siswa.
     */
    public function reject(string $id, Request $data): array
    {
        DB::beginTransaction();
        try {
            $reason = trim($data->input('reason') ?: 'Bukti transfer tidak valid atau dana tidak masuk.');

            $updated = DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'approved_by' => Auth::user()?->id,
                    'updated_at' => now(),
                ]);

            if (! $updated) {
                DB::rollback();

                return Response::buildErrorNotFound('Transaksi tidak ditemukan.');
            }

            DB::commit();

            return Response::buildSuccess(message: 'Pembayaran berhasil ditolak.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
