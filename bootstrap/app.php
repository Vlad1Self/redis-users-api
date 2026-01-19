<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use App\Http\Responses\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $renderMap = [
            NotFoundHttpException::class => [
                'code' => 404,
                'message' => 'Такой url не существует'
            ],
            ServiceUnavailableHttpException::class => [
                'code' => 503,
                'message' => 'Сервис временно не доступен'
            ],
            UnprocessableEntityHttpException::class => [
                'code' => 422,
                'message' => 'Не верный запрос'
            ],
            ThrottleRequestsException::class => [
                'code' => 429,
                'message' => 'Слишком много запросов. Попробуйте позже'
            ],
            TooManyRequestsHttpException::class => [
                'code' => 429,
                'message' => 'Слишком много запросов. Попробуйте позже'
            ],
            PostTooLargeException::class => [
                'code' => 413,
                'message' => 'Не верный запрос'
            ],
            MethodNotAllowedHttpException::class => [
                'code' => 400,
                'message' => 'Метод не допустим'
            ],
            BadRequestException::class => [
                'code' => 400,
                'message' => 'Не верный запрос'
            ],
            UnauthorizedHttpException::class => [
                'code' => 403,
                'message' => 'Доступ запрещен'
            ],
            ValidationException::class => [
                'code' => 422,
                'message' => 'Ошибка валидации',
                'isValidation' => true
            ],
        ];

        foreach ($renderMap as $class => $data) {
            $exceptions->renderable(function (\Throwable $exception, $request) use ($class, $data) {

                if (!$exception instanceof $class) {
                    return null;
                }

                if (!empty($data['isValidation']) && $exception instanceof ValidationException) {
                    return ApiResponse::error(
                        $data['code'],
                        $data['message'],
                        null,
                        (array)$exception->errors()
                    );
                }

                return ApiResponse::error(
                    $data['code'],
                    $data['message'],
                    (string)$exception->getMessage()
                );
            });
        }

        $exceptions->renderable(function (\Throwable $exception, $request) {
            return ApiResponse::error(
                500,
                'Внутренняя ошибка сервера',
                (string)$exception->getMessage()
            );
        });
    })->create();
