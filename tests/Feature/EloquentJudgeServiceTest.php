<?php

use App\Models\Director;
use App\Models\Movie;
use App\Services\EloquentJudgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('blocks disallowed patterns', function () {
    $judge = app(EloquentJudgeService::class);

    expect(fn () => $judge->evaluate("DB::table('movies')->get()", fn () => Movie::query()))
        ->toThrow(InvalidArgumentException::class);

    expect(fn () => $judge->evaluate("Movie::query()->update(['title' => 'x'])", fn () => Movie::query()))
        ->toThrow(InvalidArgumentException::class);
});

it('evaluates a simple read-only query and captures sql', function () {
    $director = Director::create([
        'name' => 'Test Director',
    ]);

    Movie::create([
        'director_id' => $director->id,
        'title' => 'A',
        'slug' => 'a',
        'release_year' => 2024,
        'is_streaming' => true,
    ]);

    Movie::create([
        'director_id' => $director->id,
        'title' => 'B',
        'slug' => 'b',
        'release_year' => 2023,
        'is_streaming' => true,
    ]);

    $judge = app(EloquentJudgeService::class);

    $result = $judge->evaluate(
        "Movie::query()->where('release_year', 2024)->orderBy('id')",
        fn () => Movie::query()->where('release_year', 2024)->orderBy('id')
    );

    expect($result['error'])->toBeNull();
    expect($result['passed'])->toBeTrue();
    expect($result['user_sql'])->not->toBeNull();
});

