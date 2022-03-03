<?php

namespace M3Team\PagedIndex;

use Illuminate\Support\Collection;

/**
 * This type of paged index deals only with page's size and page's index. It does not make any sorting or filtering operation
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