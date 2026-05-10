@extends('backend.master')
@section('title', 'Bill Detail')

@section('body')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold mb-1">Bill Detail</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.periods.index') }}">Billing Periods</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.periods.show', $bill->billPeriod) }}">{{ $bill->billPeriod?->name }}</a></li>
                    <li class="breadcrumb-item active">Bill #{{ $bill->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('billing.bills.invoice', $bill) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="las la-print me-1"></i> Invoice
            </a>
            @if($bill->bill_status === 'draft' || $bill->bill_status === 'adjusted')
                <button class="btn btn-sm btn-primary" id="btnIssueBill"><i class="las la-paper-plane me-1"></i> Issue to Brand</button>
            @endif
            @if(in_array($bill->bill_status, ['issued','adjusted']))
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#adjustModal"><i class="las la-edit me-1"></i> Adjust Bill</button>
                <button class="btn btn-sm btn-success" id="btnFinalizeBill"><i class="las la-check-circle me-1"></i> Finalize</button>
            @endif
            @if($bill->bill_status === 'finalized')
                <button class="btn btn-sm btn-dark" id="btnMarkPaid"><i class="las la-money-check me-1"></i> Mark as Paid</button>
            @endif
        </div>
    </div>

    <div class="row g-3">
        {{-- Left: Bill header info --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">Bill Summary</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Period</td><td class="fw-semibold">{{ $bill->billPeriod?->name }}</td></tr>
                        <tr><td class="text-muted">Store</td><td class="fw-semibold">{{ $bill->store?->title }} <span class="text-muted small">({{ $bill->store?->code }})</span></td></tr>
                        <tr><td class="text-muted">Brand</td><td class="fw-semibold">{{ $bill->brand?->name }}</td></tr>
                        <tr><td class="text-muted">Status</td><td>
                            @php $bc = match($bill->bill_status) { 'draft'=>'secondary','issued'=>'primary','disputed'=>'danger','adjusted'=>'warning','finalized'=>'success','paid'=>'dark',default=>'secondary' }; @endphp
                            <span class="badge bg-{{ $bc }} text-capitalize">{{ $bill->bill_status }}</span>
                        </td></tr>
                        <tr><td class="text-muted">Issued At</td><td>{{ $bill->issued_at?->format('d M Y H:i') ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Finalized At</td><td>{{ $bill->finalized_at?->format('d M Y H:i') ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Finalized By</td><td>{{ $bill->finalizedBy?->name ?? '—' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">Amount Breakdown</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Ground Total</td><td class="text-end fw-semibold">৳ {{ number_format($bill->ground_amount, 2) }}</td></tr>
                        <tr><td class="text-muted">Static Total</td><td class="text-end fw-semibold">৳ {{ number_format($bill->static_amount, 2) }}</td></tr>
                        <tr><td class="text-muted">Common/Other</td><td class="text-end fw-semibold">৳ {{ number_format($bill->common_amount, 2) }}</td></tr>
                        <tr class="table-light"><td class="fw-semibold">Subtotal</td><td class="text-end fw-bold">৳ {{ number_format($bill->subtotal, 2) }}</td></tr>
                        <tr><td class="text-muted">Adjustment</td><td class="text-end {{ $bill->adjustment_amount < 0 ? 'text-danger' : ($bill->adjustment_amount > 0 ? 'text-warning' : 'text-muted') }}">
                            {{ $bill->adjustment_amount != 0 ? '৳ ' . number_format($bill->adjustment_amount, 2) : '—' }}
                        </td></tr>
                        <tr class="table-primary"><td class="fw-bold">Final Amount</td><td class="text-end fw-bold fs-5">৳ {{ number_format($bill->final_amount, 2) }}</td></tr>
                    </table>
                </div>
            </div>

            @if($bill->admin_note)
            <div class="card border-0 shadow-sm mb-3 border-start border-warning border-3">
                <div class="card-header bg-white fw-semibold text-warning"><i class="las la-sticky-note me-1"></i> Admin Note</div>
                <div class="card-body text-muted small">{{ $bill->admin_note }}</div>
            </div>
            @endif

            {{-- Common Space Info --}}
            @if($commonLog)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold"><i class="las la-layer-group me-1 text-primary"></i> Common Space Audit</div>
                <div class="card-body">
                    <table class="table table-sm mb-0 small">
                        <tr><td class="text-muted">Total Store sqft</td><td class="text-end">{{ number_format($commonLog->total_store_sqft, 2) }}</td></tr>
                        <tr><td class="text-muted">Dedicated Ground sqft</td><td class="text-end">{{ number_format($commonLog->dedicated_ground_sqft, 2) }}</td></tr>
                        <tr><td class="text-muted">Common Asset sqft</td><td class="text-end">{{ number_format($commonLog->common_ground_asset_sqft, 2) }}</td></tr>
                        <tr><td class="text-muted">Remaining sqft</td><td class="text-end fw-semibold text-primary">{{ number_format($commonLog->remaining_sqft, 2) }}</td></tr>
                        <tr><td class="text-muted">Common Static Fees</td><td class="text-end">৳ {{ number_format($commonLog->common_static_fees_total, 2) }}</td></tr>
                        <tr><td class="text-muted">Brands in Store</td><td class="text-end">{{ $commonLog->brand_count }}</td></tr>
                        <tr><td class="text-muted">Rate/sqft</td><td class="text-end">৳ {{ number_format($commonLog->rate_per_sqft, 2) }}</td></tr>
                        <tr class="table-light">
                            <td class="fw-semibold">Avg/Brand <span class="text-muted fw-normal small">(ratio-based)</span></td>
                            <td class="text-end fw-bold">৳ {{ number_format($commonLog->common_charge_per_brand, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Line items + disputes --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold"><i class="las la-list me-1 text-primary"></i> Line Items</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Asset</th>
                                    <th class="text-end">sqft</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Full Cost</th>
                                    <th class="text-center">Brands</th>
                                    <th class="text-end">Brand Share</th>
                                    <th class="text-end">Final</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bill->lineItems as $item)
                                <tr>
                                    <td>
                                        @php $tc = match($item->payment_type) { 'ground'=>'success','static'=>'primary','common'=>'warning',default=>'secondary' }; @endphp
                                        <span class="badge bg-{{ $tc }} text-capitalize">{{ $item->payment_type }}</span>
                                    </td>
                                    <td>
                                        @if($item->asset)
                                            <div class="fw-semibold">{{ $item->asset->name }}</div>
                                            <div class="text-muted">{{ $item->assetType?->name }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Common Space</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $item->asset_sqft > 0 ? number_format($item->asset_sqft, 2) : '—' }}</td>
                                    <td class="text-end">{{ $item->rate_per_sqft > 0 ? number_format($item->rate_per_sqft, 2) : '—' }}</td>
                                    <td class="text-end">{{ $item->unit_price > 0 ? '৳ ' . number_format($item->unit_price, 2) : '—' }}</td>
                                    <td class="text-end">৳ {{ number_format($item->full_calculated_amount, 2) }}</td>
                                    <td class="text-center">
                                        @if($item->assigned_brands_count > 1)
                                            <span class="badge bg-light text-dark border">{{ $item->assigned_brands_count }} brands</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">৳ {{ number_format($item->calculated_amount, 2) }}</td>
                                    <td class="text-end fw-bold {{ $item->override_amount !== null ? 'text-warning' : '' }}">
                                        ৳ {{ number_format($item->final_amount, 2) }}
                                        @if($item->override_amount !== null)
                                            <i class="las la-edit text-warning" title="Overridden"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$bill->billPeriod?->isFinalized())
                                        <button class="btn btn-xs btn-outline-secondary py-0 px-1 btn-override-line"
                                            data-id="{{ $item->id }}"
                                            data-amount="{{ $item->final_amount }}"
                                            data-note="{{ $item->note }}"
                                            title="Override">
                                            <i class="las la-pen" style="font-size:12px"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="8" class="text-end">Total</th>
                                    <th class="text-end text-primary">৳ {{ number_format($bill->final_amount, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Disputes --}}
            @if($bill->disputes->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="las la-exclamation-triangle me-1 text-danger"></i> Disputes</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr><th>By</th><th>Original</th><th>Requested</th><th>Approved</th><th>Status</th><th>Admin Response</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @foreach($bill->disputes as $dispute)
                                <tr>
                                    <td>{{ $dispute->requestedBy?->name }}</td>
                                    <td>৳ {{ number_format($dispute->original_amount, 2) }}</td>
                                    <td>৳ {{ number_format($dispute->requested_amount, 2) }}</td>
                                    <td>{{ $dispute->approved_amount ? '৳ ' . number_format($dispute->approved_amount, 2) : '—' }}</td>
                                    <td>
                                        @php $dc = match($dispute->status) { 'pending'=>'warning','approved'=>'success','partially_approved'=>'info','rejected'=>'danger',default=>'secondary' }; @endphp
                                        <span class="badge bg-{{ $dc }} text-capitalize">{{ str_replace('_', ' ', $dispute->status) }}</span>
                                    </td>
                                    <td class="text-muted">{{ $dispute->admin_response ?? '—' }}</td>
                                    <td>
                                        @if($dispute->status === 'pending')
                                            <a href="{{ route('billing.disputes.show', $dispute) }}" class="btn btn-xs btn-outline-primary py-0 px-2">Review</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submit dispute button for issued bills --}}
            @if(in_array($bill->bill_status, ['issued']))
            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">
                    <i class="las la-exclamation-circle me-1"></i> Raise Dispute
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('modal')
{{-- Adjust Modal --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Adjust Bill</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="text-muted small">Current subtotal: <strong>৳ {{ number_format($bill->subtotal, 2) }}</strong></p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Adjustment Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="adjustmentAmount" class="form-control" value="{{ $bill->adjustment_amount }}" placeholder="Use negative for discount (e.g. -500)">
                    <div class="form-text">Negative = discount. New final = subtotal + adjustment.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Admin Note</label>
                    <textarea class="form-control" id="adminNote" rows="3">{{ $bill->admin_note }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-warning btn-sm" id="saveAdjustBtn">Save Adjustment</button>
            </div>
        </div>
    </div>
</div>

{{-- Dispute Modal --}}
<div class="modal fade" id="disputeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Raise Dispute</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <p class="text-muted small">Current final amount: <strong>৳ {{ number_format($bill->final_amount, 2) }}</strong></p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Requested Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="requestedAmount" class="form-control" placeholder="Amount you want to pay">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="disputeReason" rows="4" placeholder="Explain the reason for your dispute..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger btn-sm" id="saveDisputeBtn">Submit Dispute</button>
            </div>
        </div>
    </div>
</div>

{{-- Override Line Item Modal --}}
<div class="modal fade" id="overrideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Override Line Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="overrideLineId">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Override Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" id="overrideAmount" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Note</label>
                    <input type="text" id="overrideNote" class="form-control" placeholder="Reason for override">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary btn-sm" id="saveOverrideBtn">Save Override</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const billId = {{ $bill->id }};

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(body),
        }).then(r => r.json());
    }

    // Issue
    document.getElementById('btnIssueBill')?.addEventListener('click', function () {
        if (!confirm('Issue this bill to the brand?')) return;
        post(`/billing/bills/${billId}/issue`).then(res => { alert(res.message); if (res.success) location.reload(); });
    });

    // Finalize
    document.getElementById('btnFinalizeBill')?.addEventListener('click', function () {
        if (!confirm('Finalize this bill?')) return;
        post(`/billing/bills/${billId}/finalize`).then(res => { alert(res.message); if (res.success) location.reload(); });
    });

    // Mark Paid
    document.getElementById('btnMarkPaid')?.addEventListener('click', function () {
        if (!confirm('Mark this bill as paid?')) return;
        post(`/billing/bills/${billId}/paid`).then(res => { alert(res.message); if (res.success) location.reload(); });
    });

    // Adjust
    document.getElementById('saveAdjustBtn')?.addEventListener('click', function () {
        post(`/billing/bills/${billId}/adjust`, {
            adjustment_amount: document.getElementById('adjustmentAmount').value,
            admin_note: document.getElementById('adminNote').value,
        }).then(res => { alert(res.message); if (res.success) location.reload(); });
    });

    // Dispute
    document.getElementById('saveDisputeBtn')?.addEventListener('click', function () {
        post(`/billing/disputes/${billId}`, {
            requested_amount: document.getElementById('requestedAmount').value,
            reason: document.getElementById('disputeReason').value,
        }).then(res => { alert(res.message); if (res.success) location.reload(); });
    });

    // Override Line Item
    document.querySelectorAll('.btn-override-line').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('overrideLineId').value  = this.dataset.id;
            document.getElementById('overrideAmount').value  = this.dataset.amount;
            document.getElementById('overrideNote').value    = this.dataset.note;
            new bootstrap.Modal(document.getElementById('overrideModal')).show();
        });
    });

    document.getElementById('saveOverrideBtn')?.addEventListener('click', function () {
        const lineId = document.getElementById('overrideLineId').value;
        post(`/billing/line-items/${lineId}/override`, {
            override_amount: document.getElementById('overrideAmount').value,
            note: document.getElementById('overrideNote').value,
        }).then(res => { alert(res.message); if (res.success) location.reload(); });
    });
})();
</script>
@endpush
