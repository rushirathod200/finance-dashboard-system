<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active.user' => EnsureActiveUser::class,
            'role' => RoleMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $isApiRequest = static fn (Request $request): bool => $request->is('api/*') || $request->expectsJson();

        $exceptions->shouldRenderJsonWhen(fn (Request $request, Throwable $e) => $isApiRequest($request));

        $exceptions->render(function (AuthenticationException $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return redirect()
                    ->guest(route('login'))
                    ->withErrors(['auth' => 'Please sign in to continue.']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        });

        $exceptions->render(function (ValidationException $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'Forbidden',
            ], Response::HTTP_FORBIDDEN);
        });

        $exceptions->render(function (TokenMismatchException $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return redirect()
                    ->guest(route('login'))
                    ->withErrors(['session' => 'Your session expired. Please sign in again.']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Session expired.',
            ], 419);
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            $message = $exception->getMessage();

            return response()->json([
                'success' => false,
                'message' => $message !== ''
                    ? $message
                    : (Response::$statusTexts[$exception->getStatusCode()] ?? 'Request failed'),
            ], $exception->getStatusCode());
        });

        $exceptions->render(function (Throwable $exception, Request $request) use ($isApiRequest) {
            if (! $isApiRequest($request)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => app()->hasDebugModeEnabled()
                    ? $exception->getMessage()
                    : 'Server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
