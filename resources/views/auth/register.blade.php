<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Criar conta - EloquentTrainer</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="canonical" href="{{ route('register') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-zinc-950 text-zinc-100">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
            <div class="w-full max-w-md rounded-2xl border border-white/10 bg-white/5 p-6">
                <div class="text-sm font-semibold text-zinc-200">EloquentTrainer</div>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight">Criar conta</h1>
                <p class="mt-2 text-sm text-zinc-300">
                    Sua pontuacao fica vinculada ao seu usuario.
                </p>

                <form method="POST" action="{{ route('register.submit') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label class="text-xs text-zinc-400">Nome</label>
                        <input
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            class="mt-1 w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-zinc-100 outline-none focus:border-white/20"
                            required
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-rose-300">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400">Email</label>
                        <input
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            class="mt-1 w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-zinc-100 outline-none focus:border-white/20"
                            required
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-rose-300">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400">Senha</label>
                        <input
                            name="password"
                            type="password"
                            class="mt-1 w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-zinc-100 outline-none focus:border-white/20"
                            required
                        />
                        @error('password')
                            <div class="mt-1 text-sm text-rose-300">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400">Confirmar senha</label>
                        <input
                            name="password_confirmation"
                            type="password"
                            class="mt-1 w-full rounded-xl border border-white/10 bg-black/40 px-3 py-2 text-sm text-zinc-100 outline-none focus:border-white/20"
                            required
                        />
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-zinc-100">
                        Criar conta
                    </button>
                </form>

                <div class="mt-5 text-sm text-zinc-400">
                    Ja tem conta?
                    <a class="text-zinc-200 hover:underline" href="{{ route('login') }}">Entrar</a>
                </div>
            </div>
        </div>
    </body>
</html>
