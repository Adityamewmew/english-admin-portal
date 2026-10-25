<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\PaymentTransactionUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

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
        $status = $request->get('status', 'paid');
        $keywords = $request->get('keywords');
        $category = $request->get('category');
        $page = (int) $request->get('page', 1);

        $filter = [
            'keywords' => $keywords,
            'category' => $category,
            'exclude_unapproved_manual' => true,
        ];

        if ($status !== 'all' && ! empty($status)) {
            $filter['status'] = $status;
        }

        // Cache default landing page (page 1, status paid, no filter) for 20 seconds
        $isDefaultView = empty($keywords) && empty($category) && $status === 'paid' && $page === 1;

        if ($isDefaultView) {
            $data = Cache::remember('payment_tx_index_paid_p1', 20, fn () => $this->usecase->getAll($filter));
        } else {
            $data = $this->usecase->getAll($filter);
        }

        $list = $data['data']['list'] ?? [];

        return view('_admin.payment_transactions.index', [
            'page' => $this->page,
            'data' => $list,
            'keywords' => $keywords,
            'status' => $status,
            'category' => $category,
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
