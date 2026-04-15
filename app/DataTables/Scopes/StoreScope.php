<?php

namespace App\DataTables\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class StoreScope implements DataTableScope
{
    public function __construct(private readonly int|string $storeId) {}

    public function apply($query): void
    {
        $query->where('store_id', $this->storeId);
    }
}
