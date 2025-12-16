<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;
use Illuminate\Http\Resources\Json\JsonResource;

class TransformPipe {
    /**
     * @param class-string|null $resource
     */
    public function __construct(protected string|null $resource = null) {
    }

    public function handle($builder, Closure $next)
    {
        $collection = $builder->get();
        $transformed = $this->resource !== null && is_subclass_of($this->resource, JsonResource::class)
            ? ($this->resource)::collection($collection)
            : $collection;

        return $next($transformed);
    }
}
