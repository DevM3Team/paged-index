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
use M3Team\PagedIndex\Pipes\FilterPipe;
use M3Team\PagedIndex\Pipes\PaginationPipe;
use M3Team\PagedIndex\Pipes\RelationshipsPipe;
use M3Team\PagedIndex\Pipes\SortPipe;
use M3Team\PagedIndex\Pipes\ToPagedIndexCollectionPipe;
use M3Team\PagedIndex\Pipes\TransformPipe;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template T
 */
class PagedIndex implements Jsonable {
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
    protected ?array $filters;
    protected ?string $sortDirection;
    private array $relationships = [];

    /**
     * @param array{
     *     page_index: int,
     *     page_size: int,
     *     sort_column: string,
     *     sort_direction: 'asc'|'desc'
     * } $params
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(protected QueryBuilder|EloquentBuilder $builder, array $params) {
        $this->pageIndex = request()->get(self::PAGE_INDEX, 0);
        $this->pageSize = request()->get(self::PAGE_SIZE, 0);
        $this->filter = request()->get(self::FILTER, null);
        $this->sortColumn = request()->get(self::SORT_COLUMN, 'id');
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
        $this->relationships = request()->collect('relationships')->toArray();
    }

    protected function getSortBehaviors(): array {
        return [];
    }

    protected function getFilterables(): array {
        return [];
    }

    protected function applyPipeline($builder, array $pipes) {
        return app(Pipeline::class)
            ->send($builder)
            ->through($pipes)
            ->thenReturn();
    }

    public function getObjects(): PagedIndexCollection {
        $filtered = $this->applyPipeline($this->builder->clone(), [
            new RelationshipsPipe($this->relationships),
            new SortPipe($this->sortColumn, $this->sortDirection, $this->getSortBehaviors()),
            new FilterPipe($this->filters ?? [], $this->getFilterables())
        ]);
        $count = $filtered->clone()->count();
        return $this->applyPipeline($filtered, [
            new PaginationPipe($this->pageIndex ?? 0, $this->pageSize ?? 0),
            new TransformPipe($this->resource),
            new ToPagedIndexCollectionPipe($count, $this->pageIndex, $this->pageSize)
        ]);
    }

    public function toJson($options = 0): string {
        return $this->getObjects()->toJson($options);
    }

}
