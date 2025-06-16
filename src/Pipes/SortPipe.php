<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;

class SortPipe {
    public function __construct(protected string $sortKey, protected string $sortDirection = 'asc', protected array $sortable = []) {
    }

    public function handle($builder, Closure $next) {
        $direction = strtolower($this->sortDirection) === 'desc' ? 'desc' : 'asc';
        if (!isset($this->sortable[$this->sortKey])) return $next($builder);
        $value = $this->sortable[$this->sortKey];
        if (is_callable($value)) {
            $value($builder, $direction);
        } else {
            $builder->orderBy($value, $direction);
        }
        return $next($builder);
    }
}
