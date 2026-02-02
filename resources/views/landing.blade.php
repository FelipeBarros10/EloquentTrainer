@extends('layouts.app')

@section('title', 'EloquentTrainer - Treino de Eloquent ORM')
@section('meta_description', 'Aprenda Eloquent ORM na pratica com desafios progressivos, SQL explainer e pontuacao por usuario. Feito em Laravel.')
@section('canonical', route('home'))

@section('content')
    <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-white/10 to-white/5 p-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full bg-black/40 px-3 py-1 text-xs text-zinc-200 ring-1 ring-white/10">
                Laravel + Eloquent ORM
            </div>
            <h1 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                Treine Eloquent com feedback imediato
            </h1>
            <p class="mt-4 text-sm leading-6 text-zinc-300 sm:text-base">
                Resolva desafios reais de consultas, relacionamentos e agregacoes. Veja o SQL gerado, compare resultados e acumule pontos por usuario.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                @guest
                    <a href="{{ route('register') }}" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-zinc-100">
                        Criar conta
                    </a>
                    <a href="{{ route('login') }}" class="rounded-xl border border-white/15 bg-transparent px-4 py-2 text-sm font-semibold text-zinc-200 hover:bg-white/5">
                        Entrar
                    </a>
                @endguest

                @auth
                    <a href="{{ route('challenges.index') }}" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-zinc-100">
                        Ir para desafios
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm font-semibold text-zinc-200">SQL Explainer</div>
            <div class="mt-2 text-sm leading-6 text-zinc-300">
                Veja a query gerada para entender como o Eloquent traduz sua intencao.
            </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm font-semibold text-zinc-200">Progresso por usuario</div>
            <div class="mt-2 text-sm leading-6 text-zinc-300">
                Pontuacao persistida no banco, com tentativas e historico da ultima solucao.
            </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm font-semibold text-zinc-200">Desafios progressivos</div>
            <div class="mt-2 text-sm leading-6 text-zinc-300">
                Do basico ao avancado: filtros, relacionamentos, pivot, morph e agregacoes.
            </div>
        </div>
    </div>

    <div class="mt-10 rounded-2xl border border-white/10 bg-black/30 p-5">
        <div class="text-sm font-semibold text-zinc-200">Como funciona</div>
        <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-zinc-300">
            <li>Voce escreve uma expressao Eloquent (ex: <code class="rounded bg-black/40 px-1 py-0.5">Movie::query()->where(...)</code>).</li>
            <li>O sistema executa em sandbox (transaction + rollback) e compara com o gabarito.</li>
            <li>Se bater, voce ganha pontos e desbloqueia seu progresso.</li>
        </ol>
    </div>
@endsection

