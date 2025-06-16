<?php

namespace M3Team\PagedIndex\Pipes;

use Illuminate\Support\Collection;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;

class ToPagedIndexCollectionPipe {
    public function __construct(protected int $count, protected int $pageIndex, protected int $pageSize) {
    }

    public function handle(Collection $collection, \Closure $next) {
        return $next(new PagedIndexCollection(
            $collection,
            $this->count,
            $this->pageIndex,
            $this->pageSize
        ));
    }
}
