<?php

namespace M3Team\PagedIndex;

use Exception;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class PagedIndex
{
    const PAGE_INDEX = 'page_index';
    const PAGE_SIZE = 'page_size';
    const FILTER = 'filter';
    const SORT_COLUMN = 'sort_column';
    const SORT_DIRECTION = 'sort_direction';


    protected int $pageIndex, $pageSize, $sortColumn;
    protected string $filter, $sortDirection;
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
        $this->filter = request()->get(self::FILTER, '');
        $this->sortColumn = request()->get(self::SORT_COLUMN, 0);
        $this->sortDirection = request()->get(self::SORT_DIRECTION, 'asc');
        $this->collection = $collection;
    }

    /**
     * The number of models to skip according to page's size and page's index
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
     * Orders the models' collection
     * Ordina la collection dell'oggetto e la ritorna ordinata
     * @return Collection The ordered collection | La collection ordinata
     */
    protected abstract function sort(): Collection;

    /**
     * Filters the models' collection
     * Filtra la collection dell'oggetto e la ritorna filtrata
     * @return Collection The filtered collection | La collection filtrata
     */
    protected abstract function filter(): Collection;

    /**
     * Selects the models to return according to page's size and page's index
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
     * @return array
     */
    #[ArrayShape([
        "objects" => "\Illuminate\Support\Collection",
        "total" => "int",
        "page_index" => "int|mixed",
        "page_size" => "int|mixed"
    ])] public function getObjects(): array
    {
        $this->collection = $this->sort();
        $this->collection = $this->filter();
        $count = $this->collection->count();
        $this->collection = $this->page();
        return [
            "objects" => $this->collection->values(),
            "total" => $count,
            "page_index" => $this->pageIndex,
            "page_size" => $this->pageSize
        ];
    }

}