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
}
