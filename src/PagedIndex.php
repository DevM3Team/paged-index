<?php

namespace M3Team\PagedIndex;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Validator;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;
use M3Team\PagedIndex\Pipes\FilterPipe;
use M3Team\PagedIndex\Pipes\PaginationPipe;
use M3Team\PagedIndex\Pipes\RelationshipsPipe;
use M3Team\PagedIndex\Pipes\SortPipe;
use M3Team\PagedIndex\Pipes\ToPagedIndexCollectionPipe;
use M3Team\PagedIndex\Pipes\TransformPipe;

/**
 * @template T
 */
final readonly class PagedIndex implements Jsonable {
    private static function rules(): array {
        return [
            config('paged-index.request_keys.page_index', "page_index") => ["nullable", "integer"],
            config('paged-index.request_keys.page_size', "page_size") => ["nullable", "integer"],
            config('paged-index.request_keys.sort_column', "sort_column") => ["nullable", "string"],
            config('paged-index.request_keys.sort_direction', "sort_direction") => ["nullable", "string"],
            config('paged-index.request_keys.filters', "filters") => ["nullable", "array"],
            config('paged-index.request_keys.filters', "filters") . ".*" => ["required", "string"],
            config('paged-index.request_keys.relationships', "relationships") => ["nullable", "array"],
            config('paged-index.request_keys.relationships', "relationships") . ".*" => ["required", "string"],
        ];
    }

    public static function fromRequest(EloquentBuilder|QueryBuilder $builder, ?string $resource = null): self {
        $data = Validator::validate(request()->all(), self::rules());
        return new self(
            $builder,
            $resource,
            $data[config('paged-index.request_keys.page_index', "page_index")] ?? 0,
            $data[config('paged-index.request_keys.page_size', "page_size")] ?? 0,
            $data[config('paged-index.request_keys.sort_column', "sort_column")] ?? config('paged-index.defaults.sort_column', 'id'),
            $data[config('paged-index.request_keys.sort_direction', "sort_direction")] ?? config('paged-index.defaults.sort_direction', 'asc'),
            $data[config('paged-index.request_keys.filters', "filters")] ?? [],
            $data[config('paged-index.request_keys.relationships', "relationships")] ?? [],
            [],
            []
        );
    }

    protected ?int $pageIndex;
    protected ?int $pageSize;
    protected ?string $sortColumn;
    protected ?string $sortDirection;
    /** @var string[] */
    protected array $filters;
    /** @var string[] */
    protected array $relationships;

    /** @var array<string, string|callable(QueryBuilder|EloquentBuilder, 'asc'|'desc'): void> */
    private array $allowedSorts;

    /** @var array<string, string|callable(QueryBuilder|EloquentBuilder, mixed): void> */
    private array $allowedFilters;

    /**
     * @param QueryBuilder|EloquentBuilder $builder
     * @param class-string|null $resource
     * @param int|null $pageIndex
     * @param int|null $pageSize
     * @param string|null $sortColumn
     * @param string|null $sortDirection
     * @param string[] $filters
     * @param string[] $relationships
     */
    public function __construct(
        protected QueryBuilder|EloquentBuilder $builder,
        protected string|null                  $resource = null,
        ?int                                   $pageIndex = null,
        ?int                                   $pageSize = null,
        ?string                                $sortColumn = null,
        ?string                                $sortDirection = null,
        array                                  $filters = [],
        array                                  $relationships = [],
        array                                  $allowedSorts = [],
        array                                  $allowedFilters = []
    ) {
        $this->pageIndex = $pageIndex ?? 0;
        $this->pageSize = $pageSize ?? 0;
        $this->sortColumn = $sortColumn ?? config('paged-index.defaults.sort_column', 'id');
        $this->sortDirection = $sortDirection ?? config('paged-index.defaults.sort_direction', 'asc');
        $this->filters = $filters;
        $this->relationships = $relationships;
        $this->allowedSorts = $allowedSorts;
        $this->allowedFilters = $allowedFilters;
    }

    public function allowedSorts(array $sorts, bool $merge = true): self {
        $allowed = $merge ? array_replace($this->allowedSorts, $sorts) : $sorts;

        return new self(
            builder: $this->builder,
            resource: $this->resource,
            pageIndex: $this->pageIndex,
            pageSize: $this->pageSize,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            filters: $this->filters,
            relationships: $this->relationships,
            allowedSorts: $allowed,
            allowedFilters: $this->allowedFilters,
        );
    }

    public function allowedFilters(array $filters, bool $merge = true): self {
        $allowed = $merge ? array_replace($this->allowedFilters, $filters) : $filters;

        return new self(
            builder: $this->builder,
            resource: $this->resource,
            pageIndex: $this->pageIndex,
            pageSize: $this->pageSize,
            sortColumn: $this->sortColumn,
            sortDirection: $this->sortDirection,
            filters: $this->filters,
            relationships: $this->relationships,
            allowedSorts: $this->allowedSorts,
            allowedFilters: $allowed,
        );
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
            new SortPipe($this->sortColumn, $this->sortDirection, $this->allowedSorts),
            new FilterPipe($this->filters ?? [], $this->allowedFilters)
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
