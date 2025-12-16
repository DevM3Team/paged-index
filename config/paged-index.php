<?php

return [
    'request_keys' => [
        'page_index' => env('PAGED_INDEX_KEY_PAGE_INDEX', 'page_index'),
        'page_size' => env('PAGED_INDEX_KEY_PAGE_SIZE', 'page_size'),
        'filters' => env('PAGED_INDEX_KEY_FILTER', 'filters'),
        'sort_column' => env('PAGED_INDEX_KEY_SORT_COLUMN', 'sort_column'),
        'sort_direction' => env('PAGED_INDEX_KEY_SORT_DIRECTION', 'sort_direction'),
        'relationships' => env('PAGED_INDEX_KEY_RELATIONSHIPS', 'with'),
    ],
    'defaults' => [
        'sort_column' => env('PAGED_INDEX_DEFAULTS_SORT_COLUMN', 'id'),
        'sort_direction' => env('PAGED_INDEX_DEFAULTS_SORT_DIRECTION', 'asc'),
    ]
];
