<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

final readonly class RelationshipsPipe
{
    public function __construct(
        protected array $relationships,
        protected array $allowedRelationships = []
    ) {}

    public function handle($builder, Closure $next)
    {
        if (! $builder instanceof Builder || $this->relationships === []) {
            return $next($builder);
        }

        $allowedRelationships = $this->normalizeRelationships($this->allowedRelationships);
        $eagerLoads = [];

        foreach ($this->relationships as $relationship) {
            if (! is_string($relationship) || ! array_key_exists($relationship, $allowedRelationships)) {
                continue;
            }

            $allowed = $allowedRelationships[$relationship];

            if (is_callable($allowed)) {
                $eagerLoads[$relationship] = $allowed;

                continue;
            }

            $eagerLoads[] = $allowed;
        }

        if ($eagerLoads !== []) {
            return $next($builder->with($eagerLoads));
        }

        return $next($builder);
    }

    private function normalizeRelationships(array $relationships): array
    {
        return Arr::mapWithKeys($relationships, static function ($value, $key) {
            if (is_int($key) && is_string($value)) {
                return [$value => $value];
            }

            if (is_string($key)) {
                return [$key => $value];
            }

            return [];
        });
    }
}
