<?php

namespace M3Team\PagedIndex;

use Closure;
use Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;
use M3Team\PagedIndex\Pipes\PaginationPipe;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template T
 */
abstract class PagedIndex implements Jsonable
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

    public function __construct(protected QueryBuilder|EloquentBuilder $builder) {
        $this->pageIndex = request()->get(self::PAGE_INDEX, 0);
        $this->pageSize = request()->get(self::PAGE_SIZE, 0);
        $this->filter = request()->get(self::FILTER, null);
        $this->sortColumn = request()->get(self::SORT_COLUMN, 'id');
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
    }

    protected function getSortPipe(): callable {
        return function ($builder, \Closure $next) {
            $next($builder->orderBy($this->sortColumn, $this->sortDirection));
        };
    }

    protected function getFilterPipe(): callable {
        return function ($builder, \Closure $next) {
            $next($builder);
        };
    }

    protected function applyPipeline($builder, array $pipes) {
        return app(Pipeline::class)
            ->send($builder)
            ->through($pipes)
            ->thenReturn();
    }

    public function getObjects(): PagedIndexCollection {
        $filtered = $this->applyPipeline($this->builder->clone(), [
            $this->getSortPipe(),
            $this->getFilterPipe()
        ]);
        $count = $filtered->clone()->count();
        $paginated = $this->applyPipeline($filtered, [
            new PaginationPipe($this->pageIndex ?? 0, $this->pageSize ?? 0)
        ]);
        $collection = $paginated->get();
        return new PagedIndexCollection(
            $this->resource === null
                ? $collection
                : ($this->resource)::collection($collection),
            $count,
            $this->pageIndex,
            $this->pageSize
        );
    }

    public function toJson($options = 0): string {
        return $this->getObjects()->toJson($options);
    }

}
