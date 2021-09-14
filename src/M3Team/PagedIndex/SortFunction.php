<?php


    namespace M3Team\PagedIndex;


    interface SortFunction {
        /**
         * Funzione per l'ordinamento dinamico dei PagedIndex
         * @param $query
         * @param int $column
         * @return mixed
         */
        public function __invoke($query, int $column);
    }