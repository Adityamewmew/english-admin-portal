<?php

namespace App\Http\Presenter;

use App\Constants\ResponseConst;

class Response
{
    /**
     * Build a success response
     */
    public static function buildSuccess(mixed $data = [], int $code = ResponseConst::HTTP_SUCCESS, string $message = 'Success'): array
    {
        return [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Build a success response for created resources
     */
    public static function buildSuccessCreated(mixed $data = [], string $message = ResponseConst::SUCCESS_MESSAGE_CREATED): array
    {
        return [
            'success' => true,
            'code' => ResponseConst::HTTP_CREATED,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Build an error response for service/server errors
     */
    public static function buildErrorService(string $message = ResponseConst::ERROR_MESSAGE_SERVICE): array
    {
        return [
            'success' => false,
            'code' => ResponseConst::HTTP_INTERNAL_ERROR,
            'message' => $message,
            'data' => null,
        ];
    }

    /**
     * Build an error response for not found
     */
    public static function buildErrorNotFound(string $message = ResponseConst::ERROR_MESSAGE_NOT_FOUND): array
    {
        return [
            'success' => false,
            'code' => ResponseConst::HTTP_NOT_FOUND,
            'message' => $message,
            'data' => null,
        ];
    }

    /**
     * Build an error response for validation failures
     */
    public static function buildErrorValidation(mixed $data = null, string $message = ResponseConst::ERROR_MESSAGE_VALIDATION): array
    {
        return [
            'success' => false,
            'code' => ResponseConst::HTTP_BAD_REQUEST,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Build an error response for conflict / already exists
     */
    public static function buildErrorConflict(string $message = 'Data sudah ada'): array
    {
        return [
            'success' => false,
            'code' => 409,
            'message' => $message,
            'data' => null,
        ];
    }

    /**
     * Build a custom error response
     */
    public static function buildError(int $code, string $message, mixed $data = null): array
    {
        return [
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
