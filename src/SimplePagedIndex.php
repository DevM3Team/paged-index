<?php

namespace M3Team\PagedIndex;

use Illuminate\Support\Collection;

/**
 * Paged index che si occupa solamente di paginare una collection senza opeare operazioni di ordinamento e/o filtro
 */
class SimplePagedIndex extends PagedIndex
{

    /**
     * @inheritDoc
     */
    protected function sort(): Collection
    {
        return $this->collection;
    }

    /**
     * @inheritDoc
     */
    protected function filter(): Collection
    {
        return $this->collection;
    }
}