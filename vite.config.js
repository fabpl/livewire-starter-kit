import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { fontsource } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Fontsource rather than one of the remote providers. They differ in one way that
            // decides it: the remote ones fetch from a third party's stylesheet endpoint on
            // every build, while Fontsource resolves from an installed package. The typefaces
            // are therefore versioned dependencies fixed by the lockfile and fetched by the
            // install step the chain already runs — the reproducibility argument of ADR-0001
            // applied to assets — and the browser suite's build step needs no network.
            //
            // Preloading is restricted rather than left at the provider's default of every
            // variant: the three faces that set above-the-fold text are preloaded, and the
            // interface face's heavier weight arrives by swap, since it sets only short labels.
            // Every family names its preloaded weights, including the two that declare a single
            // variant. Leaving those to the default would give the same three preloads today
            // and silently add a fourth the day a weight is added — the restriction would then
            // be true by coincidence rather than by decision.
            //
            // Metric-matched fallbacks are declined rather than silently unavailable: they need
            // the optional `fontaine` package, which this ticket did not ask for. Saying so here
            // keeps the build quiet and leaves the dependency a decision rather than an omission.
            fonts: [
                fontsource('Poppins', {
                    weights: [600],
                    subsets: ['latin'],
                    styles: ['normal'],
                    display: 'swap',
                    preload: [{ weight: 600 }],
                    optimizedFallbacks: false,
                }),
                fontsource('Lato', {
                    // The design document asks for the interface face at regular and medium.
                    // Lato ships no 500 — its weights are 100, 300, 400, 700 and 900 — so the
                    // short-label weight is 700. Declaring 500 would resolve to 400 by CSS font
                    // matching and put a weight in the stylesheet that never renders.
                    weights: [400, 700],
                    subsets: ['latin'],
                    styles: ['normal'],
                    display: 'swap',
                    preload: [{ weight: 400 }],
                    optimizedFallbacks: false,
                }),
                fontsource('Lora', {
                    weights: [400],
                    subsets: ['latin'],
                    styles: ['normal'],
                    display: 'swap',
                    preload: [{ weight: 400 }],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
