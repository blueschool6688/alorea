<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle unauthenticated requests cho Sanctum
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login first.',
                    'code' => 'UNAUTHENTICATED'
                ], 401);
            }

            return null;
        });

        // Handle general exceptions
        $exceptions->render(function (\Throwable $e, Request $request) {
            // Debug log để kiểm tra
            \Log::info('Exception handler triggered', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'is_api' => $request->is('api/*'),
                'expects_json' => $request->expectsJson(),
                'url' => $request->url(),
                'user_authenticated' => auth()->check(),
                'headers' => $request->headers->all()
            ]);

            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 500;

                // Handle different exception types
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $statusCode = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422;
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401;
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $statusCode = 403;
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $statusCode = 404;
                }

                $response = [
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred'
                ];

                // Add validation errors if ValidationException
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $response['errors'] = $e->errors();
                }

                // Add debug info in development
                if (config('app.debug')) {
                    $response['debug'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => explode("\n", $e->getTraceAsString())
                    ];
                }

                return new JsonResponse($response, $statusCode);
            }

            // Return null để Laravel xử lý theo default handler
            return null;
        });
    })->create();
