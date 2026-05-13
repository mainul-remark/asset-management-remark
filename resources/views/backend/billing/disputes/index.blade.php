@extends('backend.master')
@section('title', 'Bill Disputes')

@section('body')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-3">
        <div>
            <h4 class="fw-semibold mb-1">Disputes</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.periods.index') }}">Billing Periods</a></li>
                    <li class="breadcrumb-item active">Disputes</li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-0" id="disputeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabBillDisputes" type="button">
                <i class="las la-file-invoice me-1"></i> Bill Disputes
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabBrandDisputes" type="button">
                <i class="las la-layer-group me-1"></i> Brand Disputes
                @if($brandPendingCount > 0)
                    <span class="badge bg-danger ms-1">{{ $brandPendingCount }}</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- Tab 1: Per-bill disputes --}}
        <div class="tab-pane fade show active" id="tabBillDisputes">
            <div class="card border-0 shadow-sm border-top-0 rounded-top-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Period</th>
                                    <th>Store</th>
                                    <th>Brand</th>
                                    <th class="text-end">Original (৳)</th>
                                    <th class="text-end">Requested (৳)</th>
                                    <th class="text-end">Approved (৳)</th>
                                    <th>Status</th>
                                    <th>Raised By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disputes as $dispute)
                                <tr>
                                    <td>{{ $dispute->id }}</td>
                                    <td class="small">{{ $dispute->storeBrandBill?->billPeriod?->name }}</td>
                                    <td><div class="fw-semibold small">{{ $dispute->storeBrandBill?->store?->title }}</div></td>
                                    <td><div class="fw-semibold small">{{ $dispute->storeBrandBill?->brand?->name }}</div></td>
                                    <td class="text-end">{{ number_format($dispute->original_amount, 2) }}</td>
                                    <td class="text-end text-warning fw-semibold">{{ number_format($dispute->requested_amount, 2) }}</td>
                                    <td class="text-end {{ $dispute->approved_amount ? 'text-success fw-semibold' : 'text-muted' }}">
                                        {{ $dispute->approved_amount ? number_format($dispute->approved_amount, 2) : '—' }}
                                    </td>
                                    <td>
                                        @php $dc = match($dispute->status) { 'pending'=>'warning','approved'=>'success','partially_approved'=>'info','rejected'=>'danger',default=>'secondary' }; @endphp
                                        <span class="badge bg-{{ $dc }} text-capitalize">{{ str_replace('_', ' ', $dispute->status) }}</span>
                                    </td>
                                    <td class="small">{{ $dispute->requestedBy?->name }}</td>
                                    <td class="small text-muted">{{ $dispute->created_at?->format('d M Y') }}</td>
                                    <td>
                                        @allowed('billing.disputes.show')
                                        <a href="{{ route('billing.disputes.show', $dispute) }}" class="btn btn-sm btn-outline-primary">
                                            {{ $dispute->status === 'pending' ? 'Review' : 'View' }}
                                        </a>
                                        @endallowed
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="las la-check-circle fs-2 d-block mb-2 text-success"></i>
                                        No bill disputes found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($disputes->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $disputes->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Tab 2: Brand-level disputes --}}
        <div class="tab-pane fade" id="tabBrandDisputes">
            <div class="card border-0 shadow-sm border-top-0 rounded-top-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Period</th>
                                    <th>Brand</th>
                                    <th class="text-end">Original Total (৳)</th>
                                    <th class="text-end">Requested Total (৳)</th>
                                    <th class="text-end">Approved Total (৳)</th>
                                    <th>Status</th>
                                    <th>Raised By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brandDisputes as $dispute)
                                <tr>
                                    <td>{{ $dispute->id }}</td>
                                    <td class="small">{{ $dispute->billPeriod?->name }}</td>
                                    <td><div class="fw-semibold small">{{ $dispute->brand?->name }}</div></td>
                                    <td class="text-end">{{ number_format($dispute->original_amount, 2) }}</td>
                                    <td class="text-end text-warning fw-semibold">{{ number_format($dispute->requested_amount, 2) }}</td>
                                    <td class="text-end {{ $dispute->approved_amount ? 'text-success fw-semibold' : 'text-muted' }}">
                                        {{ $dispute->approved_amount ? number_format($dispute->approved_amount, 2) : '—' }}
                                    </td>
                                    <td>
                                        @php $dc = match($dispute->status) { 'pending'=>'warning','approved'=>'success','partially_approved'=>'info','rejected'=>'danger',default=>'secondary' }; @endphp
                                        <span class="badge bg-{{ $dc }} text-capitalize">{{ str_replace('_', ' ', $dispute->status) }}</span>
                                    </td>
                                    <td class="small">{{ $dispute->requestedBy?->name }}</td>
                                    <td class="small text-muted">{{ $dispute->created_at?->format('d M Y') }}</td>
                                    <td>
                                        @allowed('billing.brand-disputes.show')
                                        <a href="{{ route('billing.brand-disputes.show', $dispute) }}" class="btn btn-sm btn-outline-primary">
                                            {{ $dispute->status === 'pending' ? 'Review' : 'View' }}
                                        </a>
                                        @endallowed
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="las la-check-circle fs-2 d-block mb-2 text-success"></i>
                                        No brand disputes found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($brandDisputes->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    {{ $brandDisputes->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
