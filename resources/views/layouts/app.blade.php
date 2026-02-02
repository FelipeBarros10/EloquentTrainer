<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $metaTitle = trim($__env->yieldContent('title', config('app.name', 'EloquentTrainer')));
            $metaDescription = trim($__env->yieldContent(
                'meta_description',
                'Plataforma de treino de Eloquent ORM com desafios praticos, SQL explainer e pontuacao por usuario.'
            ));
            $metaRobots = trim($__env->yieldContent('robots', 'index,follow'));
            $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
            $ogType = trim($__env->yieldContent('og_type', 'website'));
            $ogImage = trim($__env->yieldContent('og_image', asset('og.svg')));
        @endphp

        <title>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="robots" content="{{ $metaRobots }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta name="theme-color" content="#09090b">

        <meta property="og:site_name" content="{{ config('app.name', 'EloquentTrainer') }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $ogImage }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('head')
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100">
        <header class="border-b border-white/10">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
                <a href="{{ auth()->check() ? route('challenges.index') : route('home') }}" class="font-semibold tracking-tight">
                    EloquentTrainer
                </a>

                <div class="flex items-center gap-3 text-sm text-zinc-300">
                    <div class="rounded-full bg-white/10 px-3 py-1">
                        Pontos: <span class="font-semibold text-zinc-100">{{ $score ?? 0 }}</span>
                    </div>

                    @auth
                        <div class="hidden sm:block text-zinc-400">
                            {{ auth()->user()->name }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-white/10 px-3 py-1 text-zinc-200 hover:bg-white/15">
                                Sair
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-4 py-6">
            @yield('content')
        </main>

        <footer class="mx-auto max-w-6xl px-4 pb-10 pt-6 text-xs text-zinc-500">
            Dica: envie a query (sem ->get()) para ver o SQL, mas o juiz aceita ambos.
        </footer>
    </body>
</html>
