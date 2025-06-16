<?php

namespace M3Team\PagedIndex\Pipes;

use Illuminate\Contracts\Database\Query\Builder;

class FilterPipe {
    /**
     * @param array<string, mixed> $filters         User-supplied values (e.g. from request)
     * @param array<string, string|callable(Builder, mixed): void> $filterables
     */
    public function __construct(
        protected array $filters = [],
        protected array $filterables = []
    ) {}

    public function handle($builder, \Closure $next)
    {
        foreach ($this->filters as $key => $value) {
            if (!array_key_exists($key, $this->filterables) || $value === null) {
                continue;
            }

            $config = $this->filterables[$key];

            if (is_callable($config)) {
                $config($builder, $value); // custom logic
            } else {
                $builder->where($config, $value); // simple column = value
            }
        }

        return $next($builder);
    }
}
