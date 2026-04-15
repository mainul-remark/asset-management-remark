<?php

namespace App\DataTables\Scopes;

use Yajra\DataTables\Contracts\DataTableScope;

class FixStatusScope implements DataTableScope
{
    public function __construct(private readonly string $status) {}

    public function apply($query): void
    {
        $query->where('issue_fix_status', $this->status);
    }
}
