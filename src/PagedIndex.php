<?php


    namespace M3Team\PagedIndex;


    use Closure;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;

    class PagedIndex {
        const PAGE_INDEX = 'page_index';
        const PAGE_SIZE = 'page_size';
        const FILTER = 'filter';
        const SORT_COLUMN = 'sort_column';
        const SORT_DIRECTION = 'sort_direction';


        protected int $pageIndex, $pageSize, $sortColumn;
        protected string $filter, $sortDirection;
        protected Collection $collection;
        protected SortFunction $sortFn;
        protected FilterFunction $filterFn;

        public function __construct(Request $request, Collection $collection) {
            $this->pageIndex = $request->get(self::PAGE_INDEX, 0);
            $this->pageSize = $request->get(self::PAGE_SIZE, 0);
            $this->filter = $request->get(self::FILTER, '');
            $this->sortColumn = $request->get(self::SORT_COLUMN, 0);
            $this->sortDirection = $request->get(self::SORT_DIRECTION, 'asc');
            $this->collection = $collection;
            $this->sortFn = new class implements SortFunction {
                public function __invoke(Model $query, int $column) {
                    return $query->id;
                }

            };
            $this->filterFn = new class implements FilterFunction{
                public function __invoke(Model $model, string $filter): bool {
                    return true;
                }
            };
        }

        protected function getSkip() {
            try {
                return $this->pageIndex * $this->pageSize;
            } catch (\Exception $e) {
                return 0;
            }
        }

        protected function sort() {
            $sortFn = function ($query) {
                return ($this->sortFn)($query, $this->sortColumn);
            };
            $this->collection = $this->sortDirection == 'asc' ? $this->collection->sortBy($sortFn) : $this->collection->sortByDesc($sortFn);
        }

        protected function page() {
            $this->collection = $this->pageSize != 0 ? $this->collection->skip($this->getSkip())->take($this->pageSize) : $this->collection;
        }

        protected function filter(): int {
            if ($this->filter != '') {
                $this->collection = $this->collection->filter(function (Model $query) {
                    return ($this->filterFn)($query, $this->filter);
                });
            }
            return $this->collection->count();
        }

        public function setSortFn(SortFunction $sortFn) {
            $this->sortFn = $sortFn;
        }

        public function setFilterFn(FilterFunction $filterFn) {
            $this->filterFn = $filterFn;
        }

        public function getObjects(): array {
            $this->sort();
            $total = $this->filter();
            $this->page();
            return ["objects" => $this->collection->values(), "total" => $total];
        }

    }