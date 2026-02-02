<?php

namespace App\Challenges;

final class SchemaCatalog
{
    /**
     * @return array<string, array{
     *   table: string,
     *   columns: array<int, array{name: string, type: string, notes?: string}>
     * }>
     */
    public static function models(): array
    {
        return [
            'User' => [
                'table' => 'users',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'email', 'type' => 'string', 'notes' => 'unique'],
                    ['name' => 'email_verified_at', 'type' => 'timestamp nullable'],
                    ['name' => 'password', 'type' => 'string'],
                    ['name' => 'remember_token', 'type' => 'string nullable'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Profile' => [
                'table' => 'profiles',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'user_id', 'type' => 'bigint unsigned', 'notes' => 'FK users.id (unique)'],
                    ['name' => 'display_name', 'type' => 'string nullable'],
                    ['name' => 'bio', 'type' => 'text nullable'],
                    ['name' => 'avatar_url', 'type' => 'string nullable'],
                    ['name' => 'birthdate', 'type' => 'date nullable'],
                    ['name' => 'country', 'type' => 'string(2) nullable'],
                    ['name' => 'language', 'type' => 'string(5) nullable'],
                    ['name' => 'timezone', 'type' => 'string(64) nullable'],
                    ['name' => 'is_public', 'type' => 'boolean', 'notes' => 'default true'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Director' => [
                'table' => 'directors',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'bio', 'type' => 'text nullable'],
                    ['name' => 'birthdate', 'type' => 'date nullable'],
                    ['name' => 'country', 'type' => 'string(2) nullable'],
                    ['name' => 'imdb_id', 'type' => 'string nullable', 'notes' => 'unique'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Actor' => [
                'table' => 'actors',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'name', 'type' => 'string'],
                    ['name' => 'bio', 'type' => 'text nullable'],
                    ['name' => 'birthdate', 'type' => 'date nullable'],
                    ['name' => 'country', 'type' => 'string(2) nullable'],
                    ['name' => 'imdb_id', 'type' => 'string nullable', 'notes' => 'unique'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Genre' => [
                'table' => 'genres',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'name', 'type' => 'string', 'notes' => 'unique'],
                    ['name' => 'slug', 'type' => 'string', 'notes' => 'unique'],
                    ['name' => 'description', 'type' => 'text nullable'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Movie' => [
                'table' => 'movies',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'director_id', 'type' => 'bigint unsigned', 'notes' => 'FK directors.id'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'slug', 'type' => 'string', 'notes' => 'unique'],
                    ['name' => 'synopsis', 'type' => 'text nullable'],
                    ['name' => 'release_year', 'type' => 'smallint unsigned', 'notes' => 'indexed'],
                    ['name' => 'release_date', 'type' => 'date nullable'],
                    ['name' => 'runtime_minutes', 'type' => 'smallint unsigned nullable'],
                    ['name' => 'language', 'type' => 'string(5) nullable'],
                    ['name' => 'country', 'type' => 'string(2) nullable'],
                    ['name' => 'age_rating', 'type' => 'string(10) nullable'],
                    ['name' => 'is_streaming', 'type' => 'boolean', 'notes' => 'default false'],
                    ['name' => 'streaming_start_date', 'type' => 'date nullable'],
                    ['name' => 'budget', 'type' => 'bigint unsigned nullable'],
                    ['name' => 'revenue', 'type' => 'bigint unsigned nullable'],
                    ['name' => 'imdb_id', 'type' => 'string nullable', 'notes' => 'unique'],
                    ['name' => 'avg_rating', 'type' => 'decimal(3,2)', 'notes' => 'default 0'],
                    ['name' => 'ratings_count', 'type' => 'int unsigned', 'notes' => 'default 0'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Subscription' => [
                'table' => 'subscriptions',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'user_id', 'type' => 'bigint unsigned', 'notes' => 'FK users.id'],
                    ['name' => 'plan_name', 'type' => 'string'],
                    ['name' => 'price_cents', 'type' => 'int unsigned'],
                    ['name' => 'currency', 'type' => 'string(3)', 'notes' => 'default USD'],
                    ['name' => 'status', 'type' => 'string(20)', 'notes' => 'indexed'],
                    ['name' => 'provider', 'type' => 'string nullable'],
                    ['name' => 'external_id', 'type' => 'string nullable'],
                    ['name' => 'auto_renew', 'type' => 'boolean', 'notes' => 'default true'],
                    ['name' => 'started_at', 'type' => 'timestamp nullable'],
                    ['name' => 'renews_at', 'type' => 'timestamp nullable'],
                    ['name' => 'ends_at', 'type' => 'timestamp nullable'],
                    ['name' => 'last_billed_at', 'type' => 'timestamp nullable'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'Review' => [
                'table' => 'reviews',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'user_id', 'type' => 'bigint unsigned', 'notes' => 'FK users.id'],
                    ['name' => 'reviewable_id', 'type' => 'bigint unsigned', 'notes' => 'morph'],
                    ['name' => 'reviewable_type', 'type' => 'string', 'notes' => 'morph'],
                    ['name' => 'rating', 'type' => 'tinyint unsigned'],
                    ['name' => 'title', 'type' => 'string nullable'],
                    ['name' => 'body', 'type' => 'text nullable'],
                    ['name' => 'is_spoiler', 'type' => 'boolean', 'notes' => 'default false'],
                    ['name' => 'helpful_count', 'type' => 'int unsigned', 'notes' => 'default 0'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            // Pivot tables (not models, but useful in challenges).
            'actor_movie (pivot)' => [
                'table' => 'actor_movie',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'actor_id', 'type' => 'bigint unsigned', 'notes' => 'FK actors.id'],
                    ['name' => 'movie_id', 'type' => 'bigint unsigned', 'notes' => 'FK movies.id'],
                    ['name' => 'role_name', 'type' => 'string nullable'],
                    ['name' => 'billing_order', 'type' => 'smallint unsigned nullable'],
                    ['name' => 'is_lead', 'type' => 'boolean', 'notes' => 'default false'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
            'genre_movie (pivot)' => [
                'table' => 'genre_movie',
                'columns' => [
                    ['name' => 'id', 'type' => 'bigint unsigned', 'notes' => 'PK'],
                    ['name' => 'genre_id', 'type' => 'bigint unsigned', 'notes' => 'FK genres.id'],
                    ['name' => 'movie_id', 'type' => 'bigint unsigned', 'notes' => 'FK movies.id'],
                    ['name' => 'is_primary', 'type' => 'boolean', 'notes' => 'default false'],
                    ['name' => 'created_at', 'type' => 'timestamp nullable'],
                    ['name' => 'updated_at', 'type' => 'timestamp nullable'],
                ],
            ],
        ];
    }

    /**
     * @return array{table: string, columns: array<int, array{name: string, type: string, notes?: string}>}|null
     */
    public static function forModel(string $modelName): ?array
    {
        $all = self::models();

        return $all[$modelName] ?? null;
    }
}

