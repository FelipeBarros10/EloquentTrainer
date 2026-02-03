<?php

namespace App\Challenges;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;

final class ChallengeRepository
{
    /**
     * @return array<int, array{
     *   id: string,
     *   title: string,
     *   difficulty: 'easy'|'medium'|'hard',
     *   points: int,
     *   story: string,
     *   models: array<int, string>,
     *   starter_code: string,
     *   gold: callable(): mixed,
     * }>
     */
    public static function all(): array
    {
        $challenges = [];

        $add = function (array $challenge) use (&$challenges): void {
            $challenges[] = $challenge;
        };

        // Base set (kept IDs stable), but starter_code is a neutral scaffold (no "answer" prefilled).
        $add([
            'id' => 'easy-01',
            'title' => 'Catalogo 2024 em Streaming',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Um editor quer listar os filmes lancados em 2024 que ja estao disponiveis em streaming. Ordene por id (asc).',
            'models' => ['Movie'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->where('release_year', 2024)
                ->where('is_streaming', true)
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'easy-02',
            'title' => 'Diretores Prolificos',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'A curadoria quer destacar diretores com pelo menos 2 filmes no catalogo. Ordene por movies_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Director', 'Movie'],
            'starter_code' => 'Director::query()',
            'gold' => fn () => Director::query()
                ->whereHas('movies', fn ($q) => $q, '>=', 2)
                ->withCount('movies')
                ->orderByDesc('movies_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'medium-01',
            'title' => 'Sci-Fi Bem Avaliado',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'O time de marketing precisa de uma lista de filmes Sci-Fi com nota media >= 4.0, incluindo o diretor. Ordene por avg_rating (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie', 'Genre', 'Director'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->whereHas('genres', fn ($q) => $q->where('genres.slug', 'sci-fi'))
                ->where('avg_rating', '>=', 4)
                ->with('director')
                ->orderByDesc('avg_rating')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'medium-02',
            'title' => 'Elenco em Destaque',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'O aplicativo quer mostrar os 5 atores com mais papeis de protagonista (pivot is_lead=true) em filmes em streaming. Ordene por lead_streaming_roles_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Actor', 'Movie', 'actor_movie (pivot)'],
            'starter_code' => 'Actor::query()',
            'gold' => fn () => Actor::query()
                ->withCount([
                    'movies as lead_streaming_roles_count' => fn ($q) => $q
                        ->where('movies.is_streaming', true)
                        ->wherePivot('is_lead', true),
                ])
                ->orderByDesc('lead_streaming_roles_count')
                ->orderBy('id')
                ->limit(5),
        ]);

        $add([
            'id' => 'hard-01',
            'title' => 'Assinantes Engajados',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Identifique usuarios com assinatura ativa (subscriptions.status=active) que fizeram pelo menos 3 reviews de filmes com rating >= 4. Ordene por high_movie_reviews_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['User', 'Subscription', 'Review', 'Movie'],
            'starter_code' => 'User::query()',
            'gold' => fn () => User::query()
                ->whereHas('subscriptions', fn ($q) => $q->where('status', 'active'))
                ->whereHas(
                    'reviews',
                    fn ($q) => $q->where('reviewable_type', Movie::class)->where('rating', '>=', 4),
                    '>=',
                    3
                )
                ->withCount([
                    'reviews as high_movie_reviews_count' => fn ($q) => $q->where('reviewable_type', Movie::class)->where('rating', '>=', 4),
                ])
                ->orderByDesc('high_movie_reviews_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'hard-02',
            'title' => 'Reviews Uteis em Streaming',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Destaque filmes em streaming com pelo menos 2 reviews nao-spoiler (is_spoiler=false) e helpful_count > 50. Ordene por helpful_reviews_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie', 'Review'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->where('is_streaming', true)
                ->whereHas(
                    'reviews',
                    fn ($q) => $q->where('is_spoiler', false)->where('helpful_count', '>', 50),
                    '>=',
                    2
                )
                ->withCount([
                    'reviews as helpful_reviews_count' => fn ($q) => $q->where('is_spoiler', false)->where('helpful_count', '>', 50),
                ])
                ->orderByDesc('helpful_reviews_count')
                ->orderBy('id'),
        ]);

        // EASY: filtering + ordering fundamentals.
        $years = [2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025];
        foreach ($years as $i => $year) {
            $idx = $i + 3;
            $add([
                'id' => sprintf('easy-%02d', $idx),
                'title' => "Lancamentos de {$year}",
                'difficulty' => 'easy',
                'points' => 10,
                'story' => "Liste filmes com movies.release_year = {$year}, ordenando por id (asc).",
                'models' => ['Movie'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()->where('release_year', $year)->orderBy('id'),
            ]);
        }

        $add([
            'id' => 'easy-11',
            'title' => 'Streaming com Data de Inicio',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Liste filmes em streaming que possuem streaming_start_date preenchida (nao-null). Ordene por id (asc).',
            'models' => ['Movie'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->where('is_streaming', true)
                ->whereNotNull('streaming_start_date')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'easy-12',
            'title' => 'Filmes com Orçamento',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Liste filmes com budget preenchido (budget nao-null), ordenando por budget desc e id asc.',
            'models' => ['Movie'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->whereNotNull('budget')
                ->orderByDesc('budget')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'easy-13',
            'title' => 'Filmes Longos',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Liste filmes com runtime_minutes >= 150 (ignorando os que estao null). Ordene por runtime_minutes (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->whereNotNull('runtime_minutes')
                ->where('runtime_minutes', '>=', 150)
                ->orderByDesc('runtime_minutes')
                ->orderBy('id'),
        ]);

        $add([
            'id' => 'easy-14',
            'title' => 'Diretores com IMDB ID',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Liste diretores com imdb_id preenchido (nao-null), ordenando por id (asc).',
            'models' => ['Director'],
            'starter_code' => 'Director::query()',
            'gold' => fn () => Director::query()->whereNotNull('imdb_id')->orderBy('id'),
        ]);

        $add([
            'id' => 'easy-15',
            'title' => 'Perfis Publicos',
            'difficulty' => 'easy',
            'points' => 10,
            'story' => 'Liste perfis publicos (profiles.is_public = true), ordenando por id (asc).',
            'models' => ['Profile'],
            'starter_code' => 'Profile::query()',
            'gold' => fn () => Profile::query()->where('is_public', true)->orderBy('id'),
        ]);

        // MEDIUM: whereHas + eager loading + counts.
        $genreSlugs = [
            'action', 'adventure', 'animation', 'comedy', 'crime', 'documentary',
            'drama', 'fantasy', 'horror', 'romance', 'sci-fi', 'thriller',
        ];

        $mediumIndex = 3;
        foreach ($genreSlugs as $slug) {
            $add([
                'id' => sprintf('medium-%02d', $mediumIndex++),
                'title' => 'Catalogo por Genero: ' . strtoupper($slug),
                'difficulty' => 'medium',
                'points' => 50,
                'story' => "Liste filmes que possuem o genero genres.slug = '{$slug}'. Inclua o diretor e ordene por id (asc).",
                'models' => ['Movie', 'Genre', 'Director', 'genre_movie (pivot)'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->whereHas('genres', fn ($q) => $q->where('genres.slug', $slug))
                    ->with('director')
                    ->orderBy('id'),
            ]);
        }

        $add([
            'id' => sprintf('medium-%02d', $mediumIndex++),
            'title' => 'Diretores com Nota Media Boa',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'Liste diretores que possuem pelo menos 1 filme com avg_rating >= 4.0. Ordene por good_movies_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Director', 'Movie'],
            'starter_code' => 'Director::query()',
            'gold' => fn () => Director::query()
                ->whereHas('movies', fn ($q) => $q->where('avg_rating', '>=', 4))
                ->withCount(['movies as good_movies_count' => fn ($q) => $q->where('avg_rating', '>=', 4)])
                ->orderByDesc('good_movies_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('medium-%02d', $mediumIndex++),
            'title' => 'Filmes com Muitos Reviews',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'Liste filmes que possuem 5+ reviews (qualquer rating), com reviews_count. Ordene por reviews_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie', 'Review'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->has('reviews', '>=', 5)
                ->withCount('reviews')
                ->orderByDesc('reviews_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('medium-%02d', $mediumIndex++),
            'title' => 'Atores em Filmes de 2024',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'Liste atores que participam de pelo menos 1 filme com release_year = 2024. Ordene por movies_2024_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Actor', 'Movie', 'actor_movie (pivot)'],
            'starter_code' => 'Actor::query()',
            'gold' => fn () => Actor::query()
                ->whereHas('movies', fn ($q) => $q->where('release_year', 2024))
                ->withCount(['movies as movies_2024_count' => fn ($q) => $q->where('release_year', 2024)])
                ->orderByDesc('movies_2024_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('medium-%02d', $mediumIndex++),
            'title' => 'Assinaturas Ativas (Usuarios)',
            'difficulty' => 'medium',
            'points' => 50,
            'story' => 'Liste usuarios que possuem pelo menos 1 assinatura com status = active, com active_subscriptions_count. Ordene por active_subscriptions_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['User', 'Subscription'],
            'starter_code' => 'User::query()',
            'gold' => fn () => User::query()
                ->whereHas('subscriptions', fn ($q) => $q->where('status', 'active'))
                ->withCount(['subscriptions as active_subscriptions_count' => fn ($q) => $q->where('status', 'active')])
                ->orderByDesc('active_subscriptions_count')
                ->orderBy('id'),
        ]);

        // HARD: multi-relations + morph filters + pivot filters.
        $hardIndex = 3;

        $add([
            'id' => sprintf('hard-%02d', $hardIndex++),
            'title' => 'Top Filmes (Streaming + Nota + Reviews)',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Liste filmes em streaming com avg_rating >= 4.0 e pelo menos 3 reviews (rating >= 4), com high_reviews_count. Ordene por high_reviews_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie', 'Review'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->where('is_streaming', true)
                ->where('avg_rating', '>=', 4)
                ->whereHas(
                    'reviews',
                    fn ($q) => $q->where('rating', '>=', 4),
                    '>=',
                    3
                )
                ->withCount(['reviews as high_reviews_count' => fn ($q) => $q->where('rating', '>=', 4)])
                ->orderByDesc('high_reviews_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('hard-%02d', $hardIndex++),
            'title' => 'Diretores Consistentes',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Liste diretores que possuem 2+ filmes e TODOS esses filmes tem is_streaming = true. Ordene por movies_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Director', 'Movie'],
            'starter_code' => 'Director::query()',
            'gold' => fn () => Director::query()
                ->has('movies', '>=', 2)
                ->whereDoesntHave('movies', fn ($q) => $q->where('is_streaming', false))
                ->withCount('movies')
                ->orderByDesc('movies_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('hard-%02d', $hardIndex++),
            'title' => 'Usuarios que Avaliaram Diretores',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Liste usuarios que fizeram pelo menos 2 reviews em diretores (reviewable_type = Director::class), com director_reviews_count. Ordene por director_reviews_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['User', 'Review', 'Director'],
            'starter_code' => 'User::query()',
            'gold' => fn () => User::query()
                ->whereHas(
                    'reviews',
                    fn ($q) => $q->where('reviewable_type', Director::class),
                    '>=',
                    2
                )
                ->withCount([
                    'reviews as director_reviews_count' => fn ($q) => $q->where('reviewable_type', Director::class),
                ])
                ->orderByDesc('director_reviews_count')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('hard-%02d', $hardIndex++),
            'title' => 'Atores Muito Avaliados',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Liste atores com 2+ reviews e rating medio (avg) >= 4.0. Ordene por reviews_avg_rating (desc) e, em caso de empate, por id (asc).',
            'models' => ['Actor', 'Review'],
            'starter_code' => 'Actor::query()',
            'gold' => fn () => Actor::query()
                ->whereHas('reviews', fn ($q) => $q, '>=', 2)
                ->withCount('reviews')
                ->withAvg('reviews as reviews_avg_rating', 'rating')
                ->having('reviews_avg_rating', '>=', 4)
                ->orderByDesc('reviews_avg_rating')
                ->orderBy('id'),
        ]);

        $add([
            'id' => sprintf('hard-%02d', $hardIndex++),
            'title' => 'Filmes com Elenco Lider Forte',
            'difficulty' => 'hard',
            'points' => 100,
            'story' => 'Liste filmes que tem 2+ atores com pivot is_lead=true, com lead_actors_count. Ordene por lead_actors_count (desc) e, em caso de empate, por id (asc).',
            'models' => ['Movie', 'Actor', 'actor_movie (pivot)'],
            'starter_code' => 'Movie::query()',
            'gold' => fn () => Movie::query()
                ->whereHas(
                    'actors',
                    fn ($q) => $q->wherePivot('is_lead', true),
                    '>=',
                    2
                )
                ->withCount([
                    'actors as lead_actors_count' => fn ($q) => $q->wherePivot('is_lead', true),
                ])
                ->orderByDesc('lead_actors_count')
                ->orderBy('id'),
        ]);

        // Ensure at least 50 challenges with extra variations (no "answer" prefill).
        $extras = [
            [
                'id' => 'easy-16',
                'title' => 'Filmes Sem Synopsis',
                'difficulty' => 'easy',
                'points' => 10,
                'story' => 'Liste filmes com synopsis null (sem descricao), ordenando por id (asc).',
                'models' => ['Movie'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()->whereNull('synopsis')->orderBy('id'),
            ],
            [
                'id' => 'easy-17',
                'title' => 'Filmes com Revenue Alto',
                'difficulty' => 'easy',
                'points' => 10,
                'story' => 'Liste filmes com revenue >= 200000000 (ignorando null). Ordene por revenue (desc) e, em caso de empate, por id (asc).',
                'models' => ['Movie'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->whereNotNull('revenue')
                    ->where('revenue', '>=', 200000000)
                    ->orderByDesc('revenue')
                    ->orderBy('id'),
            ],
            [
                'id' => 'easy-18',
                'title' => 'Usuarios com Perfil',
                'difficulty' => 'easy',
                'points' => 10,
                'story' => 'Liste usuarios que possuem profile (hasOne), ordenando por id (asc).',
                'models' => ['User', 'Profile'],
                'starter_code' => 'User::query()',
                'gold' => fn () => User::query()->has('profile')->orderBy('id'),
            ],
            [
                'id' => 'medium-10x',
                'title' => 'Filmes com Genero Primario',
                'difficulty' => 'medium',
                'points' => 50,
                'story' => 'Liste filmes que possuem pelo menos 1 genero com pivot is_primary=true, com primary_genres_count. Ordene por primary_genres_count (desc) e, em caso de empate, por id (asc).',
                'models' => ['Movie', 'Genre', 'genre_movie (pivot)'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->whereHas('genres', fn ($q) => $q->wherePivot('is_primary', true))
                    ->withCount(['genres as primary_genres_count' => fn ($q) => $q->wherePivot('is_primary', true)])
                    ->orderByDesc('primary_genres_count')
                    ->orderBy('id'),
            ],
            [
                'id' => 'medium-11x',
                'title' => 'Filmes com Diretor e Elenco',
                'difficulty' => 'medium',
                'points' => 50,
                'story' => 'Liste filmes em streaming, trazendo (eager) director e actors. Ordene por id (asc).',
                'models' => ['Movie', 'Director', 'Actor'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->where('is_streaming', true)
                    ->with(['director', 'actors'])
                    ->orderBy('id'),
            ],
            [
                'id' => 'medium-12x',
                'title' => 'Usuarios com Reviews Uteis',
                'difficulty' => 'medium',
                'points' => 50,
                'story' => 'Liste usuarios que fizeram 2+ reviews com helpful_count >= 10, com helpful_reviews_count. Ordene por helpful_reviews_count (desc) e, em caso de empate, por id (asc).',
                'models' => ['User', 'Review'],
                'starter_code' => 'User::query()',
                'gold' => fn () => User::query()
                    ->whereHas('reviews', fn ($q) => $q->where('helpful_count', '>=', 10), '>=', 2)
                    ->withCount(['reviews as helpful_reviews_count' => fn ($q) => $q->where('helpful_count', '>=', 10)])
                    ->orderByDesc('helpful_reviews_count')
                    ->orderBy('id'),
            ],
            [
                'id' => 'hard-10x',
                'title' => 'Assinantes com Reviews e Perfil Publico',
                'difficulty' => 'hard',
                'points' => 100,
                'story' => 'Liste usuarios com profile publico (profiles.is_public=true), com assinatura active e 1+ review (qualquer). Ordene por id (asc).',
                'models' => ['User', 'Profile', 'Subscription', 'Review'],
                'starter_code' => 'User::query()',
                'gold' => fn () => User::query()
                    ->whereHas('profile', fn ($q) => $q->where('is_public', true))
                    ->whereHas('subscriptions', fn ($q) => $q->where('status', 'active'))
                    ->has('reviews', '>=', 1)
                    ->with(['profile'])
                    ->orderBy('id'),
            ],
            [
                'id' => 'hard-11x',
                'title' => 'Filmes de Diretores Avaliados',
                'difficulty' => 'hard',
                'points' => 100,
                'story' => 'Liste filmes cujo diretor tem pelo menos 2 reviews (morph: reviews.reviewable_type = Director::class). Ordene por id (asc).',
                'models' => ['Movie', 'Director', 'Review'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->whereHas('director.reviews', fn ($q) => $q->where('reviewable_type', Director::class), '>=', 2)
                    ->with('director')
                    ->orderBy('id'),
            ],
            [
                'id' => 'hard-12x',
                'title' => 'Filmes 2024 com Protagonista e Genero',
                'difficulty' => 'hard',
                'points' => 100,
                'story' => 'Liste filmes de 2024 que tem pelo menos 1 ator protagonista (pivot is_lead=true) e pelo menos 2 generos. Ordene por id (asc).',
                'models' => ['Movie', 'Actor', 'Genre', 'actor_movie (pivot)', 'genre_movie (pivot)'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()
                    ->where('release_year', 2024)
                    ->whereHas('actors', fn ($q) => $q->wherePivot('is_lead', true))
                    ->has('genres', '>=', 2)
                    ->with(['actors', 'genres'])
                    ->orderBy('id'),
            ],
        ];

        foreach ($extras as $extra) {
            $add($extra);
        }

        // If still short (shouldn't be), pad with distinct year-based "streaming" variations.
        $target = 50;
        $padIndex = 1;
        while (count($challenges) < $target) {
            $year = $years[($padIndex - 1) % count($years)];
            $add([
                'id' => sprintf('easy-pad%02d', $padIndex++),
                'title' => "Streaming de {$year}",
                'difficulty' => 'easy',
                'points' => 10,
                'story' => "Liste filmes com is_streaming=true e release_year={$year}, ordenando por id (asc).",
                'models' => ['Movie'],
                'starter_code' => 'Movie::query()',
                'gold' => fn () => Movie::query()->where('is_streaming', true)->where('release_year', $year)->orderBy('id'),
            ]);
        }

        return $challenges;
    }

    /**
     * @return array{
     *   id: string,
     *   title: string,
     *   difficulty: 'easy'|'medium'|'hard',
     *   points: int,
     *   story: string,
     *   models: array<int, string>,
     *   starter_code: string,
     *   gold: callable(): mixed,
     * }|null
     */
    public static function find(string $id): ?array
    {
        foreach (self::all() as $challenge) {
            if ($challenge['id'] === $id) {
                return $challenge;
            }
        }

        return null;
    }

    /**
     * @return array{
     *   id: string,
     *   title: string,
     *   difficulty: 'easy'|'medium'|'hard',
     *   points: int,
     *   story: string,
     *   models: array<int, string>,
     *   starter_code: string,
     *   gold: callable(): mixed,
     * }
     */
    public static function findOrFail(string $id): array
    {
        $challenge = self::find($id);

        if (!$challenge) {
            abort(404);
        }

        return $challenge;
    }
}
