<?php


    namespace M3Team\PagedIndex;


    use Illuminate\Database\Eloquent\Model;

    interface SortFunction {
        /**
         * Funzione per l'ordinamento dinamico dei PagedIndex
         * @param Model $query
         * @param int $column
         * @return mixed
         */
        public function __invoke(Model $query, int $column);
    }