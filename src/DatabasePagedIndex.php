<?php

namespace M3Team\PagedIndex;

use Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;
use M3Team\PagedIndex\Pipes\PaginationPipe;

/**
 * @deprecated since version `5.0.0`. Will be removed in version 7.
 * Use `PagedIndex` instead.
 */
class DatabasePagedIndex implements Jsonable {
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

    protected function getSkip(): int
    {
        try {
            return $this->pageIndex * $this->pageSize;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function __construct(protected QueryBuilder|EloquentBuilder $builder)
    {
        $this->pageIndex = request()->get(self::PAGE_INDEX, 0);
        $this->pageSize = request()->get(self::PAGE_SIZE, 0);
        $this->filter = request()->get(self::FILTER, null);
        $this->sortColumn = request()->get(self::SORT_COLUMN, 'id');
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
    }

    protected function sort(): QueryBuilder|EloquentBuilder
    {
        return $this->builder->orderBy($this->sortColumn, $this->sortDirection);
    }

    protected function filter(): QueryBuilder|EloquentBuilder
    {
        return $this->builder;
    }

    protected function page(): QueryBuilder|EloquentBuilder
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
