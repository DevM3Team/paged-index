<?php

namespace M3Team\PagedIndex\Pipes;

use Illuminate\Database\Eloquent\Builder;

final readonly class RelationshipsPipe {
    public function __construct(protected array $relationships) {
    }

    public function handle($builder, \Closure $next) {
        if($builder instanceof Builder && $this->relationships !== [])
            return $next($builder->with($this->relationships));
        return $next($builder);
    }
}
