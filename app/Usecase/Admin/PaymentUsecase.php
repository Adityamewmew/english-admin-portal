<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentUsecase extends Usecase
{
    /**
     * Ambil semua daftar rekening transfer bank manual.
     */
    public function getPaymentMethods(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::PAYMENT_METHODS())
                ->whereNull('deleted_at')
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('bank_name', 'like', '%'.$keywords.'%')
                            ->orWhere('account_name', 'like', '%'.$keywords.'%')
                            ->orWhere('account_number', 'like', '%'.$keywords.'%');
                    });
                })
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc');

            $methods = $query->get();

            return Response::buildSuccess(['list' => $methods], ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil data rekening bank berdasarkan ID.
     */
    public function getPaymentMethodById(int $id): array
    {
        try {
            $data = DB::table(DatabaseConst::PAYMENT_METHODS())
                ->whereNull('deleted_at')
                ->where('id', $id)
                ->first();

            if (! $data) {
                return Response::buildErrorNotFound('Rekening pembayaran tidak ditemukan');
            }

            return Response::buildSuccess(collect($data)->toArray(), ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Buat rekening transfer manual baru.
     */
    public function createPaymentMethod(Request $data): array
    {
        $validator = Validator::make($data->all(), [
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return Response::buildError(ResponseConst::HTTP_BAD_REQUEST, $validator->errors()->first());
        }

        $bankLogo = null;
        if ($data->hasFile('bank_logo') && $data->file('bank_logo')->isValid()) {
            $file = $data->file('bank_logo');
            $ext = strtolower($file->guessExtension() ?: 'png');
            if (! in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'])) {
                $ext = 'png';
            }
            $filename = 'bank_'.time().'_'.uniqid().'.'.$ext;
            $dest = public_path('uploads/banks');
            if (! file_exists($dest)) {
                @mkdir($dest, 0755, true);
            }
            $file->move($dest, $filename);
            $bankLogo = '/uploads/banks/'.$filename;
        } elseif (is_string($data->input('bank_logo'))) {
            $bankLogo = $data->input('bank_logo');
        }

        DB::beginTransaction();
        try {
            DB::table(DatabaseConst::PAYMENT_METHODS())->insert([
                'bank_name' => trim($data->input('bank_name')),
                'account_name' => trim($data->input('account_name')),
                'account_number' => trim($data->input('account_number')),
                'bank_logo' => $bankLogo,
                'instructions' => $data->input('instructions'),
                'sort_order' => (int) ($data->input('sort_order') ?: 1),
                'is_active' => $data->has('is_active') ? (int) $data->input('is_active') : 1,
                'created_by' => Auth::user()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return Response::buildSuccessCreated(message: ResponseConst::SUCCESS_MESSAGE_CREATED);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Update data rekening transfer manual.
     */
    public function updatePaymentMethod(Request $data, int $id): array
    {
        $validator = Validator::make($data->all(), [
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'account_number' => 'required|string|max:50',
            'instructions' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return Response::buildError(ResponseConst::HTTP_BAD_REQUEST, $validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $payload = [
                'bank_name' => trim($data->input('bank_name')),
                'account_name' => trim($data->input('account_name')),
                'account_number' => trim($data->input('account_number')),
                'instructions' => $data->input('instructions'),
                'sort_order' => (int) ($data->input('sort_order') ?: 1),
                'is_active' => $data->has('is_active') ? (int) $data->input('is_active') : 1,
                'updated_by' => Auth::user()?->id,
                'updated_at' => now(),
            ];

            if ($data->hasFile('bank_logo') && $data->file('bank_logo')->isValid()) {
                $file = $data->file('bank_logo');
                $ext = strtolower($file->guessExtension() ?: 'png');
                if (! in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'])) {
                    $ext = 'png';
                }
                $filename = 'bank_'.time().'_'.uniqid().'.'.$ext;
                $dest = public_path('uploads/banks');
                if (! file_exists($dest)) {
                    @mkdir($dest, 0755, true);
                }
                $file->move($dest, $filename);
                $payload['bank_logo'] = '/uploads/banks/'.$filename;
            }

            $updated = DB::table(DatabaseConst::PAYMENT_METHODS())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update($payload);

            if (! $updated) {
                DB::rollback();

                return Response::buildErrorNotFound('Rekening tidak ditemukan atau gagal diperbarui');
            }

            DB::commit();

            return Response::buildSuccess(message: ResponseConst::SUCCESS_MESSAGE_UPDATED);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Soft delete rekening transfer bank manual.
     */
    public function deletePaymentMethod(int $id): array
    {
        DB::beginTransaction();
        try {
            $deleted = DB::table(DatabaseConst::PAYMENT_METHODS())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'deleted_by' => Auth::user()?->id,
                    'deleted_at' => now(),
                ]);

            if (! $deleted) {
                DB::rollback();

                return Response::buildErrorNotFound('Rekening tidak ditemukan');
            }

            DB::commit();

            return Response::buildSuccess(message: ResponseConst::SUCCESS_MESSAGE_DELETED);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Toggle status aktif / nonaktif rekening bank.
     */
    public function toggleStatusPaymentMethod(int $id): array
    {
        DB::beginTransaction();
        try {
            $method = DB::table(DatabaseConst::PAYMENT_METHODS())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (! $method) {
                DB::rollback();

                return Response::buildErrorNotFound('Rekening tidak ditemukan');
            }

            $newStatus = $method->is_active ? 0 : 1;

            DB::table(DatabaseConst::PAYMENT_METHODS())
                ->where('id', $id)
                ->update([
                    'is_active' => $newStatus,
                    'updated_by' => Auth::user()?->id,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return Response::buildSuccess(message: 'Status rekening berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil semua konfigurasi pembayaran (WhatsApp & General).
     */
    public function getPaymentSettings(): array
    {
        try {
            $settings = DB::table(DatabaseConst::PAYMENT_SETTINGS())->get();
            $map = [];
            foreach ($settings as $s) {
                $map[$s->key] = $s->value;
            }

            // Fallback default keys if empty
            $defaults = [
                'whatsapp_number' => $map['whatsapp_number'] ?? '6281234567890',
                'whatsapp_name' => $map['whatsapp_name'] ?? 'Admin Support EDDY\'S AI',
                'whatsapp_message' => $map['whatsapp_message'] ?? 'Halo Admin EDDY\'S AI, saya ingin konfirmasi pembayaran untuk aktivasi kelas.',
                'whatsapp_is_active' => $map['whatsapp_is_active'] ?? '1',
                'singapay_is_sandbox' => $map['singapay_is_sandbox'] ?? (env('SINGAPAY_ENVIRONMENT') === 'production' ? '0' : '1'),
                'payment_expired_hours' => $map['payment_expired_hours'] ?? '24',
            ];

            return Response::buildSuccess($defaults, ResponseConst::HTTP_SUCCESS);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Update konfigurasi pembayaran (WhatsApp & Gateway SingaPay).
     */
    public function updatePaymentSettings(Request $data): array
    {
        $settingType = $data->input('setting_type', 'whatsapp');

        DB::beginTransaction();
        try {
            if ($settingType === 'gateway') {
                $keys = [
                    'singapay_is_sandbox' => $data->has('singapay_is_sandbox') ? '1' : '0',
                    'payment_expired_hours' => (string) ($data->input('payment_expired_hours') ?: '24'),
                ];
            } else {
                $cleanNumber = preg_replace('/[^0-9]/', '', $data->input('whatsapp_number'));
                if (str_starts_with($cleanNumber, '08')) {
                    $cleanNumber = '628'.substr($cleanNumber, 2);
                }

                $keys = [
                    'whatsapp_number' => $cleanNumber,
                    'whatsapp_name' => trim($data->input('whatsapp_name')),
                    'whatsapp_message' => trim($data->input('whatsapp_message')),
                    'whatsapp_is_active' => $data->has('whatsapp_is_active') ? (string) $data->input('whatsapp_is_active') : '1',
                ];
            }

            foreach ($keys as $k => $v) {
                DB::table(DatabaseConst::PAYMENT_SETTINGS())->updateOrInsert(
                    ['key' => $k],
                    ['value' => $v, 'updated_at' => now()]
                );
            }

            DB::commit();

            return Response::buildSuccess(message: 'Pengaturan pembayaran berhasil disimpan');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage(), ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
