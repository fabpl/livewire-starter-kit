<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any" />
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />

    {{-- Applies the theme class before the first paint. A deferred module paints first, so an
    inline script in the head is the only way to avoid a flash of the wrong theme. It reads the
    stored preference, falls back to the media query, and sets a class — and it is forbidden from
    growing beyond those three steps. Nothing writes the stored preference yet; the control that
    does arrives with the theme switch, and this script is written whole now so that the switch
    has somewhere to write to. --}}
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var dark = stored === 'dark' || (stored !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @fonts

    {{-- No fallback stylesheet behind a manifest check. The one the kit shipped with would need
    regenerating by hand on every theme change, which makes it a second source of truth for
    styling condemned to disagree with the first. The cost, which is not free: this layout is
    rendered by the blocking suite as well as the browser one, so `composer test` now needs a
    build present. `composer setup` produces one and `composer browser:test` rebuilds before it
    runs; CI runs setup before the gate. What no command does is build on the way into
    `composer test` — run setup first in a fresh tree. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground">
    {{-- The document and nothing else: no header, no navigation, no footer. The repository has
    one page, so anything else placed here would be a conjecture about pages that do not exist —
    and the first authenticated screen of a real project would want a different shell entirely.
    This page's own chrome is composed by the Page. --}}
    {{ $slot }}
</body>
</html>
