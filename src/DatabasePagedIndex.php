<?php

namespace M3Team\PagedIndex;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;

class DatabasePagedIndex
{
    public const PAGE_INDEX = 'page_index';
    public const PAGE_SIZE = 'page_size';
    public const FILTER = 'filter';
    public const SORT_COLUMN = 'sort_column';
    public const SORT_DIRECTION = 'sort_direction';

    /** @var class-string|null */
    protected string|null $resource = null;
    protected int $pageIndex, $pageSize;
    protected mixed $sortColumn;
    protected ?string $filter;
    protected ?string $sortDirection;

    /**
     * The number of models to skip according to page's size and page's index
     *
     * Specifica come si sceglie quanti modelli skippare in base alla pagina
     * @return int
     */
    protected function getSkip(): int
    {
        try {
            return $this->pageIndex * $this->pageSize;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function __construct(protected Builder $builder)
    {
        $this->pageIndex = request()->get(self::PAGE_INDEX, 0);
        $this->pageSize = request()->get(self::PAGE_SIZE, 0);
        $this->filter = request()->get(self::FILTER, null);
        $this->sortColumn = request()->get(self::SORT_COLUMN, 0);
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
    }

    protected function sort(): Builder
    {
        return $this->builder->orderBy($this->sortColumn, $this->sortDirection);
    }

    protected function filter(): Builder
    {
        return $this->builder;
    }

    protected function page(): Builder
    {
        return $this->pageSize != 0
            ? $this->builder->skip($this->getSkip())->limit($this->pageSize) :
            $this->builder;
    }

    public function getObjects(): PagedIndexCollection
    {
        $this->sort();
        $this->filter();
        $count = $this->builder->clone()->count();
        $this->page();
        $collection = $this->builder->get();
        return new PagedIndexCollection(
            $this->resource === null
                ? $collection
                : ($this->resource)::collection($collection),
            $count,
            $this->pageIndex,
            $this->pageSize
        );
    }

    public function toJson($options = 0): string
    {
        return $this->getObjects()->toJson($options);
    }
}