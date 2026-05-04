<?php

namespace M3Team\PagedIndex\Pipes;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;

final readonly class ToPagedIndexCollectionPipe {
    public function __construct(protected int $count, protected int $pageIndex, protected int $pageSize) {
    }

    public function handle(Collection|AnonymousResourceCollection $collection, \Closure $next) {
        return $next(new PagedIndexCollection(
            $collection,
            $this->count,
            $this->pageIndex,
            $this->pageSize
        ));
    }
}
