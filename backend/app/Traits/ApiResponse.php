<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

trait ApiResponse
{
    /**
     * Prepare response.
     *
     * @param string $status
     * @param string $message
     * @param int $statusCode
     * @return array
     */
    protected function prepareResponse($status = 'success', $message = '', $statusCode = Response::HTTP_OK): array
    {
        if (empty($message)) {
            $message = Response::$statusTexts[$statusCode];
        }

        return [
            'status' => $status,
            'message' => $message,
            'message_content' => $message,
        ];
    }

    /**
     * @param $data
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public function success($data = [], $statusCode = Response::HTTP_OK, $message = '', $extraData = null): JsonResponse
    {
        $status = 'success';
        $response = $this->prepareResponse($status, $message, $statusCode);
        $response['results'] = $data;
        $response['concurrent_time'] = Carbon::now()->timestamp;
        if ($extraData) $response['extra'] = $extraData;

        return response()->json($response, $statusCode);
    }

    /**
     * Error Response
     *
     * @param  $errors
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public function error($errors, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $message = ''): JsonResponse
    {
        $status = 'failure';
        $response = $this->prepareResponse($status, $message, $statusCode);
        $response['results'] = $errors;

        return response()->json($response, $statusCode);
    }

    /**
     * Response with status code 200.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function ok($data, $message = ''): JsonResponse
    {
        return $this->success($data, Response::HTTP_OK, $message);
    }

    /**
     * Response with status code 201.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function created($data, $message = ''): JsonResponse
    {
        return $this->success($data, Response::HTTP_CREATED, $message);
    }

    /**
     * Response with status code 400.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function badRequest($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_BAD_REQUEST, $message);
    }

    /**
     * Response with status code 401.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function unauthorized($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_UNAUTHORIZED, $message);
    }

    /**
     * Response with status code 403.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function forbidden($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_FORBIDDEN, $message);
    }

    /**
     * Response with status code 404.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function notFound($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_NOT_FOUND, $message);
    }

    /**
     * Response with status code 409.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function conflict($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_CONFLICT, $message);
    }

    /**
     * Response with status code 422.
     *
     * @param  $data
     * @param string $message
     * @return JsonResponse
     */
    public function unprocessable($data, $message = ''): JsonResponse
    {
        return $this->error($data, Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
