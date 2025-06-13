<?php

namespace M3Team\PagedIndex\Pipes;

interface ApplySort {
    public function handle($builder, \Closure $next);
}
