<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Profile;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class EloquentJudgeService
{
    private const DISALLOWED_PATTERNS = [
        '/\bDB\s*::/i',
        '/\bconfig\s*\(/i',
        '/\bConfig\s*::/i',
        '/\bupdate\b/i',
        '/\bdelete\b/i',
        '/\binsert\b/i',
        '/\btruncate\b/i',
        '/\bdrop\b/i',
        '/\balter\b/i',
        '/\bcreate\b/i',
        '/\braw\b/i',
        '/\bwhereRaw\b/i',
        '/\bselectRaw\b/i',
        '/\borderByRaw\b/i',
        '/\bhavingRaw\b/i',
        '/\bgroupByRaw\b/i',
        '/\bstatement\b/i',
        '/\bunprepared\b/i',
        '/\battach\b/i',
        '/\bdetach\b/i',
        '/\bsync\b/i',
        '/\bexec\b/i',
        '/\bshell_exec\b/i',
        '/\bpassthru\b/i',
        '/\bproc_open\b/i',
        '/`/',
    ];

    public function evaluate(string $userCode, callable $goldQuery): array
    {
        $this->ensureSafeQuery($userCode);
        $this->bootSandboxAliases();

        DB::beginTransaction();

        try {
            [$userResult, $userSql, $userBindings] = $this->runUserQuery($userCode);
            $goldResult = $this->runGoldQuery($goldQuery);

            $userCollection = $this->normalizeResult($userResult);
            $goldCollection = $this->normalizeResult($goldResult);

            $passed = $this->compareCollections($userCollection, $goldCollection);

            return [
                'passed' => $passed,
                'user_sql' => $userSql,
                'user_bindings' => $userBindings,
                'user_result' => $userCollection,
                'gold_result' => $goldCollection,
                'error' => null,
            ];
        } catch (Throwable $exception) {
            return [
                'passed' => false,
                'user_sql' => null,
                'user_bindings' => [],
                'user_result' => collect(),
                'gold_result' => collect(),
                'error' => $exception->getMessage(),
            ];
        } finally {
            DB::rollBack();
        }
    }

    private function runUserQuery(string $userCode): array
    {
        $connection = DB::connection();
        $connection->flushQueryLog();
        $connection->enableQueryLog();

        $result = $this->evalUserCode($userCode);
        $sql = null;
        $bindings = [];

        if ($result instanceof Relation) {
            $sql = $result->toSql();
            $bindings = $result->getBindings();
            $result = $result->get();
        } elseif ($result instanceof EloquentBuilder || $result instanceof QueryBuilder) {
            $sql = $result->toSql();
            $bindings = $result->getBindings();
            $result = $result->get();
        }

        $queryLog = $connection->getQueryLog();
        $connection->disableQueryLog();
        $connection->flushQueryLog();

        if (!$sql && !empty($queryLog)) {
            $last = $queryLog[array_key_last($queryLog)];
            $sql = $last['query'] ?? null;
            $bindings = $last['bindings'] ?? [];
        }

        return [$result, $sql, $bindings];
    }

    private function runGoldQuery(callable $goldQuery): mixed
    {
        $result = $goldQuery();

        if ($result instanceof Relation || $result instanceof EloquentBuilder || $result instanceof QueryBuilder) {
            return $result->get();
        }

        return $result;
    }

    private function evalUserCode(string $userCode): mixed
    {
        $code = trim($userCode);

        if (!Str::startsWith($code, 'return ')) {
            $code = 'return ' . rtrim($code, ';') . ';';
        }

        return eval($code);
    }

    private function normalizeResult(mixed $result): Collection
    {
        if ($result instanceof EloquentCollection || $result instanceof Collection) {
            return $result->values();
        }

        if ($result instanceof Model) {
            return collect([$result]);
        }

        if (is_array($result)) {
            return collect($result)->values();
        }

        return collect([$result]);
    }

    private function compareCollections(Collection $left, Collection $right): bool
    {
        return $this->serializeCollection($left) === $this->serializeCollection($right);
    }

    private function serializeCollection(Collection $collection): array
    {
        return $collection->map(function ($item) {
            if ($item instanceof Model) {
                return $item->getAttributes();
            }

            if ($item instanceof Arrayable) {
                return $item->toArray();
            }

            if (is_object($item)) {
                return (array) $item;
            }

            return $item;
        })->values()->toArray();
    }

    private function ensureSafeQuery(string $userCode): void
    {
        foreach (self::DISALLOWED_PATTERNS as $pattern) {
            if (preg_match($pattern, $userCode)) {
                throw new \InvalidArgumentException('Consulta bloqueada por seguranca. Use apenas consultas de leitura com Eloquent.');
            }
        }
    }

    private function bootSandboxAliases(): void
    {
        $aliases = [
            'User' => User::class,
            'Profile' => Profile::class,
            'Movie' => Movie::class,
            'Director' => Director::class,
            'Actor' => Actor::class,
            'Genre' => Genre::class,
            'Subscription' => Subscription::class,
            'Review' => Review::class,
        ];

        foreach ($aliases as $alias => $class) {
            if (!class_exists($alias)) {
                class_alias($class, $alias);
            }
        }
    }
}
