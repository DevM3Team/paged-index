<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;
use Illuminate\Support\Collection;

class TransformPipe {
    public function __construct(protected string|null $resource = null) {
    }

    public function handle($builder, Closure $next)
    {
        $collection = $builder->get();
        $transformed = $this->resource && method_exists($this->resource, 'collection')
            ? ($this->resource)::collection($collection)
            : $collection;

        return $next($transformed);
    }
}
