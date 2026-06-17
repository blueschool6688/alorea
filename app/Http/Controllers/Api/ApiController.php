<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends BaseController
{

    protected function sendApiResponse(mixed $data = null, string $message = 'success', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json(compact('status', 'message', 'data'), $status); //200
    }

    protected function responseServerError(string $message = "Internal Server Error", mixed $data = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $data); // 500
    }

    protected function responseCreated(string $message = 'Created success.', mixed $data = null): JsonResponse
    {
        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    protected function responseUnprocess(string $message = 'Invalid data', mixed $data = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $data); // 422
    }

    protected function responseNotFound(mixed $data = null, ?string $message = 'Record not found!'): JsonResponse
    {
        return $this->APIError(Response::HTTP_NOT_FOUND, $message, $data);
    }

    protected function responseConflict(string $message = '', mixed $data = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_CONFLICT, $message, $data);
    }

    protected function paginator($response): array
    {
        return [
            'total' => $response->total(),
            'per_page' => $response->perPage(),
            'current_page' => $response->currentPage(),
            'last_page' => $response->lastPage(),
            'from' => $response->firstItem(),
            'to' => $response->lastItem(),
            'path' => $response->path(),
        ];
    }

    protected function responseApiWithOptions(mixed $response = null, string $message = ''): JsonResponse
    {
        if ($response instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return $this->sendApiResponse([
                'data' => $response->values(),
                'paginator' => $this->paginator($response)
            ], $message);
        }
        return $this->sendApiResponse($response, $message);
    }

    private function APIError(int $code, ?string $title = '', mixed $data = null): JsonResponse
    {
        $errors = [
            'status' => $code,
            'errors' => [
                'title' => $title,
                'data' => $data,
            ],
        ];
        return new JsonResponse($errors, $code);
    }
}
