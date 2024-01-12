<?php

namespace M3Team\PagedIndex\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use JetBrains\PhpStorm\ArrayShape;

class PagedIndexCollection extends ResourceCollection
{

    private int $count;
    private int $pageIndex;
    private int $pageSize;

    public function __construct($resource, int $count, int $pageIndex, int $pageSize)
    {
        parent::__construct($resource);
        $this->count = $count;
        $this->pageIndex = $pageIndex;
        $this->pageSize = $pageSize;
    }

    #[ArrayShape([
        'objects' => "\Illuminate\Support\Collection",
        "total" => "int",
        "page_index" => "int",
        "page_size" => "int"
    ])] public function toArray($request)
    {
        return [
            'objects' => $this->collection->toArray(),
            "total" => $this->count,
            "page_index" => $this->pageIndex,
            "page_size" => $this->pageSize
        ];
    }
}