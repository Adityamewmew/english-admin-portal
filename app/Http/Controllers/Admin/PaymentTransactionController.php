<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\PaymentTransactionUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentTransactionController extends Controller
{
    protected array $page = [
        'route' => 'payment_transactions',
        'title' => 'Transaksi & Konfirmasi',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected PaymentTransactionUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/payment-transactions';
    }

    /**
     * Tampilkan daftar transaksi pembayaran dengan filter.
     */
    public function index(Request $request): View|Response
    {
        $data = $this->usecase->getAll([
            'keywords' => $request->get('keywords'),
            'status' => $request->get('status'),
            'category' => $request->get('category'),
        ]);

        $list = $data['data']['list'] ?? [];

        return view('_admin.payment_transactions.index', [
            'page' => $this->page,
            'data' => $list,
            'keywords' => $request->get('keywords'),
            'status' => $request->get('status'),
            'category' => $request->get('category'),
        ]);
    }

    /**
     * Rincian transaksi dan bukti transfer.
     */
    public function detail(string $id): View|RedirectResponse
    {
        $data = $this->usecase->getByID(id: $id);

        if (empty($data['data'])) {
            return redirect()->route('admin.payment_transactions.index')
                ->with('error', $data['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
        }

        return view('_admin.payment_transactions.detail', [
            'page' => $this->page,
            'data' => (object) $data['data'],
        ]);
    }

    /**
     * ACC Pembayaran Siswa.
     */
    public function doApprove(string $id): RedirectResponse
    {
        $process = $this->usecase->approve(id: $id);

        if ($process['success']) {
            return redirect()->back()
                ->with('success', $process['message'] ?? 'Pembayaran berhasil di-ACC!');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Tolak Pembayaran Siswa.
     */
    public function doReject(Request $request, string $id): RedirectResponse
    {
        $process = $this->usecase->reject(id: $id, data: $request);

        if ($process['success']) {
            return redirect()->back()
                ->with('success', $process['message'] ?? 'Pembayaran berhasil ditolak.');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }
}
