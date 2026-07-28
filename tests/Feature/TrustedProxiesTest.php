<?php

declare(strict_types=1);

use function Pest\Laravel\get;
use function Pest\Laravel\withHeader;
use function Pest\Laravel\withServerVariables;

/*
 * @note The client address is read back from the request the kernel handled rather than from a
 * response body, because no route in this foundation echoes it and adding one would put a
 * fixture in the application to serve a test. The middleware acts on the request instance the
 * container holds, so reading it there observes the same resolution a controller would see.
 */

it('recovers the address a proxy on a private network forwards', function (): void {
    withServerVariables(['REMOTE_ADDR' => '10.1.2.3']);
    withHeader('X-Forwarded-For', '203.0.113.10');

    get('/')->assertOk();

    expect(request()->ip())->toBe('203.0.113.10');
});

it('ignores an address forwarded by a peer that is not a proxy', function (): void {
    withServerVariables(['REMOTE_ADDR' => '198.51.100.7']);
    withHeader('X-Forwarded-For', '203.0.113.10');

    get('/')->assertOk();

    expect(request()->ip())->toBe('198.51.100.7');
});
