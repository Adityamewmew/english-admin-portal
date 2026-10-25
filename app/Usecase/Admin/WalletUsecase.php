<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Http\Presenter\Response;
use App\Models\User;
use App\Services\SingapayDisbursementService;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletUsecase extends Usecase
{
    public function __construct(
        protected SingapayDisbursementService $singapayService
    ) {}

    /**
     * Ambil ringkasan saldo pengguna
     */
    public function getSummary(string $userId): array
    {
        try {
            $user = User::find($userId);
            $balance = $user ? (float) $user->balanceInt : 0;

            $totalIncome = (float) DB::table(DatabaseConst::WALLET_TRANSACTIONS())
                ->where('payable_id', $userId)
                ->where('type', 'deposit')
                ->where('confirmed', true)
                ->sum('amount');

            $withdrawStats = DB::table(DatabaseConst::WITHDRAWALS())
                ->where('user_id', $userId)
                ->selectRaw('
                    COALESCE(SUM(CASE WHEN status = \'success\' THEN amount ELSE 0 END), 0) as total_withdraw,
                    COALESCE(SUM(CASE WHEN status IN (\'pending\', \'approved\', \'processing\') THEN total_debit ELSE 0 END), 0) as pending_withdraw
                ')
                ->first();

            $totalWithdraw = (float) ($withdrawStats->total_withdraw ?? 0);
            $pendingWithdraw = (float) ($withdrawStats->pending_withdraw ?? 0);

            return Response::buildSuccess([
                'balance' => $balance,
                'total_income' => $totalIncome,
                'total_withdraw' => $totalWithdraw,
                'pending_withdraw' => $pendingWithdraw,
            ]);
        } catch (Exception $e) {
            Log::error('WalletUsecase::getSummary error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil riwayat transaksi wallet
     */
    public function getTransactions(string $userId, array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::WALLET_TRANSACTIONS().' as t')
                ->select('t.*')
                ->where('t.payable_id', $userId)
                ->when($filterData['type'] ?? false, function ($q, $type) {
                    return $q->where('t.type', $type);
                })
                ->orderBy('t.created_at', 'desc');

            $perPage = $filterData['per_page'] ?? 15;
            $data = $query->paginate($perPage);

            return Response::buildSuccess(['list' => $data]);
        } catch (Exception $e) {
            Log::error('WalletUsecase::getTransactions error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil daftar rekening bank tersimpan
     */
    public function getBankAccounts(string $userId): array
    {
        try {
            $list = DB::table(DatabaseConst::USER_BANK_ACCOUNTS())
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return Response::buildSuccess(['list' => $list]);
        } catch (Exception $e) {
            Log::error('WalletUsecase::getBankAccounts error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Tambah rekening bank pengguna
     */
    public function addBankAccount(string $userId, Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'bank_code' => 'required|string',
            'bank_name' => 'required|string',
            'account_number' => 'required|string|min:5|max:30',
            'account_name' => 'required|string|min:3|max:100',
        ]);

        if ($validator->fails()) {
            return Response::buildError(422, $validator->errors()->first());
        }

        try {
            $hasExisting = DB::table(DatabaseConst::USER_BANK_ACCOUNTS())
                ->where('user_id', $userId)
                ->whereNull('deleted_at')
                ->exists();

            $id = (string) Str::uuid();
            DB::table(DatabaseConst::USER_BANK_ACCOUNTS())->insert([
                'id' => $id,
                'user_id' => $userId,
                'bank_code' => $request->get('bank_code'),
                'bank_name' => $request->get('bank_name'),
                'account_number' => $request->get('account_number'),
                'account_name' => strtoupper($request->get('account_name')),
                'is_primary' => ! $hasExisting,
                'is_verified' => true,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return Response::buildSuccessCreated(['id' => $id]);
        } catch (Exception $e) {
            Log::error('WalletUsecase::addBankAccount error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Hapus rekening bank
     */
    public function deleteBankAccount(string $userId, string $id): array
    {
        try {
            DB::table(DatabaseConst::USER_BANK_ACCOUNTS())
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $userId,
                ]);

            return Response::buildSuccess(message: 'Rekening bank berhasil dihapus');
        } catch (Exception $e) {
            Log::error('WalletUsecase::deleteBankAccount error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ajukan penarikan dana (Withdrawal)
     */
    public function requestWithdraw(string $userId, Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|string',
            'amount' => 'required|numeric|min:10000',
        ]);

        if ($validator->fails()) {
            return Response::buildError(422, $validator->errors()->first());
        }

        $bankAccount = DB::table(DatabaseConst::USER_BANK_ACCOUNTS())
            ->where('id', $request->get('bank_account_id'))
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->first();

        if (! $bankAccount) {
            return Response::buildError(404, 'Rekening bank tidak ditemukan atau tidak valid');
        }

        $amount = (float) $request->get('amount');
        $adminFee = 2500; // Flat fee penarikan
        $totalDebit = $amount + $adminFee;

        $user = User::find($userId);
        if (! $user) {
            return Response::buildErrorNotFound('Pengguna tidak ditemukan');
        }

        if ($user->balanceInt < $totalDebit) {
            return Response::buildError(422, 'Saldo Anda tidak mencukupi untuk penarikan ini (termasuk biaya admin Rp '.number_format($adminFee, 0, ',', '.').')');
        }

        DB::beginTransaction();
        try {
            $withdrawalId = (string) Str::uuid();
            $referenceNo = 'WD-'.date('Ymd').'-'.strtoupper(Str::random(6));

            // Potong saldo pengguna dengan bavix wallet (thread-safe locking)
            $user->withdraw($totalDebit, [
                'type' => 'withdrawal',
                'reference_no' => $referenceNo,
                'withdrawal_id' => $withdrawalId,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'bank_name' => $bankAccount->bank_name,
                'account_number' => $bankAccount->account_number,
                'account_name' => $bankAccount->account_name,
            ]);

            DB::table(DatabaseConst::WITHDRAWALS())->insert([
                'id' => $withdrawalId,
                'user_id' => $userId,
                'bank_account_id' => $bankAccount->id,
                'reference_no' => $referenceNo,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'total_debit' => $totalDebit,
                'singapay_cost' => 1200,
                'status' => 'pending',
                'notes' => $request->get('notes'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return Response::buildSuccessCreated([
                'withdrawal_id' => $withdrawalId,
                'reference_no' => $referenceNo,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'total_debit' => $totalDebit,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('WalletUsecase::requestWithdraw error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Ambil semua daftar penarikan (Untuk halaman Finance/Admin)
     */
    public function getAllWithdrawals(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::WITHDRAWALS().' as w')
                ->leftJoin(DatabaseConst::USER().' as u', 'w.user_id', '=', 'u.id')
                ->leftJoin(DatabaseConst::USER_BANK_ACCOUNTS().' as b', 'w.bank_account_id', '=', 'b.id')
                ->select(
                    'w.*',
                    'u.name as user_name',
                    'u.email as user_email',
                    'b.bank_name',
                    'b.bank_code',
                    'b.account_number',
                    'b.account_name'
                )
                ->when($filterData['status'] ?? false, function ($q, $status) {
                    return $q->where('w.status', $status);
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('w.reference_no', 'like', '%'.$keywords.'%')
                            ->orWhere('u.name', 'like', '%'.$keywords.'%')
                            ->orWhere('b.account_number', 'like', '%'.$keywords.'%');
                    });
                })
                ->orderBy('w.created_at', 'desc');

            $data = $query->paginate($filterData['per_page'] ?? 15);

            return Response::buildSuccess(['list' => $data]);
        } catch (Exception $e) {
            Log::error('WalletUsecase::getAllWithdrawals error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Setujui dan eksekusi transfer SingaPay
     */
    public function approveWithdrawal(string $withdrawalId, string $adminId): array
    {
        $withdrawal = DB::table(DatabaseConst::WITHDRAWALS().' as w')
            ->leftJoin(DatabaseConst::USER_BANK_ACCOUNTS().' as b', 'w.bank_account_id', '=', 'b.id')
            ->select('w.*', 'b.bank_code', 'b.account_number', 'b.account_name')
            ->where('w.id', $withdrawalId)
            ->first();

        if (! $withdrawal) {
            return Response::buildErrorNotFound('Data penarikan tidak ditemukan');
        }

        if ($withdrawal->status !== 'pending') {
            return Response::buildError(422, 'Status penarikan sudah '.$withdrawal->status);
        }

        // Panggil API SingaPay Send Money / Disburse
        $disburseRes = $this->singapayService->disburse([
            'account_number' => $withdrawal->account_number,
            'account_name' => $withdrawal->account_name,
            'bank_code' => $withdrawal->bank_code,
            'amount' => $withdrawal->amount,
            'reference_no' => $withdrawal->reference_no,
            'notes' => 'Penarikan '.$withdrawal->reference_no,
        ]);

        if (! $disburseRes['success']) {
            return Response::buildError(400, 'SingaPay Disburse Error: '.($disburseRes['message'] ?? 'Gagal memproses ke SingaPay'));
        }

        $trxId = $disburseRes['transaction_id'] ?? null;
        $status = in_array($disburseRes['status'] ?? '', ['success', 'COMPLETED']) ? 'success' : 'processing';

        DB::table(DatabaseConst::WITHDRAWALS())
            ->where('id', $withdrawalId)
            ->update([
                'singapay_trx_id' => $trxId,
                'status' => $status,
                'approved_by' => $adminId,
                'approved_at' => now(),
                'updated_at' => now(),
            ]);

        return Response::buildSuccess(message: 'Penarikan berhasil disetujui dan diteruskan ke SingaPay');
    }

    /**
     * Tolak penarikan dana dan kembalikan saldo ke dompet pengguna
     */
    public function rejectWithdrawal(string $withdrawalId, string $adminId, ?string $reason = null): array
    {
        $withdrawal = DB::table(DatabaseConst::WITHDRAWALS())
            ->where('id', $withdrawalId)
            ->first();

        if (! $withdrawal) {
            return Response::buildErrorNotFound('Data penarikan tidak ditemukan');
        }

        if ($withdrawal->status !== 'pending') {
            return Response::buildError(422, 'Penarikan sudah diproses dan tidak dapat ditolak');
        }

        $user = User::find($withdrawal->user_id);
        if (! $user) {
            return Response::buildErrorNotFound('Pengguna tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            // Kembalikan saldo pengguna
            $user->deposit($withdrawal->total_debit, [
                'type' => 'withdrawal_refund',
                'reference_no' => $withdrawal->reference_no,
                'withdrawal_id' => $withdrawal->id,
                'reason' => $reason ?? 'Penarikan ditolak oleh admin',
            ]);

            DB::table(DatabaseConst::WITHDRAWALS())
                ->where('id', $withdrawalId)
                ->update([
                    'status' => 'rejected',
                    'failure_reason' => $reason ?? 'Ditolak oleh admin',
                    'approved_by' => $adminId,
                    'approved_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            return Response::buildSuccess(message: 'Penarikan berhasil ditolak dan saldo telah dikembalikan ke dompet pengguna');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('WalletUsecase::rejectWithdrawal error: '.$e->getMessage());

            return Response::buildErrorService($e->getMessage());
        }
    }
}
