<?php

namespace M3Team\PagedIndex\Pipes;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Arr;

final readonly class FilterPipe {
    /**
     * @param array<string, mixed> $filters         User-supplied values (e.g. from request)
     * @param array<string, string|callable(Builder, mixed): void> $filterables
     */
    public function __construct(
        protected array $filters = [],
        protected array $filterables = []
    ) {
    }

    public function handle($builder, \Closure $next)
    {
        $filterables = Arr::mapWithKeys($this->filterables, fn ($value, $key) => is_int($key) && is_string($value) ? [$value => $value] : [$key => $value]);

        foreach ($this->filters as $key => $value) {
            if (!array_key_exists($key, $filterables) || $value === null) {
                continue;
            }

            $config = $filterables[$key];

            if (is_callable($config)) {
                $config($builder, $value);
            } else {
                $builder->where($config, $value);
            }
        }

        return $next($builder);
    }
}
