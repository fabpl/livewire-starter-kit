<?php

declare(strict_types=1);

use Illuminate\Support\Sleep;

/* @note The recorded sequence is the whole assertion, because recording and serving are
   mutually exclusive: the fake appends the duration and returns, so a sleep that appears in the
   sequence is one that was never waited out, and a sleep that was served appears nowhere. The
   two seconds are what makes the failure legible — unfaked, this test waits them. */
it('records a sleep rather than serving it', function (): void {
    Sleep::for(2)->seconds();

    Sleep::assertSequence([Sleep::for(2)->seconds()]);
});
