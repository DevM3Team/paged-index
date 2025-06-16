<?php

namespace M3Team\PagedIndex\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagedIndexRequest extends FormRequest {
    public function rules(): array {
        return [
            "page_index" => ["nullable", "integer"],
            "page_size" => ["nullable", "integer"],
            "sort_column" => ["nullable", "string"],
            "sort_direction" => ["nullable", "string"],
            "with" => ["nullable", "string"]
        ];
    }

    public function pageSize(): int {
        return $this->integer('page_size');
    }

    public function pageIndex(): int {
        return $this->integer('page_index');
    }

    public function sortColumn(): ?string {
        return $this->string('sort_column')?->toString();
    }

    public function sortDirection(): string {
        return $this->string('sort_direction', 'asc')->toString();
    }

    public function relationships(): array {
        return $this->string('with', '')
            ->explode(',')
            ->filter()
            ->toArray();
    }

}
