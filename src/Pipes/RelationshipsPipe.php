<?php

namespace M3Team\PagedIndex\Pipes;

class RelationshipsPipe {
    public function __construct(protected array $relationships) {
    }

    public function handle($builder, \Closure $next) {
        return $next($builder->with($this->relationships));
    }
}
