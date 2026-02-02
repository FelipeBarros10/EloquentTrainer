@extends('layouts.app')

@section('title', 'Desafios - EloquentTrainer')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight">Desafios Eloquent</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-300">
            Resolva desafios com Eloquent ORM e receba feedback instantaneo. A plataforma executa sua query em sandbox e compara com o gabarito.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach ($challenges as $challenge)
            @php
                $isDone = !empty($completed[$challenge['id']]);
                $difficulty = $challenge['difficulty'];
                $badge = match ($difficulty) {
                    'easy' => 'bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/20',
                    'medium' => 'bg-amber-500/15 text-amber-200 ring-1 ring-amber-400/20',
                    'hard' => 'bg-rose-500/15 text-rose-200 ring-1 ring-rose-400/20',
                };
            @endphp

            <a href="{{ route('challenges.show', ['id' => $challenge['id']]) }}"
               class="group relative rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:border-white/20 hover:bg-white/10">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $badge }}">
                                {{ strtoupper($difficulty) }}
                            </span>
                            <span class="text-xs text-zinc-400">{{ $challenge['points'] }} pts</span>
                            @if ($isDone)
                                <span class="text-xs font-medium text-emerald-300">Concluido</span>
                            @endif
                        </div>

                        <h2 class="mt-2 text-lg font-semibold tracking-tight text-zinc-100">
                            {{ $challenge['title'] }}
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-zinc-300">
                            {{ $challenge['story'] }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($challenge['models'] as $model)
                        <span class="rounded-lg bg-black/30 px-2 py-1 text-xs text-zinc-300 ring-1 ring-white/10">{{ $model }}</span>
                    @endforeach
                </div>

                <div class="mt-5 text-sm font-medium text-zinc-200 opacity-90 group-hover:opacity-100">
                    Abrir desafio →
                </div>
            </a>
        @endforeach
    </div>
@endsection
