<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /* @note No host list is passed: the framework derives one from the configured
           application URL and its subdomains. It also disables this middleware in the local
           environment and under test, which is why local development and the suite are
           unaffected — and why the suite cannot observe this default and no level makes its
           guard provable. It is the one default here taken on trust, installed anyway because
           a forged host turns a token-bearing link into a URL the attacker chose. */
        $middleware->trustHosts();

        /* @note A forwarded header is honoured only when the immediate peer is in this list, so
           a client reaching the application directly can never rewrite its own address. Loopback
           and the three private ranges describe a proxy terminating TLS in nearly every
           topology, without knowing the deployment. What they do not describe: an edge on public
           addresses, a CDN for instance — a deployment of that shape extends this list. The
           addresses are literals because this callback runs before the environment file is
           loaded, so a lookup here would see process variables and never the file. */
        $middleware->trustProxies(at: [
            '127.0.0.1',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
