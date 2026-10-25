<?php

namespace App\Http\Controllers\Admin;

use App\Constants\DatabaseConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\CourseClassUsecase;
use App\Usecase\Admin\PaymentTransactionUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseClassController extends Controller
{
    protected array $page = [
        'route' => 'classes',
        'title' => 'Manajemen Kelas & Tarif',
    ];

    public function __construct(
        protected CourseClassUsecase $usecase,
        protected PaymentTransactionUsecase $transactionUsecase,
    ) {}

    public function index(Request $request): View|Response
    {
        $activeTab = $request->get('tab', 'classes');
        $cefr = $request->get('cefr', 'all');
        $keywords = $request->get('keywords');

        $classesData = $this->usecase->getClasses();
        $classes = $classesData['data']['classes'] ?? [];

        $accessList = [];
        $totalAccesses = 0;
        $students = [];

        if ($activeTab === 'access') {
            $accessData = $this->usecase->getStudentAccesses([
                'cefr' => $cefr,
                'keywords' => $keywords,
            ]);
            $accessList = $accessData['data']['list'] ?? [];
            $totalAccesses = $accessData['data']['total_accesses'] ?? 0;
            $students = $this->usecase->getStudentsForDropdown();
        } else {
            $totalAccesses = Cache::remember('user_class_access_total', 60, function () {
                return DB::table(DatabaseConst::USER_CLASS_ACCESS())->count();
            });
        }

        return view('_admin.classes.index', [
            'page' => $this->page,
            'activeTab' => $activeTab,
            'classes' => $classes,
            'accessList' => $accessList,
            'totalAccesses' => $totalAccesses,
            'cefr' => $cefr,
            'keywords' => $keywords,
            'students' => $students,
        ]);
    }

    public function add(): View
    {
        return view('_admin.classes.add', [
            'page' => $this->page,
        ]);
    }

    public function doCreate(Request $request): RedirectResponse
    {
        $request->validate([
            'cefr' => 'required|string|in:A1,A2,B1,B2,C1,C2',
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'cefr.required' => 'Pilih kategori level CEFR.',
            'name.required' => 'Nama paket kelas wajib diisi.',
            'price.required' => 'Tarif kelas wajib diisi.',
        ]);

        $cefr = strtoupper($request->input('cefr'));
        $data = [
            'cefr' => $cefr,
            'name' => $request->input('name'),
            'price' => (int) $request->input('price'),
            'original_price' => $request->filled('original_price') ? (int) $request->input('original_price') : null,
            'description' => $request->input('description'),
            'features' => $request->input('features'),
            'is_active' => $request->boolean('is_active', true),
        ];

        $res = $this->usecase->createClass($data);

        if (! $res['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $res['message'] ?? 'Gagal membuat paket kelas.');
        }

        return redirect()->route('admin.classes.index')
            ->with('success', "Paket kelas CEFR {$cefr} berhasil ditambahkan.");
    }

    public function edit(string $cefr): View|RedirectResponse
    {
        $res = $this->usecase->getClassByCefr($cefr);
        $class = $res['data']['class'] ?? null;

        if (! $class) {
            return redirect()->route('admin.classes.index')
                ->with('error', 'Data kelas tidak ditemukan.');
        }

        return view('_admin.classes.edit', [
            'page' => $this->page,
            'class' => $class,
            'cefr' => strtoupper($cefr),
        ]);
    }

    public function doUpdate(Request $request, string $cefr): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $price = (int) ($request->input('tier_plans.pro.price') ?? $request->input('price', 0));
        $originalPrice = $request->filled('tier_plans.pro.original_price')
            ? (int) $request->input('tier_plans.pro.original_price')
            : ($request->filled('original_price') ? (int) $request->input('original_price') : null);

        $data = [
            'name' => $request->input('name'),
            'price' => $price,
            'original_price' => $originalPrice,
            'description' => $request->input('description'),
            'features' => $request->input('features'),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->has('tier_plans')) {
            $data['tier_plans'] = $request->input('tier_plans');
        }

        $res = $this->usecase->updateClass($cefr, $data);

        if (! $res['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $res['message'] ?? 'Gagal memperbarui paket kelas.');
        }

        return redirect()->route('admin.classes.index')
            ->with('success', "Paket kelas CEFR {$cefr} berhasil diperbarui.");
    }

    public function delete(string $cefr): RedirectResponse
    {
        $res = $this->usecase->deleteClass($cefr);

        if (! $res['success']) {
            return redirect()->back()
                ->with('error', $res['message'] ?? 'Gagal menghapus paket kelas.');
        }

        return redirect()->route('admin.classes.index')
            ->with('success', "Paket kelas CEFR {$cefr} berhasil dihapus.");
    }

    public function doGrantAccess(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|string',
            'cefr' => 'required|string|in:A1,A2,B1,B2,C1,C2',
            'tier' => 'nullable|string|in:basic,pro,ultra',
        ], [
            'user_id.required' => 'Pilih siswa penerima akses.',
            'cefr.required' => 'Pilih level kelas yang dibuka.',
        ]);

        $res = $this->usecase->grantManualAccess(
            $request->input('user_id'),
            $request->input('cefr'),
            $request->input('tier', 'basic')
        );

        if (! $res['success']) {
            return redirect()->back()
                ->with('error', $res['message'] ?? 'Gagal memberikan akses kelas.');
        }

        return redirect()->route('admin.classes.index', ['tab' => 'access'])
            ->with('success', $res['message'] ?? 'Akses kelas berhasil diberikan.');
    }

    public function doRevokeAccess(string $id): RedirectResponse
    {
        $res = $this->usecase->revokeAccess($id);

        if (! $res['success']) {
            return redirect()->back()
                ->with('error', $res['message'] ?? 'Gagal mencabut akses kelas.');
        }

        return redirect()->route('admin.classes.index', ['tab' => 'access'])
            ->with('success', 'Akses kelas siswa berhasil dicabut.');
    }

    /**
     * Konfirmasi Pembayaran Siswa via Transfer Manual (Buka Akses Kelas).
     */
    public function confirmations(Request $request): View|Response
    {
        $status = $request->get('status', 'verification');
        $keywords = $request->get('keywords');

        $filter = [
            'category' => 'manual_transfer',
            'keywords' => $keywords,
        ];
        if ($status !== 'all') {
            $filter['status'] = $status;
        }

        $res = $this->transactionUsecase->getAll($filter);
        $list = $res['data']['list'] ?? [];

        $counts = [
            'verification' => DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->whereNull('deleted_at')
                ->where('payment_category', 'manual_transfer')
                ->whereIn('status', ['waiting_approval', 'verification'])
                ->count(),
            'pending' => DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->whereNull('deleted_at')
                ->where('payment_category', 'manual_transfer')
                ->where('status', 'pending')
                ->count(),
            'paid' => DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->whereNull('deleted_at')
                ->where('payment_category', 'manual_transfer')
                ->where('status', 'paid')
                ->count(),
            'all' => DB::table(DatabaseConst::PAYMENT_TRANSACTIONS())
                ->whereNull('deleted_at')
                ->where('payment_category', 'manual_transfer')
                ->count(),
        ];

        return view('_admin.classes.confirmations', [
            'page' => [
                'route' => 'classes.confirmations',
                'title' => 'Konfirmasi Akses Kelas',
            ],
            'data' => $list,
            'status' => $status,
            'keywords' => $keywords,
            'counts' => $counts,
        ]);
    }
}
