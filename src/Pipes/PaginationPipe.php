<?php

namespace M3Team\PagedIndex\Pipes;

use Closure;

final readonly class PaginationPipe {
    public function __construct(protected int $pageIndex, protected int $pageSize) {
    }

    public function handle($builder, Closure $next) {
        $pageSize = max(0, $this->pageSize);
        if ($pageSize === 0) return $next($builder);
        $pageIndex = max(0, $this->pageIndex);
        return $next($builder->skip($pageSize * $pageIndex)->limit($pageSize));
    }
}
