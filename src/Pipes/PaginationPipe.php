<?php

namespace M3Team\PagedIndex\Pipes;

class PaginationPipe {
    public function __construct(protected int $pageIndex, protected int $pageSize) {
    }

    public function handle($builder, \Closure $next) {
        $pageSize = $this->pageSize ?? 0;
        if ($pageSize) return $next($builder);
        $pageIndex = $this->pageIndex ?? 0;
        return $next($builder->skip($pageSize * $pageIndex)->limit($pageSize));
    }
}
