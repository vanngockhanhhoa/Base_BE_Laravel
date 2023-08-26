<?php

namespace App\Exceptions;

use Helper\ResponseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

use App\Exceptions\LoginFailedException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $errors = $exception->errors();
            return ResponseService::responseJsonError(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                data_get(array_values($errors), '0.0'),
                $exception->getMessage(),
                $errors
            );
        } elseif ($exception instanceof ModelNotFoundException) {
            return ResponseService::responseJsonError(
                Response::HTTP_NOT_FOUND,
                $exception->getMessage(),
                $exception->getMessage()
            );
        } elseif ($exception instanceof UnauthorizedHttpException | $exception instanceof AuthenticationException) {
            return ResponseService::responseJsonError(
                Response::HTTP_UNAUTHORIZED,
                trans('errors.unauthenticated')
            );
        } elseif ($exception instanceof TokenInvalidException) {
            return ResponseService::responseJsonError(
                Response::HTTP_UNAUTHORIZED,
                trans('errors.unauthenticated'),
                trans('errors.invalid_token')
            );
        } elseif ($exception instanceof TokenExpiredException) {
            Log::info('TokenExpiredException');
            return ResponseService::responseJsonError(
                Response::HTTP_UNAUTHORIZED,
                trans('errors.expired_token'),
                trans('errors.expired_token')
            );
        } elseif ($exception instanceof NotFoundHttpException) {
            return ResponseService::responseJsonError(
                Response::HTTP_NOT_FOUND,
                trans('errors.route_not_found')
            );
        } elseif ($exception instanceof HttpException) {
            return ResponseService::responseJsonError(
                $exception->getStatusCode(),
                trans('errors.login_fail_many_time'),
                $exception->getMessage()
            );
        } elseif ($exception instanceof LoginFailedException) {
            return ResponseService::responseJsonError(
                $exception->getStatusCode(),
                $exception->getMessage(),
                $exception->getMessage()
            );
        } else {
            return ResponseService::responseJsonError(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                trans('errors.something_error'),
                $exception->getMessage(),
                $exception->getTrace()
            );
        }
    }

    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }
}
