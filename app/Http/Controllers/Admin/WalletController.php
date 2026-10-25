<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\WalletUsecase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    protected array $page = [
        'route' => 'wallet',
        'title' => 'Saldo & Penarikan Dana',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected WalletUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    /**
     * Tampilkan menu Saldo pengguna (kartu saldo, rekening bank, mutasi)
     */
    public function index(Request $request): View|Response
    {
        $userId = Auth::user()->id;

        $summaryData = $this->usecase->getSummary($userId);
        $summary = $summaryData['data'] ?? [
            'balance' => 0,
            'total_income' => 0,
            'total_withdraw' => 0,
            'pending_withdraw' => 0,
        ];

        $transData = $this->usecase->getTransactions($userId, [
            'type' => $request->get('type'),
            'per_page' => 10,
        ]);
        $transactions = $transData['data']['list'] ?? [];

        $banksData = $this->usecase->getBankAccounts($userId);
        $bankAccounts = $banksData['data']['list'] ?? [];

        return view('_admin.wallet.index', [
            'page' => $this->page,
            'summary' => (object) $summary,
            'transactions' => $transactions,
            'bankAccounts' => $bankAccounts,
            'currentType' => $request->get('type'),
        ]);
    }

    /**
     * Simpan rekening bank baru
     */
    public function doAddBankAccount(Request $request): RedirectResponse
    {
        $userId = Auth::user()->id;
        $process = $this->usecase->addBankAccount($userId, $request);

        if ($process['success']) {
            return redirect()->route('admin.wallet.index')
                ->with('success', 'Rekening bank berhasil disimpan');
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Hapus rekening bank
     */
    public function doDeleteBankAccount(string $id): RedirectResponse
    {
        $userId = Auth::user()->id;
        $process = $this->usecase->deleteBankAccount($userId, $id);

        if ($process['success']) {
            return redirect()->route('admin.wallet.index')
                ->with('success', 'Rekening bank berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Proses pengajuan tarik saldo (Withdrawal)
     */
    public function doRequestWithdraw(Request $request): RedirectResponse
    {
        $userId = Auth::user()->id;
        $process = $this->usecase->requestWithdraw($userId, $request);

        if ($process['success']) {
            return redirect()->route('admin.wallet.index')
                ->with('success', 'Permintaan penarikan saldo berhasil diajukan dan sedang diproses');
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Tampilkan antrean penarikan dana untuk Admin / Finance
     */
    public function withdrawalsIndex(Request $request): View|Response
    {
        $page = [
            'route' => 'withdrawals',
            'title' => 'Kelola Penarikan Dana (Withdrawals)',
        ];

        $data = $this->usecase->getAllWithdrawals([
            'status' => $request->get('status'),
            'keywords' => $request->get('keywords'),
            'per_page' => 15,
        ]);
        $withdrawals = $data['data']['list'] ?? [];

        return view('_admin.wallet.withdrawals', [
            'page' => $page,
            'withdrawals' => $withdrawals,
            'currentStatus' => $request->get('status'),
            'keywords' => $request->get('keywords'),
        ]);
    }

    /**
     * Setujui penarikan dana & eksekusi SingaPay disburse
     */
    public function doApproveWithdrawal(string $id): RedirectResponse
    {
        $adminId = Auth::user()->id;
        $process = $this->usecase->approveWithdrawal($id, $adminId);

        if ($process['success']) {
            return redirect()->route('admin.wallet.withdrawals')
                ->with('success', $process['message'] ?? 'Penarikan dana berhasil disetujui');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Tolak penarikan dana & refund saldo
     */
    public function doRejectWithdrawal(Request $request, string $id): RedirectResponse
    {
        $adminId = Auth::user()->id;
        $reason = $request->get('reason', 'Ditolak oleh admin');
        $process = $this->usecase->rejectWithdrawal($id, $adminId, $reason);

        if ($process['success']) {
            return redirect()->route('admin.wallet.withdrawals')
                ->with('success', $process['message'] ?? 'Penarikan berhasil ditolak dan saldo dikembalikan');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }
}
