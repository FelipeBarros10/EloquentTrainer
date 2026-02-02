@extends('layouts.app')

@section('title', $challenge['title'] . ' - EloquentTrainer')

@section('content')
    @php
        $isDone = !empty($completed[$challenge['id']]);
        $difficulty = $challenge['difficulty'];
        $badge = match ($difficulty) {
            'easy' => 'bg-emerald-500/15 text-emerald-200 ring-1 ring-emerald-400/20',
            'medium' => 'bg-amber-500/15 text-amber-200 ring-1 ring-amber-400/20',
            'hard' => 'bg-rose-500/15 text-rose-200 ring-1 ring-rose-400/20',
        };
    @endphp

    <div class="mb-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('challenges.index') }}" class="text-sm text-zinc-400 hover:text-zinc-200"><- Voltar</a>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $badge }}">
                    {{ strtoupper($difficulty) }}
                </span>
                <span class="text-xs text-zinc-400">{{ $challenge['points'] }} pts</span>
                @if ($isDone)
                    <span class="text-xs font-medium text-emerald-300">Concluido</span>
                @endif
            </div>
        </div>

        <h1 class="mt-3 text-2xl font-semibold tracking-tight">{{ $challenge['title'] }}</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-300">{{ $challenge['story'] }}</p>

        <div class="mt-4 flex flex-wrap gap-2">
            <div class="text-xs text-zinc-400">Models envolvidos:</div>
            @foreach ($challenge['models'] as $model)
                <span class="rounded-lg bg-black/30 px-2 py-1 text-xs text-zinc-300 ring-1 ring-white/10">{{ $model }}</span>
            @endforeach
        </div>

        <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-sm font-semibold text-zinc-200">Schema rapido (colunas e tipos)</div>
            <div class="mt-3 grid gap-3 lg:grid-cols-2">
                @foreach ($challenge['models'] as $model)
                    @php
                        $schema = \App\Challenges\SchemaCatalog::forModel($model);
                    @endphp
                    <div class="rounded-xl border border-white/10 bg-black/30 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm font-semibold text-zinc-100">{{ $model }}</div>
                            @if ($schema)
                                <div class="text-xs text-zinc-500">{{ $schema['table'] }}</div>
                            @endif
                        </div>
                        @if ($schema)
                            <div class="mt-2 space-y-1 text-xs text-zinc-300">
                                @foreach ($schema['columns'] as $col)
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <code class="rounded bg-black/40 px-1 py-0.5 text-zinc-100">{{ $col['name'] }}</code>
                                        <div class="text-zinc-400">
                                            {{ $col['type'] }}@if (!empty($col['notes'])) <span class="text-zinc-500">({{ $col['notes'] }})</span>@endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-2 text-xs text-zinc-400">Sem definicao no catalogo.</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <h2 class="text-sm font-semibold text-zinc-200">Sua query</h2>
            <p class="mt-1 text-xs text-zinc-400">
                Envie apenas a expressao Eloquent. O juiz adiciona <code class="rounded bg-black/40 px-1 py-0.5">return</code> automaticamente.
            </p>

            <form method="POST" action="{{ route('challenges.submit', ['id' => $challenge['id']]) }}" class="mt-4 space-y-3">
                @csrf

                <textarea
                    name="code"
                    rows="12"
                    class="w-full rounded-xl border border-white/10 bg-black/40 p-3 font-mono text-xs leading-5 text-zinc-100 outline-none ring-0 placeholder:text-zinc-500 focus:border-white/20"
                    spellcheck="false"
                    placeholder="Ex: Movie::where('release_year', 2024)"
                >{{ old('code', $code) }}</textarea>

                @error('code')
                    <div class="text-sm text-rose-300">{{ $message }}</div>
                @enderror

                <button type="submit" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-zinc-100">
                    Avaliar
                </button>
            </form>

            <form method="POST" action="{{ route('challenges.reset', ['id' => $challenge['id']]) }}" class="mt-3">
                @csrf
                <button type="submit" class="rounded-xl border border-white/15 bg-transparent px-4 py-2 text-sm font-semibold text-zinc-200 hover:bg-white/5">
                    Resetar editor
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <h2 class="text-sm font-semibold text-zinc-200">Feedback</h2>

            @if (is_null($result))
                <p class="mt-2 text-sm text-zinc-300">
                    Cole sua query e clique em <span class="font-medium">Avaliar</span>.
                </p>
            @else
                @if (!empty($result['error']))
                    <div class="mt-3 rounded-xl border border-rose-400/20 bg-rose-500/10 p-3 text-sm text-rose-200">
                        Erro: {{ $result['error'] }}
                    </div>
                @else
                    @if ($result['passed'])
                        <div class="mt-3 rounded-xl border border-emerald-400/20 bg-emerald-500/10 p-3 text-sm text-emerald-200">
                            Passou! Resultado identico ao gabarito.
                        </div>
                    @else
                        <div class="mt-3 rounded-xl border border-amber-400/20 bg-amber-500/10 p-3 text-sm text-amber-200">
                            Ainda nao. Seu resultado nao bateu com o gabarito.
                        </div>
                    @endif

                    <div class="mt-4 space-y-3">
                        <div>
                            <div class="text-xs font-medium text-zinc-300">SQL explainer</div>
                            @if (!empty($result['user_sql']))
                                <pre class="mt-2 overflow-auto rounded-xl border border-white/10 bg-black/50 p-3 text-xs leading-5 text-zinc-100">{{ $result['user_sql'] }}</pre>
                                @if (!empty($result['user_bindings']))
                                    <div class="mt-2 text-xs text-zinc-400">
                                        Bindings: <code class="rounded bg-black/40 px-1 py-0.5">{{ json_encode($result['user_bindings']) }}</code>
                                    </div>
                                @endif
                            @else
                                <div class="mt-2 text-sm text-zinc-300">
                                    Nao foi possivel capturar o SQL dessa execucao.
                                    Tente remover <code class="rounded bg-black/40 px-1 py-0.5">->get()</code> e retornar o Builder/Relation.
                                </div>
                            @endif
                        </div>

                        @php
                            $userCount = $result['user_result']?->count() ?? 0;
                            $goldCount = $result['gold_result']?->count() ?? 0;
                        @endphp

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-white/10 bg-black/30 p-3">
                                <div class="text-xs text-zinc-400">Seu resultado</div>
                                <div class="mt-1 text-lg font-semibold">{{ $userCount }}</div>
                                @if (!$result['passed'])
                                    <pre class="mt-2 max-h-48 overflow-auto rounded-lg bg-black/50 p-2 text-[11px] leading-4 text-zinc-100">{{ json_encode($result['user_result']->take(3)->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @endif
                            </div>
                            <div class="rounded-xl border border-white/10 bg-black/30 p-3">
                                <div class="text-xs text-zinc-400">Gabarito</div>
                                <div class="mt-1 text-lg font-semibold">{{ $goldCount }}</div>
                                <div class="mt-2 text-xs text-zinc-500">
                                    Dica: ajuste filtros/relacionamentos ate a contagem bater.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
