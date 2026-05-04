<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;
use Illuminate\Support\Arr;

final readonly class SortPipe {
    public function __construct(protected string $sortKey, protected string $sortDirection = 'asc', protected array $sortable = []) {
    }

    public function handle($builder, Closure $next) {
        $sortable = Arr::mapWithKeys($this->sortable, fn($value, $key) => is_int($key) && is_string($value) ? [$value => $value] : [$key => $value]);
        $direction = strtolower($this->sortDirection) === 'desc' ? 'desc' : 'asc';
        if (!isset($sortable[$this->sortKey])) return $next($builder);
        $value = $sortable[$this->sortKey];
        if (is_callable($value)) {
            $value($builder, $direction);
        } else {
            $builder->orderBy($value, $direction);
        }
        return $next($builder);
    }
}
