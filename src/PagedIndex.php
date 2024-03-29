<?php

namespace M3Team\PagedIndex;

use Closure;
use Exception;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use M3Team\PagedIndex\Http\Resources\PagedIndexCollection;
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


    /** @var class-string|null  */
    protected string|null $resource = null;
    protected int $pageIndex, $pageSize;
    protected mixed $sortColumn;
    protected ?string $filter;
    protected ?string $sortDirection;
    protected Collection $collection;

    /**
     * The constructor takes the values from the request if specified otherwise will use default values
     * Costruttore che prende dalla richiesta i valori oppure ci assegna il valore di default
     * @param Collection $collection
     * @throws NotFoundExceptionInterface|ContainerExceptionInterface
     */
    public function __construct(Collection $collection)
    {
        $this->pageIndex = request()->get(self::PAGE_INDEX, 0);
        $this->pageSize = request()->get(self::PAGE_SIZE, 0);
        $this->filter = request()->get(self::FILTER, null);
        $this->sortColumn = request()->get(self::SORT_COLUMN, 'id');
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
        $this->collection = $collection;
    }

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

    /**
     * The function that defines the collection's order
     *
     * La funzione che definisce l'ordinamento della collection
     * @return Closure
     */
    protected abstract function sortingFunction(): Closure;


    /**
     * Orders the models' collection
     *
     * Ordina la collection dell'oggetto e la ritorna ordinata
     * @return Collection The ordered collection | La collection ordinata
     */
    protected function sort(): Collection
    {
        return $this->sortDirection === 'asc' ? $this->collection->sortBy($this->sortingFunction()) :
            $this->collection->sortByDesc($this->sortingFunction());
    }

    /**
     * Filters the models' collection
     *
     * Filtra la collection dell'oggetto e la ritorna filtrata
     * @return Collection The filtered collection | La collection filtrata
     */
    protected function filter(): Collection
    {
        return $this->collection->filter(fn($object) => $this->filterFunction($object));
    }

    /**
     * Selects the models to return according to page's size and page's index
     *
     * Seleziona il numero di oggetti da ritornare in base alla grandezza della pagina e al numero della pagina
     * @return Collection The paginated collection | La collection paginata
     */
    protected function page(): Collection
    {
        return $this->pageSize != 0
            ? $this->collection->skip($this->getSkip())->take($this->pageSize) :
            $this->collection;
    }

    /**
     * Return the computed collection
     * Ritorna gli oggetti elaborati
     * @return PagedIndexCollection
     */
    public function getObjects(): PagedIndexCollection
    {
        $this->collection = $this->sort();
        $this->collection = $this->filter();
        $count = $this->collection->count();
        $this->collection = $this->page();
        return new PagedIndexCollection(
            $this->resource === null ? $this->collection : ($this->resource)::collection($this->collection),
            $count,
            $this->pageIndex,
            $this->pageSize
        );
    }

    public function toJson($options = 0): string
    {
        return $this->getObjects()->toJson($options);
    }


    /**
     * The function that decides how to filter an element of the collection
     *
     * La funzione che decide come filtrare il singolo elemento della collezione
     * @param T $object
     * @return bool
     */
    protected abstract function filterFunction($object): bool;

}