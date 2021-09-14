<?php


    namespace M3Team\PagedIndex;


    interface FilterFunction {
        /**
         * Funzione per il filtraggio dei PagedIndex
         * @param $model
         * @param string $filter
         * @return bool
         */
        public function __invoke($model, string $filter): bool;
    }