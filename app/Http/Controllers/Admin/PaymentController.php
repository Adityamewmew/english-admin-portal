<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ResponseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\PaymentUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    protected array $page = [
        'route' => 'payment',
        'title' => 'Pengaturan Pembayaran',
    ];

    protected string $baseRedirect;

    public function __construct(
        protected PaymentUsecase $usecase
    ) {
        $this->baseRedirect = 'admin/'.$this->page['route'];
    }

    /**
     * Tampilkan halaman utama manajemen pembayaran (Tab Bank Transfer & Tab Hubungi Admin).
     */
    public function index(Request $request): View|Response
    {
        $activeTab = $request->get('tab', 'banks');
        $keywords = $request->get('keywords');

        $methodsData = $this->usecase->getPaymentMethods([
            'keywords' => $keywords,
        ]);
        $methods = $methodsData['data']['list'] ?? [];

        $settingsData = $this->usecase->getPaymentSettings();
        $settings = $settingsData['data'] ?? [];

        return view('_admin.payment.index', [
            'page' => $this->page,
            'activeTab' => $activeTab,
            'keywords' => $keywords,
            'methods' => $methods,
            'settings' => (object) $settings,
        ]);
    }

    /**
     * Tampilkan form tambah rekening transfer bank.
     */
    public function add(): View
    {
        return view('_admin.payment.add', [
            'page' => $this->page,
        ]);
    }

    /**
     * Proses penyimpanan rekening bank baru.
     */
    public function doCreate(Request $request): RedirectResponse
    {
        $process = $this->usecase->createPaymentMethod(data: $request);

        if ($process['success']) {
            return redirect()->route('admin.payment.index', ['tab' => 'banks'])
                ->with('success', ResponseConst::SUCCESS_MESSAGE_CREATED);
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Tampilkan form edit rekening bank.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $data = $this->usecase->getPaymentMethodById($id);

        if (empty($data['data'])) {
            return redirect()->route('admin.payment.index', ['tab' => 'banks'])
                ->with('error', $data['message'] ?? 'Rekening tidak ditemukan');
        }

        return view('_admin.payment.edit', [
            'page' => $this->page,
            'data' => (object) $data['data'],
        ]);
    }

    /**
     * Proses update rekening bank.
     */
    public function doUpdate(Request $request, int $id): RedirectResponse
    {
        $process = $this->usecase->updatePaymentMethod(data: $request, id: $id);

        if ($process['success']) {
            return redirect()->route('admin.payment.index', ['tab' => 'banks'])
                ->with('success', ResponseConst::SUCCESS_MESSAGE_UPDATED);
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Hapus rekening bank (soft delete).
     */
    public function delete(int $id): RedirectResponse
    {
        $process = $this->usecase->deletePaymentMethod($id);

        if ($process['success']) {
            return redirect()->route('admin.payment.index', ['tab' => 'banks'])
                ->with('success', ResponseConst::SUCCESS_MESSAGE_DELETED);
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Toggle status aktif/nonaktif rekening bank.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $process = $this->usecase->toggleStatusPaymentMethod($id);

        if ($process['success']) {
            return redirect()->route('admin.payment.index', ['tab' => 'banks'])
                ->with('success', $process['message'] ?? 'Status berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }

    /**
     * Simpan pengaturan kontak WhatsApp Admin.
     */
    public function doUpdateSettings(Request $request): RedirectResponse
    {
        $process = $this->usecase->updatePaymentSettings(data: $request);

        if ($process['success']) {
            return redirect()->route('admin.payment.index', ['tab' => 'whatsapp'])
                ->with('success', 'Pengaturan kontak WhatsApp Admin berhasil disimpan');
        }

        return redirect()->back()->withInput()
            ->with('error', $process['message'] ?? ResponseConst::DEFAULT_ERROR_MESSAGE);
    }
}
