<?php


    namespace M3Team\PagedIndex;


    use Illuminate\Database\Eloquent\Model;

    interface FilterFunction {
        /**
         * Funzione per il filtraggio dei PagedIndex
         * @param Model $model
         * @param string $filter
         * @return bool
         */
        public function __invoke(Model $model, string $filter): bool;
    }