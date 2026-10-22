<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CourseClassUsecase extends Usecase
{
    /**
     * Get all course classes (A1 - C2) with active student count
     */
    public function getClasses(): array
    {
        try {
            $classes = DB::table(DatabaseConst::COURSE_CLASSES())
                ->orderBy('order_index', 'asc')
                ->get();

            // Count active students per CEFR class
            $counts = DB::table(DatabaseConst::USER_CLASS_ACCESS())
                ->where('status', 'active')
                ->select('cefr', DB::raw('count(*) as count'))
                ->groupBy('cefr')
                ->pluck('count', 'cefr')
                ->toArray();

            $list = $classes->map(function ($c) use ($counts) {
                $c->student_count = $counts[$c->cefr] ?? 0;
                if (is_string($c->features)) {
                    $c->features = json_decode($c->features, true) ?: [];
                }
                if (is_string($c->tier_plans)) {
                    $c->tier_plans = json_decode($c->tier_plans, true) ?: [];
                }
                return $c;
            });

            return Response::buildSuccess([
                'classes' => $list,
            ]);
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::getClasses Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get single class by CEFR
     */
    public function getClassByCefr(string $cefr): array
    {
        try {
            $class = DB::table(DatabaseConst::COURSE_CLASSES())
                ->where('cefr', strtoupper($cefr))
                ->first();

            if (!$class) {
                return Response::buildErrorNotFound('Data kelas tidak ditemukan');
            }

            if (is_string($class->features)) {
                $class->features = json_decode($class->features, true) ?: [];
            }
            if (is_string($class->tier_plans)) {
                $class->tier_plans = json_decode($class->tier_plans, true) ?: [];
            }

            return Response::buildSuccess([
                'class' => $class,
            ]);
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::getClassByCefr Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Create new course class
     */
    public function createClass(array $data): array
    {
        try {
            $cefr = strtoupper(trim($data['cefr'] ?? ''));
            if (empty($cefr)) {
                return Response::buildErrorValidation(['cefr' => ['Kategori level CEFR wajib dipilih.']]);
            }

            $exists = DB::table(DatabaseConst::COURSE_CLASSES())
                ->where('cefr', $cefr)
                ->exists();

            if ($exists) {
                return Response::buildErrorConflict("Paket kelas untuk CEFR {$cefr} sudah ada.");
            }

            $features = is_array($data['features'] ?? null)
                ? $data['features']
                : array_filter(array_map('trim', explode("\n", (string) ($data['features'] ?? ''))));

            $orderIndexMap = ['A1' => 1, 'A2' => 2, 'B1' => 3, 'B2' => 4, 'C1' => 5, 'C2' => 6];

            $insertData = [
                'id' => (string) Str::uuid(),
                'cefr' => $cefr,
                'name' => $data['name'] ?? "Kelas Bahasa Inggris CEFR {$cefr}",
                'description' => $data['description'] ?? null,
                'price' => (int) ($data['price'] ?? 0),
                'original_price' => !empty($data['original_price']) ? (int) $data['original_price'] : null,
                'features' => json_encode(array_values($features)),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'order_index' => $orderIndexMap[$cefr] ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $tierPlansInput = $data['tier_plans'] ?? [
                'basic' => ['price' => (int) (($data['price'] ?? 149000) * 0.35)],
                'pro' => ['price' => (int) ($data['price'] ?? 149000), 'original_price' => $data['original_price'] ?? null],
                'ultra' => ['price' => (int) (($data['price'] ?? 149000) * 1.8)],
            ];
            $insertData['tier_plans'] = $this->formatTierPlans($cefr, $tierPlansInput);

            DB::table(DatabaseConst::COURSE_CLASSES())->insert($insertData);

            return Response::buildSuccessCreated(null, "Paket kelas CEFR {$cefr} berhasil ditambahkan.");
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::createClass Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Update course class
     */
    public function updateClass(string $cefr, array $data): array
    {
        try {
            $cefr = strtoupper($cefr);
            $updateData = [
                'name' => $data['name'] ?? '',
                'description' => $data['description'] ?? null,
                'price' => (int) ($data['price'] ?? 0),
                'original_price' => !empty($data['original_price']) ? (int) $data['original_price'] : null,
                'is_active' => (bool) ($data['is_active'] ?? true),
                'updated_at' => now(),
            ];

            if (isset($data['features'])) {
                $features = is_array($data['features'])
                    ? $data['features']
                    : array_filter(array_map('trim', explode("\n", (string) $data['features'])));
                $updateData['features'] = json_encode(array_values($features));
            }

            if (isset($data['tier_plans'])) {
                $tierPlansJson = $this->formatTierPlans($cefr, $data['tier_plans']);
                $updateData['tier_plans'] = $tierPlansJson;

                // Sync base price with pro tier
                $plansArray = json_decode($tierPlansJson, true);
                if (isset($plansArray['pro']['price'])) {
                    $updateData['price'] = (int) $plansArray['pro']['price'];
                    $updateData['original_price'] = !empty($plansArray['pro']['originalPrice']) ? (int) $plansArray['pro']['originalPrice'] : null;
                }
            }

            DB::table(DatabaseConst::COURSE_CLASSES())
                ->where('cefr', $cefr)
                ->update($updateData);

            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, "Paket kelas CEFR {$cefr} berhasil diperbarui.");
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::updateClass Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Helper to format tier plans array into clean JSON
     */
    protected function formatTierPlans(string $cefr, array $input): string
    {
        $tiers = ['basic', 'pro', 'ultra'];
        $defaults = [
            'basic' => ['chat' => 20, 'voice' => 5, 'name' => "Paket Basic ({$cefr})"],
            'pro' => ['chat' => 100, 'voice' => 30, 'name' => "Paket Pro ({$cefr})"],
            'ultra' => ['chat' => -1, 'voice' => 120, 'name' => "Paket Ultra ({$cefr})"],
        ];

        $result = [];
        foreach ($tiers as $t) {
            $tData = $input[$t] ?? [];
            $features = is_array($tData['features'] ?? null)
                ? $tData['features']
                : array_filter(array_map('trim', explode("\n", (string) ($tData['features'] ?? ''))));

            $result[$t] = [
                'name' => $tData['name'] ?? $defaults[$t]['name'],
                'price' => (int) ($tData['price'] ?? 0),
                'originalPrice' => !empty($tData['original_price']) ? (int) $tData['original_price'] : null,
                'dailyChatLimit' => isset($tData['daily_chat_limit']) ? (int) $tData['daily_chat_limit'] : $defaults[$t]['chat'],
                'dailyVoiceMinutes' => (int) ($tData['daily_voice_minutes'] ?? $defaults[$t]['voice']),
                'features' => array_values($features),
            ];
        }

        return json_encode($result);
    }

    /**
     * Delete course class
     */
    public function deleteClass(string $cefr): array
    {
        try {
            DB::table(DatabaseConst::COURSE_CLASSES())
                ->where('cefr', strtoupper($cefr))
                ->delete();

            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, "Paket kelas CEFR {$cefr} berhasil dihapus.");
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::deleteClass Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get student class accesses with filter & pagination
     */
    public function getStudentAccesses(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::USER_CLASS_ACCESS() . ' as uca')
                ->leftJoin(DatabaseConst::USER() . ' as u', 'uca.user_id', '=', 'u.id')
                ->select([
                    'uca.id',
                    'uca.user_id',
                    'uca.cefr',
                    'uca.tier',
                    'uca.granted_by',
                    'uca.status',
                    'uca.created_at',
                    'u.name as user_name',
                    'u.email as user_email',
                    'u.current_cefr',
                ])
                ->when($filterData['cefr'] ?? false, function ($q, $cefr) {
                    if ($cefr !== 'all') {
                        return $q->where('uca.cefr', strtoupper($cefr));
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('u.name', 'ilike', '%' . $keywords . '%')
                            ->orWhere('u.email', 'ilike', '%' . $keywords . '%')
                            ->orWhere('uca.cefr', 'ilike', '%' . $keywords . '%');
                    });
                })
                ->orderBy('uca.created_at', 'desc');

            $data = $query->paginate(15);

            if (!empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            $totalAccesses = DB::table(DatabaseConst::USER_CLASS_ACCESS())->count();

            return Response::buildSuccess([
                'list' => $data,
                'total_accesses' => $totalAccesses,
            ]);
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::getStudentAccesses Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Grant manual class access to a student with specific tier
     */
    public function grantManualAccess(string $userId, string $cefr, string $tier = 'basic'): array
    {
        try {
            $cefr = strtoupper(trim($cefr));
            $tier = strtolower(trim($tier));
            if (!in_array($tier, ['basic', 'pro', 'ultra'])) {
                $tier = 'basic';
            }

            $user = DB::table(DatabaseConst::USER())->where('id', $userId)->first();
            if (!$user) {
                return Response::buildErrorNotFound('Siswa tidak ditemukan.');
            }

            DB::table(DatabaseConst::USER_CLASS_ACCESS())->updateOrInsert(
                ['user_id' => $userId, 'cefr' => $cefr],
                [
                    'id' => (string) Str::uuid(),
                    'tier' => $tier,
                    'granted_by' => 'admin',
                    'status' => 'active',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, "Akses kelas CEFR {$cefr} ({$tier}) berhasil diberikan kepada {$user->name}.");
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::grantManualAccess Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Revoke student class access
     */
    public function revokeAccess(string $id): array
    {
        try {
            DB::table(DatabaseConst::USER_CLASS_ACCESS())->where('id', $id)->delete();
            return Response::buildSuccess(null, ResponseConst::HTTP_SUCCESS, 'Akses kelas siswa berhasil dicabut.');
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::revokeAccess Error: ' . $e->getMessage());
            return Response::buildErrorService($e->getMessage());
        }
    }

    /**
     * Get active students list for manual access dropdown
     */
    public function getStudentsForDropdown(): array
    {
        try {
            $students = DB::table(DatabaseConst::USER())
                ->where('role', 'student')
                ->select(['id', 'name', 'email', 'current_cefr'])
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get();

            return $students->toArray();
        } catch (Exception $e) {
            Log::error('CourseClassUsecase::getStudentsForDropdown Error: ' . $e->getMessage());
            return [];
        }
    }
}
