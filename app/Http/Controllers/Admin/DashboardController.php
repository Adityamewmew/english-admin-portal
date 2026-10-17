<?php

namespace App\Http\Controllers\Admin;

use App\Constants\DatabaseConst;
use App\Constants\UserConst;
use App\Http\Controllers\Controller;
use App\Usecase\Admin\SidebarMenuUsecase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        protected SidebarMenuUsecase $sidebarMenuUsecase
    ) {}

    public function index(): View|Response
    {
        $user = Auth::user();
        $accessType = (int) ($user?->access_type ?? UserConst::SUPERADMIN);

        $modules = $this->sidebarMenuUsecase->getDashboardModules(
            accessType: $accessType
        );

        $allowedRoutes = [
            'admin.users.index',
            'admin.modules.index',
            'admin.questions.index',
            'admin.sidebar_menu.index',
        ];

        $modules = collect($modules['data'] ?? [])
            ->filter(fn ($menu) => in_array($menu->route_name, $allowedRoutes, true))
            ->values()
            ->all();

        // EDDY'S AI Metrics
        $totalStudents = DB::table(DatabaseConst::USER())
            ->whereNull('deleted_at')
            ->where('access_type', UserConst::STUDENT)
            ->count();

        $totalAdmins = DB::table(DatabaseConst::USER())
            ->whereNull('deleted_at')
            ->where('access_type', UserConst::SUPERADMIN)
            ->count();

        $totalModules = DB::table(DatabaseConst::CURRICULUM_MODULES())
            ->whereNull('deleted_at')
            ->count();

        $totalQuestions = DB::table(DatabaseConst::ITEM_BANK())
            ->whereNull('deleted_at')
            ->count();

        $totalCompleted = DB::table(DatabaseConst::USER_MODULE_PROGRESS())
            ->where('status', 'completed')
            ->count();

        $recentStudents = DB::table(DatabaseConst::USER())
            ->whereNull('deleted_at')
            ->where('access_type', UserConst::STUDENT)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $cefrCounts = DB::table(DatabaseConst::USER())
            ->select('current_cefr', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('current_cefr')
            ->pluck('total', 'current_cefr')
            ->toArray();

        $cefrDistribution = [
            'A1' => $cefrCounts['A1'] ?? 0,
            'A2' => $cefrCounts['A2'] ?? 0,
            'B1' => $cefrCounts['B1'] ?? 0,
            'B2' => $cefrCounts['B2'] ?? 0,
            'C1' => $cefrCounts['C1'] ?? 0,
            'C2' => $cefrCounts['C2'] ?? 0,
        ];

        return view('_admin.dashboard', [
            'modules' => $modules,
            'stats' => [
                'totalStudents' => $totalStudents,
                'totalAdmins' => $totalAdmins,
                'totalModules' => $totalModules,
                'totalQuestions' => $totalQuestions,
                'totalCompleted' => $totalCompleted,
                'recentStudents' => $recentStudents,
                'cefrDistribution' => $cefrDistribution,
            ],
        ]);
    }
}
