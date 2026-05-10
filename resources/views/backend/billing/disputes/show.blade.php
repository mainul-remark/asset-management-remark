@extends('backend.master')
@section('title', 'Review Dispute #' . $dispute->id)

@section('body')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-3">
        <div>
            <h4 class="fw-semibold mb-1">Review Dispute #{{ $dispute->id }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('billing.disputes.index') }}">Disputes</a></li>
                    <li class="breadcrumb-item active">#{{ $dispute->id }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('billing.disputes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="las la-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white fw-semibold">Dispute Details</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><td class="text-muted">Period</td><td class="fw-semibold">{{ $dispute->storeBrandBill?->billPeriod?->name }}</td></tr>
                        <tr><td class="text-muted">Store</td><td class="fw-semibold">{{ $dispute->storeBrandBill?->store?->title }}</td></tr>
                        <tr><td class="text-muted">Brand</td><td class="fw-semibold">{{ $dispute->storeBrandBill?->brand?->name }}</td></tr>
                        <tr><td class="text-muted">Requested By</td><td>{{ $dispute->requestedBy?->name }}</td></tr>
                        <tr><td class="text-muted">Date</td><td>{{ $dispute->created_at?->format('d M Y H:i') }}</td></tr>
                        <tr><td class="text-muted">Original Amount</td><td class="fw-semibold">৳ {{ number_format($dispute->original_amount, 2) }}</td></tr>
                        <tr><td class="text-muted">Requested Amount</td><td class="fw-semibold text-warning">৳ {{ number_format($dispute->requested_amount, 2) }}</td></tr>
                        <tr><td class="text-muted">Difference</td><td class="fw-semibold text-danger">- ৳ {{ number_format($dispute->original_amount - $dispute->requested_amount, 2) }}</td></tr>
                        <tr><td class="text-muted">Status</td><td>
                            @php $dc = match($dispute->status) { 'pending'=>'warning','approved'=>'success','partially_approved'=>'info','rejected'=>'danger',default=>'secondary' }; @endphp
                            <span class="badge bg-{{ $dc }} text-capitalize">{{ str_replace('_', ' ', $dispute->status) }}</span>
                        </td></tr>
                    </table>
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="small fw-semibold text-muted text-uppercase mb-1">Dispute Reason</div>
                        <div class="small">{{ $dispute->reason }}</div>
                    </div>
                    @if($dispute->admin_response)
                    <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded">
                        <div class="small fw-semibold text-muted text-uppercase mb-1">Admin Response</div>
                        <div class="small">{{ $dispute->admin_response }}</div>
                        <div class="small text-muted mt-1">— {{ $dispute->reviewedBy?->name }}, {{ $dispute->reviewed_at?->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($dispute->status === 'pending')
            <div class="card border-0 shadow-sm border-start border-primary border-3">
                <div class="card-header bg-white fw-semibold text-primary">Admin Review</div>
                <div class="card-body">
                    <div id="reviewAlert" class="d-none mb-3"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Admin Response <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="adminResponse" rows="4" placeholder="Explain your decision..."></textarea>
                    </div>
                    <div class="mb-3 d-none" id="partialAmountDiv">
                        <label class="form-label fw-semibold">Approved Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="approvedAmount" class="form-control"
                            placeholder="Enter approved amount" min="0" max="{{ $dispute->original_amount }}">
                        <div class="form-text">Between ৳0 and ৳{{ number_format($dispute->original_amount, 2) }}</div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm" id="btnApprove">
                            <i class="las la-check me-1"></i> Approve (৳{{ number_format($dispute->requested_amount, 2) }})
                        </button>
                        <button class="btn btn-info btn-sm text-white" id="btnPartial">
                            <i class="las la-adjust me-1"></i> Partial Approve
                        </button>
                        <button class="btn btn-danger btn-sm" id="btnReject">
                            <i class="las la-times me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold"><i class="las la-list me-1 text-primary"></i> Bill Line Items</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Asset</th>
                                    <th class="text-end">Full Cost</th>
                                    <th class="text-center">Brands</th>
                                    <th class="text-end">Your Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dispute->storeBrandBill?->lineItems ?? [] as $item)
                                <tr>
                                    <td>
                                        @php $tc = match($item->payment_type) { 'ground'=>'success','static'=>'primary','common'=>'warning',default=>'secondary' }; @endphp
                                        <span class="badge bg-{{ $tc }} text-capitalize">{{ $item->payment_type }}</span>
                                    </td>
                                    <td>
                                        @if($item->asset)
                                            {{ $item->asset->name }}
                                            <div class="text-muted">{{ $item->assetType?->name }}</div>
                                        @else
                                            <span class="text-muted fst-italic">Common Space</span>
                                        @endif
                                    </td>
                                    <td class="text-end">৳ {{ number_format($item->full_calculated_amount, 2) }}</td>
                                    <td class="text-center">{{ $item->assigned_brands_count }}</td>
                                    <td class="text-end fw-semibold">৳ {{ number_format($item->final_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Final Amount</th>
                                    <th class="text-end text-primary">৳ {{ number_format($dispute->storeBrandBill?->final_amount ?? 0, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf    = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = `/billing/disputes/{{ $dispute->id }}`;
    let mode      = null; // 'approve' | 'partial' | 'reject'

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify(body),
        }).then(r => r.json());
    }

    function showAlert(msg, type = 'danger') {
        const el = document.getElementById('reviewAlert');
        el.className = `alert alert-${type}`;
        el.textContent = msg;
    }

    document.getElementById('btnPartial')?.addEventListener('click', function () {
        mode = 'partial';
        document.getElementById('partialAmountDiv').classList.toggle('d-none', false);
    });

    document.getElementById('btnApprove')?.addEventListener('click', function () {
        mode = 'approve';
        document.getElementById('partialAmountDiv').classList.add('d-none');
        submit();
    });

    document.getElementById('btnReject')?.addEventListener('click', function () {
        mode = 'reject';
        document.getElementById('partialAmountDiv').classList.add('d-none');
        submit();
    });

    // allow partial to also trigger submit via the partial button being clicked again after showing input
    document.getElementById('btnPartial')?.addEventListener('dblclick', submit);

    function submit() {
        if (!mode) return;
        const response = document.getElementById('adminResponse').value.trim();
        if (!response) { showAlert('Admin response is required.'); return; }

        const body = { admin_response: response };
        let endpoint = '';

        if (mode === 'approve') {
            endpoint = `${baseUrl}/approve`;
        } else if (mode === 'reject') {
            endpoint = `${baseUrl}/reject`;
        } else if (mode === 'partial') {
            const amt = document.getElementById('approvedAmount').value;
            if (!amt) { showAlert('Approved amount is required for partial approval.'); return; }
            body.approved_amount = amt;
            endpoint = `${baseUrl}/partial`;
        }

        if (!confirm('Confirm this decision?')) return;

        post(endpoint, body).then(res => {
            if (res.success) {
                window.location.href = '{{ route('billing.disputes.index') }}';
            } else {
                showAlert(res.message ?? 'An error occurred.');
            }
        });
    }

    // Make partial approve button submit when re-clicked after showing the amount field
    let partialReady = false;
    document.getElementById('btnPartial')?.addEventListener('click', function () {
        if (partialReady) { submit(); return; }
        partialReady = true;
        this.textContent = 'Confirm Partial Approve';
        this.classList.replace('btn-info', 'btn-warning');
    });
})();
</script>
@endpush
