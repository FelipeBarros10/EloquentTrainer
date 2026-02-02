<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('en_US');

        $users = User::factory()
            ->count(20)
            ->create()
            ->each(function (User $user) use ($faker) {
                Profile::create([
                    'user_id' => $user->id,
                    'display_name' => $faker->userName,
                    'bio' => $faker->sentence(12),
                    'avatar_url' => $faker->imageUrl(256, 256, 'people', true),
                    'birthdate' => $faker->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),
                    'country' => $faker->countryCode,
                    'language' => $faker->languageCode,
                    'timezone' => $faker->timezone,
                    'is_public' => $faker->boolean(80),
                ]);
            });

        $genreSeed = [
            'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary',
            'Drama', 'Fantasy', 'Horror', 'Romance', 'Sci-Fi', 'Thriller',
        ];

        $genres = collect($genreSeed)->map(function (string $name) {
            return Genre::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Stories anchored in {$name} themes.",
            ]);
        });

        $directorSeed = [
            'Ava Moreno', 'Lucas Aoki', 'Nadia Alvarez', 'Ethan Cole', 'Sofia Laurent',
            'Hassan Idris', 'Priya Desai', 'Jonas Richter', 'Marina Petrov', 'Diego Sato',
        ];

        $directors = collect($directorSeed)->map(function (string $name) use ($faker) {
            return Director::create([
                'name' => $name,
                'bio' => $faker->paragraphs(2, true),
                'birthdate' => $faker->dateTimeBetween('-70 years', '-30 years')->format('Y-m-d'),
                'country' => $faker->countryCode,
                'imdb_id' => 'nm' . $faker->unique()->numberBetween(1000000, 9999999),
            ]);
        });

        $actors = collect(range(1, 28))->map(function () use ($faker) {
            return Actor::create([
                'name' => $faker->name(),
                'bio' => $faker->paragraphs(2, true),
                'birthdate' => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'country' => $faker->countryCode,
                'imdb_id' => 'nm' . $faker->unique()->numberBetween(1000000, 9999999),
            ]);
        });

        $movieTitles = [
            'Neon Tides', 'Glass Horizon', 'Echoes of Orion', 'Midnight Carousel',
            'Crimson Atlas', 'Luminous Divide', 'Velvet Corridor', 'Iron Garden',
            'Sable Meridian', 'The Silent Orbit', 'Cinder Vale', 'City of Halos',
            'Arctic Signal', 'Sunken Reverie', 'The Hollow Choir', 'Quantum Harbor',
            'Paper Meteors', 'Atlas of Dust', 'The Last Mirage', 'Rift of Ember',
        ];

        $curatedSeed = [
            [
                'title' => 'Eloquent Dawn',
                'release_year' => 2024,
                'release_date' => '2024-06-14',
                'is_streaming' => true,
                'streaming_start_date' => '2024-07-01',
                'primary_genre' => 'sci-fi',
                'secondary_genres' => ['action', 'thriller'],
                'director' => $directors->first(),
                'rating_min' => 4,
                'rating_max' => 5,
            ],
            [
                'title' => 'Streaming Atlas',
                'release_year' => 2024,
                'release_date' => '2024-03-20',
                'is_streaming' => true,
                'streaming_start_date' => '2024-04-05',
                'primary_genre' => 'sci-fi',
                'secondary_genres' => ['adventure'],
                'director' => $directors->first(),
                'rating_min' => 4,
                'rating_max' => 5,
            ],
            [
                'title' => 'Pivot Dreams',
                'release_year' => 2018,
                'release_date' => '2018-09-11',
                'is_streaming' => false,
                'streaming_start_date' => null,
                'primary_genre' => 'drama',
                'secondary_genres' => ['romance'],
                'director' => $directors->get(3),
                'rating_min' => 3,
                'rating_max' => 5,
            ],
        ];

        $curatedMovies = collect($curatedSeed)->map(function (array $seed) use ($faker) {
            return [
                'meta' => $seed,
                'movie' => Movie::create([
                    'director_id' => $seed['director']->id,
                    'title' => $seed['title'],
                    'slug' => Str::slug($seed['title']),
                    'synopsis' => $faker->paragraphs(3, true),
                    'release_year' => $seed['release_year'],
                    'release_date' => $seed['release_date'],
                    'runtime_minutes' => $faker->numberBetween(95, 165),
                    'language' => $faker->languageCode,
                    'country' => $faker->countryCode,
                    'age_rating' => Arr::random(['PG', 'PG-13', 'R']),
                    'is_streaming' => $seed['is_streaming'],
                    'streaming_start_date' => $seed['streaming_start_date'],
                    'budget' => $faker->numberBetween(12000000, 85000000),
                    'revenue' => $faker->numberBetween(45000000, 280000000),
                    'imdb_id' => 'tt' . $faker->unique()->numberBetween(1000000, 9999999),
                    'avg_rating' => 0,
                    'ratings_count' => 0,
                ]),
            ];
        });

        $randomMovies = collect($movieTitles)->map(function (string $title) use ($faker, $directors) {
            $year = $faker->numberBetween(1995, 2025);
            $releaseDate = $faker->dateTimeBetween("{$year}-01-01", "{$year}-12-31")->format('Y-m-d');
            $isStreaming = $faker->boolean(65);
            $streamingStart = $isStreaming ? $faker->optional()->dateTimeBetween('-2 years', 'now') : null;

            return Movie::create([
                'director_id' => $directors->random()->id,
                'title' => $title,
                'slug' => Str::slug($title . '-' . $faker->unique()->numberBetween(1, 999)),
                'synopsis' => $faker->paragraphs(3, true),
                'release_year' => $year,
                'release_date' => $releaseDate,
                'runtime_minutes' => $faker->numberBetween(85, 180),
                'language' => $faker->languageCode,
                'country' => $faker->countryCode,
                'age_rating' => Arr::random(['G', 'PG', 'PG-13', 'R', 'NC-17']),
                'is_streaming' => $isStreaming,
                'streaming_start_date' => optional($streamingStart)->format('Y-m-d'),
                'budget' => $faker->numberBetween(500000, 250000000),
                'revenue' => $faker->numberBetween(1000000, 600000000),
                'imdb_id' => 'tt' . $faker->unique()->numberBetween(1000000, 9999999),
                'avg_rating' => 0,
                'ratings_count' => 0,
            ]);
        });

        $randomMovies->each(function (Movie $movie) use ($genres, $actors, $faker) {
            $genreSelection = $genres->random($faker->numberBetween(2, 4));
            $movie->genres()->attach(
                $genreSelection->mapWithKeys(function (Genre $genre, int $index) {
                    return [
                        $genre->id => [
                            'is_primary' => $index === 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ];
                })->toArray()
            );

            $actorSelection = $actors->random($faker->numberBetween(3, 6))->values();
            $movie->actors()->attach(
                $actorSelection->mapWithKeys(function (Actor $actor, int $index) use ($faker) {
                    return [
                        $actor->id => [
                            'role_name' => $faker->firstName() . ' ' . $faker->lastName(),
                            'billing_order' => $index + 1,
                            'is_lead' => $index < 2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ];
                })->toArray()
            );
        });

        $genresBySlug = $genres->keyBy('slug');

        $curatedMovies->each(function (array $curated) use ($genresBySlug, $actors, $faker) {
            /** @var Movie $movie */
            $movie = $curated['movie'];
            $meta = $curated['meta'];

            $primary = $genresBySlug->get($meta['primary_genre']);
            $secondary = collect($meta['secondary_genres'])
                ->map(fn (string $slug) => $genresBySlug->get($slug))
                ->filter();

            $genrePayload = collect([$primary])->merge($secondary)->values()->mapWithKeys(function (Genre $genre, int $index) {
                return [
                    $genre->id => [
                        'is_primary' => $index === 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];
            })->toArray();

            $movie->genres()->attach($genrePayload);

            $actorSelection = $actors->random(4)->values();
            $movie->actors()->attach(
                $actorSelection->mapWithKeys(function (Actor $actor, int $index) use ($faker) {
                    return [
                        $actor->id => [
                            'role_name' => $faker->firstName() . ' ' . $faker->lastName(),
                            'billing_order' => $index + 1,
                            'is_lead' => $index < 2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ];
                })->toArray()
            );
        });

        $movies = $randomMovies->merge($curatedMovies->pluck('movie'));

        $plans = [
            ['name' => 'Starter', 'price_cents' => 899],
            ['name' => 'Cinephile', 'price_cents' => 1499],
            ['name' => 'Studio+', 'price_cents' => 2499],
        ];

        $statuses = ['active', 'trialing', 'paused', 'canceled', 'expired'];

        $users->random(14)->each(function (User $user) use ($faker, $plans, $statuses) {
            $plan = Arr::random($plans);

            Subscription::create([
                'user_id' => $user->id,
                'plan_name' => $plan['name'],
                'price_cents' => $plan['price_cents'],
                'currency' => 'USD',
                'status' => Arr::random($statuses),
                'provider' => Arr::random(['stripe', 'paypal', 'in_app']),
                'external_id' => Str::uuid()->toString(),
                'auto_renew' => $faker->boolean(70),
                'started_at' => $faker->dateTimeBetween('-12 months', '-1 month'),
                'renews_at' => $faker->optional()->dateTimeBetween('now', '+3 months'),
                'ends_at' => $faker->optional(0.4)->dateTimeBetween('now', '+6 months'),
                'last_billed_at' => $faker->optional()->dateTimeBetween('-2 months', 'now'),
            ]);
        });

        $movies->each(function (Movie $movie) use ($users, $faker, $curatedMovies) {
            $curated = $curatedMovies->firstWhere('movie.id', $movie->id);
            $curatedMeta = $curated ? $curated['meta'] : null;
            $ratingMin = $curatedMeta['rating_min'] ?? 2;
            $ratingMax = $curatedMeta['rating_max'] ?? 5;
            $reviewers = $users->random($faker->numberBetween(2, 6));

            foreach ($reviewers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'reviewable_id' => $movie->id,
                    'reviewable_type' => Movie::class,
                    'rating' => $faker->numberBetween($ratingMin, $ratingMax),
                    'title' => $faker->sentence(6),
                    'body' => $faker->paragraphs(2, true),
                    'is_spoiler' => $faker->boolean(15),
                    'helpful_count' => $faker->numberBetween(0, 120),
                ]);
            }

            $ratings = $movie->reviews()->pluck('rating');
            $movie->update([
                'avg_rating' => $ratings->count() ? round($ratings->avg(), 2) : 0,
                'ratings_count' => $ratings->count(),
            ]);
        });

        $directors->random(6)->each(function (Director $director) use ($users, $faker) {
            $reviewers = $users->random($faker->numberBetween(1, 4));

            foreach ($reviewers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'reviewable_id' => $director->id,
                    'reviewable_type' => Director::class,
                    'rating' => $faker->numberBetween(3, 5),
                    'title' => $faker->sentence(5),
                    'body' => $faker->paragraphs(1, true),
                    'is_spoiler' => false,
                    'helpful_count' => $faker->numberBetween(0, 60),
                ]);
            }
        });

        $actors->random(10)->each(function (Actor $actor) use ($users, $faker) {
            $reviewers = $users->random($faker->numberBetween(1, 3));

            foreach ($reviewers as $user) {
                Review::create([
                    'user_id' => $user->id,
                    'reviewable_id' => $actor->id,
                    'reviewable_type' => Actor::class,
                    'rating' => $faker->numberBetween(2, 5),
                    'title' => $faker->sentence(4),
                    'body' => $faker->paragraphs(1, true),
                    'is_spoiler' => false,
                    'helpful_count' => $faker->numberBetween(0, 40),
                ]);
            }
        });
    }
}
