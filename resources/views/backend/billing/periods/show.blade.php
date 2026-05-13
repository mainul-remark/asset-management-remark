@extends('backend.master')
@section('title', $period->name . ' — Bills')

@section('body')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold mb-1">{{ $period->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.periods.index') }}">Billing Periods</a></li>
                    <li class="breadcrumb-item active">{{ $period->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 align-items-center">
            @php
                $statusColor = match($period->status) { 'open'=>'warning','generating'=>'info','generated'=>'primary','finalized'=>'success',default=>'secondary' };
            @endphp
            <span class="badge bg-{{ $statusColor }} fs-6 text-capitalize px-3 py-2">{{ $period->status }}</span>
            @if(!$period->isFinalized())
                <button class="btn btn-sm btn-primary btn-generate" data-id="{{ $period->id }}" data-name="{{ $period->name }}">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="genSpinner"></span>
                    <i class="las la-cog me-1" id="genIcon"></i><span id="genText">{{ $period->isGenerated() ? 'Regenerate Bills' : 'Generate Bills' }}</span>
                </button>
            @endif
            @if(!$period->isFinalized() && ($summary['issuable_count'] ?? 0) > 0)
                <button class="btn btn-sm btn-warning btn-issue-all-period" data-id="{{ $period->id }}">
                    <i class="las la-paper-plane me-1"></i> Issue All Bills
                    <span class="badge bg-white text-dark ms-1">{{ $summary['issuable_count'] }}</span>
                </button>
            @endif
            @if($period->isGenerated() && !$period->isFinalized())
                <button class="btn btn-sm btn-success btn-finalize-period" data-id="{{ $period->id }}" data-name="{{ $period->name }}">
                    <i class="las la-lock me-1"></i> Finalize Period
                </button>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold text-primary">{{ number_format($summary['total_amount'], 2) }}</div>
                    <div class="small text-muted">Total Amount (৳)</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold">{{ $summary['total_bills'] }}</div>
                    <div class="small text-muted">Total Bills</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold text-warning">{{ $summary['draft_count'] }}</div>
                    <div class="small text-muted">Draft</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold text-info">{{ $summary['issued_count'] }}</div>
                    <div class="small text-muted">Issued</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold text-danger">{{ $summary['disputed_count'] }}</div>
                    <div class="small text-muted">Disputed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="fs-4 fw-bold text-success">{{ $summary['finalized_count'] }}</div>
                    <div class="small text-muted">Finalized</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Common Space Audit --}}
    @if($commonLogs->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
            <i class="las la-layer-group text-primary"></i> Common Space Calculation (Store-wise)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Store</th>
                            <th class="text-end">Total (sqft)</th>
                            <th class="text-end">Dedicated Ground (sqft)</th>
                            <th class="text-end">Common Asset (sqft)</th>
                            <th class="text-end">Remaining (sqft)</th>
                            <th class="text-end">Static Common (৳)</th>
                            <th class="text-end">Brands</th>
                            <th class="text-end">Rate/sqft</th>
                            <th class="text-end">Per Brand (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commonLogs as $log)
                        <tr>
                            <td class="fw-semibold">{{ $log->store?->title }}</td>
                            <td class="text-end">{{ number_format($log->total_store_sqft, 2) }}</td>
                            <td class="text-end">{{ number_format($log->dedicated_ground_sqft, 2) }}</td>
                            <td class="text-end">{{ number_format($log->common_ground_asset_sqft, 2) }}</td>
                            <td class="text-end text-primary fw-semibold">{{ number_format($log->remaining_sqft, 2) }}</td>
                            <td class="text-end">{{ number_format($log->common_static_fees_total, 2) }}</td>
                            <td class="text-end">{{ $log->brand_count }}</td>
                            <td class="text-end">{{ number_format($log->rate_per_sqft, 2) }}</td>
                            <td class="text-end fw-bold text-success">{{ number_format($log->common_charge_per_brand, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('billing.periods.show', $period) }}" class="row g-2 align-items-end">

                {{-- Group By toggle --}}
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-1 d-block">Group By</label>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="group_by" id="grpBrand" value="brand"
                            {{ $groupBy === 'brand' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-secondary" for="grpBrand">
                            <i class="las la-tag me-1"></i>Brand
                        </label>
                        <input type="radio" class="btn-check" name="group_by" id="grpStore" value="store"
                            {{ $groupBy === 'store' ? 'checked' : '' }} autocomplete="off">
                        <label class="btn btn-outline-secondary" for="grpStore">
                            <i class="las la-store me-1"></i>Store
                        </label>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Brand</label>
                    <select name="brand_id" class="form-select form-select-sm">
                        <option value="">All Brands</option>
                        @foreach($filterBrands as $b)
                            <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}{{ $b->code ? ' ('.$b->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Store</label>
                    <select name="store_id" class="form-select form-select-sm">
                        <option value="">All Stores</option>
                        @foreach($filterStores as $s)
                            <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->title }}{{ $s->code ? ' ('.$s->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-auto d-flex gap-2 flex-wrap align-items-center">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="las la-filter me-1"></i>Filter
                    </button>
                    @if(request('brand_id') || request('store_id'))
                        <a href="{{ route('billing.periods.show', $period) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="las la-times me-1"></i>Clear
                        </a>
                    @endif
                    @if(request('brand_id'))
                        <a href="{{ route('billing.periods.brand-invoice', [$period, request('brand_id')]) }}"
                           target="_blank" class="btn btn-sm btn-success">
                            <i class="las la-file-invoice-dollar me-1"></i>Brand Invoice
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
            <i class="las la-file-invoice-dollar text-primary"></i> Store Brand Bills
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            @if($groupBy === 'brand')
                                <th style="min-width:150px">Brand</th>
                                <th style="min-width:140px">Store</th>
                            @else
                                <th style="min-width:150px">Store</th>
                                <th style="min-width:140px">Brand</th>
                            @endif
                            <th class="text-end">Ground (৳)</th>
                            <th class="text-end">Static (৳)</th>
                            <th class="text-end">Common (৳)</th>
                            <th class="text-end">Subtotal (৳)</th>
                            <th class="text-end">Adj (৳)</th>
                            <th class="text-end">Final (৳)</th>
                            <th class="text-end" style="min-width:120px">Group Total (৳)</th>
                            <th class="text-end" style="min-width:120px">Override Δ (৳)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupKey    = $groupBy === 'brand' ? 'brand_id' : 'store_id';
                            $groupedBills = $bills->getCollection()->groupBy($groupKey);
                        @endphp

                        @forelse($groupedBills as $groupId => $groupRows)
                            @foreach($groupRows as $bill)
                            <tr>
                                @if($loop->first)
                                    {{-- Rowspan cell: Store or Brand depending on mode --}}
                                    @if($groupBy === 'brand')
                                    @php $brandIssuable = $groupRows->whereIn('bill_status', ['draft','adjusted'])->count(); @endphp
                                    <td rowspan="{{ $groupRows->count() }}" class="align-middle border-end">
                                        <div class="fw-semibold">{{ $bill->brand?->name }}</div>
                                        <div class="text-muted small">{{ $bill->brand?->code }}</div>
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            <span class="badge bg-light text-dark border small">
                                                {{ $groupRows->count() }} {{ Str::plural('store', $groupRows->count()) }}
                                            </span>
                                            @if(!$period->isFinalized() && $brandIssuable > 0)
                                            <button class="btn btn-outline-warning btn-issue-brand-all"
                                                style="font-size:0.7rem;padding:1px 6px;line-height:1.4"
                                                data-period-id="{{ $period->id }}"
                                                data-brand-id="{{ $groupId }}"
                                                data-brand-name="{{ $bill->brand?->name }}">
                                                <i class="las la-paper-plane"></i> Issue ({{ $brandIssuable }})
                                            </button>
                                            @endif
                                            @php $brandFinalizable = $groupRows->where('bill_status', 'issued')->count(); @endphp
                                            @if(!$period->isFinalized() && $brandFinalizable > 0)
                                            <button class="btn btn-outline-success btn-finalize-brand-all"
                                                style="font-size:0.7rem;padding:1px 6px;line-height:1.4"
                                                data-period-id="{{ $period->id }}"
                                                data-brand-id="{{ $groupId }}"
                                                data-brand-name="{{ $bill->brand?->name }}">
                                                <i class="las la-check-circle"></i> Finalize ({{ $brandFinalizable }})
                                            </button>
                                            @endif
                                            @php
                                                $hasPendingBrandDispute = in_array($groupId, $brandPendingDisputeIds ?? []);
                                                $brandIssuedCount = $groupRows->where('bill_status', 'issued')->count();
                                            @endphp
                                            @if(!$period->isFinalized() && $brandIssuedCount > 0 && !$hasPendingBrandDispute)
                                            <button class="btn btn-outline-danger btn-raise-brand-dispute"
                                                style="font-size:0.7rem;padding:1px 6px;line-height:1.4"
                                                data-period-id="{{ $period->id }}"
                                                data-brand-id="{{ $groupId }}"
                                                data-brand-name="{{ $bill->brand?->name }}"
                                                data-total="{{ $groupRows->sum('final_amount') }}">
                                                <i class="las la-exclamation-circle"></i> Dispute
                                            </button>
                                            @elseif($hasPendingBrandDispute)
                                            <span class="badge bg-danger" style="font-size:0.65rem;padding:3px 5px">
                                                <i class="las la-exclamation-triangle"></i> Dispute Pending
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    @else
                                    <td rowspan="{{ $groupRows->count() }}" class="align-middle border-end">
                                        <div class="fw-semibold">{{ $bill->store?->title }}</div>
                                        <div class="text-muted small">{{ $bill->store?->code }}</div>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border small">
                                                {{ $groupRows->count() }} {{ Str::plural('brand', $groupRows->count()) }}
                                            </span>
                                        </div>
                                    </td>
                                    @endif
                                @endif

                                {{-- Per-row cell: opposite of the rowspan column --}}
                                @if($groupBy === 'brand')
                                <td>
                                    <div class="fw-semibold">{{ $bill->store?->title }}</div>
                                    <div class="text-muted small">{{ $bill->store?->code }}</div>
                                </td>
                                @else
                                <td>
                                    <div class="fw-semibold">{{ $bill->brand?->name }}</div>
                                    <div class="text-muted small">{{ $bill->brand?->code }}</div>
                                </td>
                                @endif

                                <td class="text-end">{{ number_format($bill->ground_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($bill->static_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($bill->common_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($bill->subtotal, 2) }}</td>
                                <td class="text-end {{ $bill->adjustment_amount != 0 ? 'text-warning' : 'text-muted' }}">
                                    {{ $bill->adjustment_amount != 0 ? number_format($bill->adjustment_amount, 2) : '—' }}
                                </td>
                                <td class="text-end fw-bold text-primary">{{ number_format($bill->final_amount, 2) }}</td>

                                @if($loop->first)
                                @php
                                    $groupTotal  = $groupRows->sum('final_amount');
                                    $groupDelta  = $groupRows->sum('line_item_override_delta');
                                    $deltaAbs    = abs($groupDelta);
                                    $deltaColor  = $groupDelta < 0 ? 'danger' : ($groupDelta > 0 ? 'success' : 'muted');
                                    $deltaLabel  = $groupDelta < 0 ? 'discount' : 'extra';
                                @endphp
                                <td rowspan="{{ $groupRows->count() }}"
                                    class="text-end align-middle fw-bold text-success border-start"
                                    style="background:rgba(25,135,84,.05)">
                                    ৳ {{ number_format($groupTotal, 2) }}
                                </td>
                                <td rowspan="{{ $groupRows->count() }}"
{{--                                    class="text-end align-middle fw-semibold text-{{ $deltaColor }} border-start"--}}
                                    class="text-end align-middle fw-semibold text-{{ $deltaLabel == 'discount' ? 'success' : 'danger' }} border-start"
                                    style="background:rgba(0,0,0,.02)">
                                    @if($groupDelta == 0)
                                        <span class="text-muted">—</span>
                                    @else
                                        ৳ {{ number_format($deltaAbs, 2) }}
{{--                                        <div class="small fw-normal text-{{ $deltaColor }}">{{ $deltaLabel }}</div>--}}
                                        <div class="small fw-normal text-{{ $deltaLabel == 'discount' ? 'success' : 'danger' }}">{{ $deltaLabel }}</div>
                                    @endif
                                </td>
                                @endif

                                <td>
                                    @php
                                        $bc = match($bill->bill_status) {
                                            'draft'=>'secondary','issued'=>'primary','disputed'=>'danger',
                                            'adjusted'=>'warning','finalized'=>'success','paid'=>'dark',default=>'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $bc }} text-capitalize">{{ $bill->bill_status }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('billing.bills.show', $bill) }}"
                                           class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="las la-eye"></i>
                                        </a>
                                        @if($bill->bill_status === 'draft' || $bill->bill_status === 'adjusted')
                                            <button class="btn btn-sm btn-outline-primary btn-issue-bill"
                                                data-id="{{ $bill->id }}" title="Issue to Brand">
                                                <i class="las la-paper-plane"></i>
                                            </button>
                                        @endif
                                        @if($bill->bill_status === 'issued' || $bill->bill_status === 'adjusted')
                                            <button class="btn btn-sm btn-outline-success btn-finalize-bill"
                                                data-id="{{ $bill->id }}" title="Finalize">
                                                <i class="las la-check-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
                                <i class="las la-file-invoice-dollar fs-2 d-block mb-2"></i>
                                No bills generated yet. Click "Generate Bills" above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bills->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $bills->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
{{-- Brand Dispute Modal --}}
<div class="modal fade" id="brandDisputeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-exclamation-circle me-1 text-danger"></i> Raise Brand Dispute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-1">Brand: <strong id="bdBrandName"></strong></p>
                <p class="text-muted small mb-3">Current Total: <strong id="bdCurrentTotal"></strong></p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Requested Total Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="bdRequestedAmount" class="form-control" placeholder="Amount you want to pay in total">
                    <div class="form-text">Will be distributed proportionally across all store bills for this brand.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="bdReason" rows="4" placeholder="Explain the reason for the dispute..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger btn-sm" id="bdSubmitBtn">Submit Dispute</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @include('backend.includes.plugins.toastr')
    @include('backend.includes.plugins.sweetalert2')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function postAction(url, confirmMsg, successCb) {
        // if (!confirm(confirmMsg)) return;
        Swal.fire({
            title: "Are you sure?",
            text: confirmMsg,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Submit!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            toastr.success(res.message);
                            successCb();
                        } else {
                            toastr.error(res.message);
                        }
                    });
            }
        });

    }

    // Generate — dispatches job; polls if worker is running, reloads immediately if ran synchronously
    (function () {
        const btn     = document.querySelector('.btn-generate');
        if (!btn) return;

        const spinner = document.getElementById('genSpinner');
        const icon    = document.getElementById('genIcon');
        const label   = document.getElementById('genText');
        let pollTimer = null;

        function setGenerating() {
            btn.disabled = true;
            spinner?.classList.remove('d-none');
            icon?.classList.add('d-none');
            if (label) label.textContent = 'Generating…';
        }

        function resetBtn() {
            btn.disabled = false;
            spinner?.classList.add('d-none');
            icon?.classList.remove('d-none');
            if (label) label.textContent = label.dataset.original || 'Generate Bills';
        }

        function showToast(msg, type = 'info') {
            let toast = document.getElementById('genToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'genToast';
                toast.style.cssText = 'position:fixed;bottom:1rem;right:1rem;z-index:9999;min-width:280px';
                document.body.appendChild(toast);
            }
            const icons = { info: '<div class="spinner-border spinner-border-sm me-2"></div>', success: '<i class="las la-check-circle fs-5 me-2"></i>' };
            toast.className = `alert alert-${type} d-flex align-items-center shadow mb-0`;
            toast.innerHTML = (icons[type] ?? '') + `<span>${msg}</span>`;
        }

        function startPolling(id) {
            showToast('Bill generation running in background…', 'info');
            pollTimer = setInterval(() => {
                fetch(`/billing/periods/${id}/status`)
                    .then(r => r.json())
                    .then(res => {
                        if (res.done) {
                            clearInterval(pollTimer);
                            showToast('Done! Reloading…', 'success');
                            setTimeout(() => location.reload(), 800);
                        }
                    })
                    .catch(() => {
                        clearInterval(pollTimer);
                        document.getElementById('genToast')?.remove();
                        resetBtn();
                    });
            }, 3000);
        }

        // Save original label text so resetBtn can restore it
        if (label) label.dataset.original = label.textContent.trim();

        // If page loads while still generating (e.g. manual refresh mid-run), resume polling
        @if($period->status === 'generating')
        setGenerating();
        startPolling({{ $period->id }});
        @endif

        btn.addEventListener('click', function () {
            const id = this.dataset.id, name = this.dataset.name;
            // if (!confirm(`Generate bills for "${name}"?`)) return;
            Swal.fire({
                title: "Are you sure?",
                text: `Generate bills for "${name}"?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Generate!"
            }).then((result) => {
                if (result.isConfirmed) {
                    setGenerating();
                    fetch(`/billing/periods/${id}/generate`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf } })
                        .then(r => r.json())
                        .then(res => {
                            if (!res.success) {
                                // alert(res.message);
                                toastr.error(res.message);
                                resetBtn();
                                return;
                            }
                            // always queued — poll until the background worker finishes
                            startPolling(id);
                        })
                        .catch(() => resetBtn());
                }
            });


        });
    })();

    // Finalize Period
    document.querySelector('.btn-finalize-period')?.addEventListener('click', function () {
        const id = this.dataset.id, name = this.dataset.name;
        postAction(`/billing/periods/${id}/finalize`, `Finalize period "${name}"? This cannot be undone.`, () => location.reload());
    });

    // Issue All Bills (period-level)
    document.querySelector('.btn-issue-all-period')?.addEventListener('click', function () {
        const id = this.dataset.id;
        postAction(`/billing/periods/${id}/issue-all`, 'Issue all draft/adjusted bills in this period to brands?', () => location.reload());
    });

    // Issue All Bills (per-brand)
    document.querySelectorAll('.btn-issue-brand-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const periodId  = this.dataset.periodId;
            const brandId   = this.dataset.brandId;
            const brandName = this.dataset.brandName;
            postAction(
                `/billing/periods/${periodId}/brands/${brandId}/issue-all`,
                `Issue all draft/adjusted bills for "${brandName}"?`,
                () => location.reload()
            );
        });
    });

    // Finalize All Bills (per-brand)
    document.querySelectorAll('.btn-finalize-brand-all').forEach(btn => {
        btn.addEventListener('click', function () {
            const periodId  = this.dataset.periodId;
            const brandId   = this.dataset.brandId;
            const brandName = this.dataset.brandName;
            postAction(
                `/billing/periods/${periodId}/brands/${brandId}/finalize-all`,
                `Finalize all issued bills for "${brandName}"?`,
                () => location.reload()
            );
        });
    });

    // Raise Brand Dispute
    (function () {
        let activePeriodId = null, activeBrandId = null;
        const modal = new bootstrap.Modal(document.getElementById('brandDisputeModal'));

        document.querySelectorAll('.btn-raise-brand-dispute').forEach(btn => {
            btn.addEventListener('click', function () {
                activePeriodId = this.dataset.periodId;
                activeBrandId  = this.dataset.brandId;
                document.getElementById('bdBrandName').textContent    = this.dataset.brandName;
                document.getElementById('bdCurrentTotal').textContent = '৳ ' + parseFloat(this.dataset.total).toLocaleString('en-US', {minimumFractionDigits:2});
                document.getElementById('bdRequestedAmount').value    = '';
                document.getElementById('bdReason').value             = '';
                modal.show();
            });
        });

        document.getElementById('bdSubmitBtn')?.addEventListener('click', function () {
            const amount = document.getElementById('bdRequestedAmount').value.trim();
            const reason = document.getElementById('bdReason').value.trim();
            if (!amount || !reason) { toastr.error('Please fill in all required fields.'); return; }

            fetch(`/billing/periods/${activePeriodId}/brands/${activeBrandId}/brand-dispute`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ requested_amount: amount, reason: reason }),
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    toastr.success(res.message);
                    modal.hide();
                    setTimeout(() => location.reload(), 1200);
                } else {
                    toastr.error(res.message);
                }
            });
        });
    })();

    // Issue Bill (single)
    document.querySelectorAll('.btn-issue-bill').forEach(btn => {
        btn.addEventListener('click', function () {
            postAction(`/billing/bills/${this.dataset.id}/issue`, 'Issue this bill to the brand?', () => location.reload());
        });
    });

    // Finalize Bill
    document.querySelectorAll('.btn-finalize-bill').forEach(btn => {
        btn.addEventListener('click', function () {
            postAction(`/billing/bills/${this.dataset.id}/finalize`, 'Finalize this bill? Pending disputes must be resolved first.', () => location.reload());
        });
    });
})();
</script>
@endpush
