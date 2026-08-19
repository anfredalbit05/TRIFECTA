<?php

// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;
// use Illuminate\Http\Request;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware): void {
//     $middleware->redirectGuestsTo(fn () => route('home'));
//         })
//     ->withExceptions(function (Exceptions $exceptions): void {
//         $exceptions->shouldRenderJsonWhen(
//             fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
//         );
//     })->create();


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckMaintenanceMode;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Redirect unauthenticated / expired-session users to the home route
        // instead of Laravel's default route('login') (which doesn't exist here).
        $middleware->redirectGuestsTo(fn () => route('home'));

        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );

        // Register your middleware alias here
        $middleware->alias([
            'maintenance' => CheckMaintenanceMode::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Safety net: if a guest/expired session ever triggers a route-not-found
        // (e.g. a stale route('login') reference) or an auth exception slips through,
        // send them to home instead of showing an error page.
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|
            \Symfony\Component\Routing\Exception\RouteNotFoundException|
            \Illuminate\Auth\AuthenticationException $e,
            Request $request
        ) {
            if (!$request->expectsJson()) {
                \Illuminate\Support\Facades\Log::info('Redirected to home after caught exception: ' . get_class($e), [
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                ]);

                return redirect()->route('home');
            }
        });
    })
    ->create();
