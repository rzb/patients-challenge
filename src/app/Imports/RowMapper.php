<?php

namespace App\Imports;

use Illuminate\Support\Collection;

class RowMapper
{
    protected Collection $config;

    public function __construct(array|collection $config)
    {
        $this->config = collect($config)->dot();
    }

    public function handle(array $row): Collection
    {
        return collect($row)->mapWithKeys(fn ($value, $key) => [
            $this->config->search($key) ?: $key => $value
        ]);
    }
}
