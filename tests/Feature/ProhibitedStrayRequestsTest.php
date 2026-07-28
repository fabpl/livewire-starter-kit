<?php

declare(strict_types=1);

use Illuminate\Http\Client\StrayRequestException;
use Illuminate\Support\Facades\Http;

/* @note Nothing listens on port one of the loopback address, and that is what the address is
   for. This is the one place in the suite that writes an outbound call with no fake behind it,
   and what it asserts is that the call never leaves: on the day the prohibition is removed, the
   attempt has to be refused locally rather than reach a third party. */
it('fails a test that reaches the network without a fake', function (): void {
    Http::get('http://127.0.0.1:1');
})->throws(StrayRequestException::class);
