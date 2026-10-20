<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Constants\UserConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GradebookUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
            $studentAccess = UserConst::STUDENT;

            $query = DB::table(DatabaseConst::USER().' as u')
                ->whereNull('u.deleted_at')
                ->where('u.access_type', $studentAccess)
                ->select([
                    'u.id',
                    'u.name',
                    'u.email',
                    'u.current_cefr',
                    'u.created_at',
                    DB::raw('(SELECT COUNT(*) FROM '.DatabaseConst::USER_MODULE_PROGRESS().' WHERE user_id = u.id AND status = \'completed\') as completed_modules_count'),
                    DB::raw('(SELECT ROUND(AVG(score)) FROM '.DatabaseConst::USER_MODULE_PROGRESS().' WHERE user_id = u.id AND status = \'completed\' AND score > 0) as avg_score'),
                    DB::raw('(SELECT COUNT(*) FROM '.DatabaseConst::USER_MODULE_PROGRESS().' as ump JOIN '.DatabaseConst::CURRICULUM_MODULES().' as cm ON ump.module_id = cm.id WHERE ump.user_id = u.id AND ump.status = \'completed\' AND (cm.is_exam = true OR cm.id LIKE \'%-M13\') AND ump.score >= cm.passing_score) as passed_exams_count'),
                    DB::raw('(SELECT COUNT(*) FROM '.DatabaseConst::USER_SUB_MODULE_PROGRESS().' as usmp JOIN '.DatabaseConst::SUB_MODULES().' as sm ON usmp.sub_module_id = sm.id WHERE usmp.user_id = u.id AND usmp.status = \'completed\' AND (sm.skill_type = \'assessment\' OR sm.skill_type = \'exam\')) as completed_assessments_count'),
                    DB::raw('(SELECT MAX(completed_at) FROM '.DatabaseConst::USER_MODULE_PROGRESS().' WHERE user_id = u.id) as last_completed_at'),
                ])
                ->when($filterData['level'] ?? false, function ($q, $level) {
                    if ($level !== 'all') {
                        return $q->where('u.current_cefr', 'ilike', $level.'%');
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('u.name', 'ilike', '%'.$keywords.'%')
                            ->orWhere('u.email', 'ilike', '%'.$keywords.'%');
                    });
                })
                ->orderBy('u.created_at', 'desc');

            $data = empty($filterData['no_pagination'])
                ? $query->paginate(20)
                : $query->get();

            if (! empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            // Summary stats for KPI cards
            $totalStudents = DB::table(DatabaseConst::USER())
                ->whereNull('deleted_at')
                ->where('access_type', $studentAccess)
                ->count();

            $totalCompleted = DB::table(DatabaseConst::USER_MODULE_PROGRESS())
                ->where('status', 'completed')
                ->count();

            $overallAvgScore = (int) DB::table(DatabaseConst::USER_MODULE_PROGRESS())
                ->where('status', 'completed')
                ->where('score', '>', 0)
                ->avg('score');

            $totalPassedExams = DB::table(DatabaseConst::USER_MODULE_PROGRESS().' as ump')
                ->join(DatabaseConst::CURRICULUM_MODULES().' as cm', 'ump.module_id', '=', 'cm.id')
                ->where('ump.status', 'completed')
                ->where(function ($q) {
                    $q->where('cm.is_exam', true)->orWhere('cm.id', 'like', '%-M13');
                })
                ->whereRaw('ump.score >= cm.passing_score')
                ->count();

            return Response::buildSuccess(
                [
                    'list' => $data,
                    'summary' => [
                        'total_students' => $totalStudents,
                        'total_completed_modules' => $totalCompleted,
                        'overall_avg_score' => $overallAvgScore,
                        'total_passed_exams' => $totalPassedExams,
                    ],
                ],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getStudentScoreDetail(string $studentId): array
    {
        try {
            $student = DB::table(DatabaseConst::USER())
                ->whereNull('deleted_at')
                ->where('id', $studentId)
                ->first();

            if (! $student) {
                return Response::buildErrorNotFound('Data siswa tidak ditemukan.');
            }

            // Fetch all module progresses with module info
            $modules = DB::table(DatabaseConst::USER_MODULE_PROGRESS().' as ump')
                ->join(DatabaseConst::CURRICULUM_MODULES().' as cm', 'ump.module_id', '=', 'cm.id')
                ->where('ump.user_id', $studentId)
                ->select([
                    'ump.module_id',
                    'ump.status',
                    'ump.score',
                    'ump.completed_at',
                    'cm.title',
                    'cm.level_id',
                    'cm.cefr',
                    'cm.is_exam',
                    'cm.passing_score',
                    'cm.order_index',
                ])
                ->orderBy('cm.order_index', 'asc')
                ->orderBy('ump.completed_at', 'desc')
                ->get();

            // Fetch sub module progresses (reading, listening, writing, speaking, assessment)
            $subProgresses = DB::table(DatabaseConst::USER_SUB_MODULE_PROGRESS().' as usmp')
                ->join(DatabaseConst::SUB_MODULES().' as sm', 'usmp.sub_module_id', '=', 'sm.id')
                ->where('usmp.user_id', $studentId)
                ->select([
                    'usmp.sub_module_id',
                    'sm.module_id',
                    'sm.skill_type',
                    'sm.title',
                    'sm.order_index',
                    'usmp.status',
                    'usmp.score',
                    'usmp.completed_at',
                ])
                ->orderBy('sm.order_index', 'asc')
                ->get();

            $subMap = [];
            foreach ($subProgresses as $sub) {
                $subMap[$sub->module_id][] = $sub;
            }

            $enrichedModules = collect($modules)->map(function ($mod) use ($subMap) {
                $subs = $subMap[$mod->module_id] ?? [];
                $mod->sub_modules = $subs;

                return $mod;
            });

            return Response::buildSuccess(
                [
                    'student' => $student,
                    'modules' => $enrichedModules,
                ],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }
}
