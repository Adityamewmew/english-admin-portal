<?php

namespace App\Usecase\Admin;

use App\Constants\DatabaseConst;
use App\Constants\ResponseConst;
use App\Http\Presenter\Response;
use App\Usecase\Usecase;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionBankUsecase extends Usecase
{
    public function getAll(array $filterData = []): array
    {
        try {
            $query = DB::table(DatabaseConst::ITEM_BANK())
                ->whereNull('deleted_at')
                ->when($filterData['cefr'] ?? false, function ($q, $cefr) {
                    if ($cefr !== 'all') {
                        return $q->where('cefr', $cefr);
                    }
                })
                ->when($filterData['skill'] ?? false, function ($q, $skill) {
                    if ($skill !== 'all') {
                        if (in_array(strtolower($skill), ['grammar', 'vocabulary'])) {
                            return $q->whereIn('skill', [$skill, 'mcq']);
                        }
                        return $q->where('skill', $skill);
                    }
                })
                ->when($filterData['keywords'] ?? false, function ($q, $keywords) {
                    return $q->where(function ($sub) use ($keywords) {
                        $sub->where('question', 'ilike', '%'.$keywords.'%')
                            ->orWhere('id', 'ilike', '%'.$keywords.'%')
                            ->orWhere('answer', 'ilike', '%'.$keywords.'%');
                    });
                })
                ->orderBy('id', 'asc');

            $data = empty($filterData['no_pagination'])
                ? $query->paginate(20)
                : $query->get();

            if (! empty($filterData) && method_exists($data, 'appends')) {
                $data->appends($filterData);
            }

            return Response::buildSuccess(
                ['list' => $data],
                ResponseConst::HTTP_SUCCESS
            );
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function getByID(string $id): array
    {
        try {
            $item = DB::table(DatabaseConst::ITEM_BANK())
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (! $item) {
                return Response::buildErrorNotFound('Soal tidak ditemukan');
            }

            if (is_string($item->options)) {
                $item->options = json_decode($item->options, true);
            }

            return Response::buildSuccess(['item' => $item]);
        } catch (Exception $e) {
            Log::error(message: $e->getMessage(), context: ['method' => __METHOD__]);

            return Response::buildErrorService($e->getMessage());
        }
    }

    public function create(array $data): array
    {
        try {
            $id = !empty($data['id']) ? trim($data['id']) : 'Q-' . ($data['cefr'] ?? 'A1') . '-' . strtoupper(substr(uniqid(), -5));

            $existing = DB::table(DatabaseConst::ITEM_BANK())->where('id', $id)->first();
            if ($existing) {
                return Response::buildErrorValidation(['id' => 'Question ID already exists']);
            }

            $options = null;
            if (!empty($data['options'])) {
                $options = is_array($data['options']) ? json_encode(array_values(array_filter($data['options']))) : $data['options'];
            }

            $insertData = [
                'id' => $id,
                'skill' => $data['skill'] ?? 'grammar',
                'cefr' => $data['cefr'] ?? 'A1',
                'type' => $data['type'] ?? 'MCQ',
                'question' => $data['question'],
                'options' => $options,
                'answer' => $data['answer'] ?? '',
                'audio_script' => $data['audio_script'] ?? null,
                'audio_url' => $data['audio_url'] ?? null,
                'explanation' => $data['explanation'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::ITEM_BANK())->insert($insertData);

            return Response::buildSuccess(['id' => $id, 'item' => $insertData], ResponseConst::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function update(string $id, array $data): array
    {
        try {
            $existing = DB::table(DatabaseConst::ITEM_BANK())->where('id', $id)->whereNull('deleted_at')->first();
            if (!$existing) {
                return Response::buildErrorNotFound('Soal tidak ditemukan');
            }

            $options = $existing->options;
            if (isset($data['options'])) {
                $options = is_array($data['options']) ? json_encode(array_values(array_filter($data['options']))) : $data['options'];
            }

            $updateData = [
                'skill' => $data['skill'] ?? $existing->skill,
                'cefr' => $data['cefr'] ?? $existing->cefr,
                'type' => $data['type'] ?? $existing->type,
                'question' => $data['question'] ?? $existing->question,
                'options' => $options,
                'answer' => $data['answer'] ?? $existing->answer,
                'audio_script' => array_key_exists('audio_script', $data) ? $data['audio_script'] : $existing->audio_script,
                'audio_url' => array_key_exists('audio_url', $data) ? $data['audio_url'] : $existing->audio_url,
                'explanation' => array_key_exists('explanation', $data) ? $data['explanation'] : $existing->explanation,
                'updated_at' => now(),
            ];

            DB::table(DatabaseConst::ITEM_BANK())->where('id', $id)->update($updateData);

            return Response::buildSuccess(['id' => $id]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }

    public function delete(string $id): array
    {
        try {
            $existing = DB::table(DatabaseConst::ITEM_BANK())->where('id', $id)->whereNull('deleted_at')->first();
            if (!$existing) {
                return Response::buildErrorNotFound('Soal tidak ditemukan');
            }

            DB::table(DatabaseConst::ITEM_BANK())->where('id', $id)->update(['deleted_at' => now()]);

            return Response::buildSuccess(['id' => $id]);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['method' => __METHOD__]);
            return Response::buildErrorService($e->getMessage());
        }
    }
}
