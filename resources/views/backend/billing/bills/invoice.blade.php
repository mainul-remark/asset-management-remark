@extends('backend.master')
@section('title', 'Invoice #' . $bill->id)

@section('body')
<div class="container" style="max-width:860px">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3 d-print-none">
        <a href="{{ route('billing.bills.show', $bill) }}" class="btn btn-sm btn-outline-secondary">
            <i class="las la-arrow-left me-1"></i> Back
        </a>
        <button class="btn btn-sm btn-primary" onclick="window.print()">
            <i class="las la-print me-1"></i> Print / Save PDF
        </button>
    </div>

    <div class="card border shadow-sm p-4" id="invoiceCard">
        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-6">
                <h3 class="fw-bold text-primary mb-0">INVOICE</h3>
                <div class="text-muted small">Billing Period: <strong>{{ $bill->billPeriod?->name }}</strong></div>
                <div class="text-muted small">Invoice #: <strong>INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                <div class="text-muted small">Date: <strong>{{ now()->format('d M Y') }}</strong></div>
            </div>
            <div class="col-6 text-end">
                <div class="fw-bold fs-5">{{ config('app.name') }}</div>
                @php $bc = match($bill->bill_status) { 'paid'=>'success','finalized'=>'primary','disputed'=>'danger',default=>'warning' }; @endphp
                <span class="badge bg-{{ $bc }} text-capitalize fs-6 mt-1">{{ $bill->bill_status }}</span>
            </div>
        </div>

        <hr>

        {{-- Bill To / Store --}}
        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small mb-1 text-uppercase fw-semibold">Bill To</div>
                <div class="fw-bold">{{ $bill->brand?->name }}</div>
                <div class="small text-muted">Code: {{ $bill->brand?->code }}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small mb-1 text-uppercase fw-semibold">Store</div>
                <div class="fw-bold">{{ $bill->store?->title }}</div>
                <div class="small text-muted">{{ $bill->store?->code }}</div>
                @if($bill->store?->division || $bill->store?->district)
                    <div class="small text-muted">{{ $bill->store?->district?->name }}, {{ $bill->store?->division?->name }}</div>
                @endif
            </div>
        </div>

        {{-- Period Info --}}
        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small mb-1 text-uppercase fw-semibold">Period</div>
                <div>{{ $bill->billPeriod?->period_start?->format('d M Y') }} — {{ $bill->billPeriod?->period_end?->format('d M Y') }}</div>
            </div>
        </div>

        {{-- Line Items --}}
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th class="text-end">Full Cost (৳)</th>
                    <th class="text-center">Brands</th>
                    <th class="text-end">Your Share (৳)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->lineItems as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($item->asset)
                            <strong>{{ $item->asset->name }}</strong>
                            @if($item->assetType)
                                <div class="text-muted small">{{ $item->assetType->name }}
                                @if($item->payment_type === 'ground')
                                    &nbsp;· {{ number_format($item->asset_sqft, 2) }} sqft × ৳{{ number_format($item->rate_per_sqft, 2) }}/sqft
                                @elseif($item->payment_type === 'static')
                                    &nbsp;· Fixed fee
                                @endif
                                </div>
                            @endif
                        @else
                            <strong>Common Space Charge</strong>
                            <div class="text-muted small">
                                Allocated share: {{ number_format($item->asset_sqft, 2) }} sqft × ৳{{ number_format($item->rate_per_sqft, 2) }}/sqft
                                @if($commonLog?->common_static_fees_total > 0)
                                    + proportional common static fees
                                @endif
                                <span class="fst-italic">(distributed by asset footprint ratio among {{ $item->assigned_brands_count }} brands)</span>
                            </div>
                        @endif
                        @if($item->note)
                            <div class="text-warning small fst-italic"><i class="las la-edit"></i> {{ $item->note }}</div>
                        @endif
                    </td>
                    <td>
                        @php $tc = match($item->payment_type) { 'ground'=>'success','static'=>'primary','common'=>'warning',default=>'secondary' }; @endphp
                        <span class="badge bg-{{ $tc }} text-capitalize">{{ $item->payment_type }}</span>
                    </td>
                    <td class="text-end">৳ {{ number_format($item->full_calculated_amount, 2) }}</td>
                    <td class="text-center">{{ $item->assigned_brands_count }}</td>
                    <td class="text-end fw-semibold">৳ {{ number_format($item->final_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted">Ground Total</td><td class="text-end">৳ {{ number_format($bill->ground_amount, 2) }}</td></tr>
                    <tr><td class="text-muted">Static Total</td><td class="text-end">৳ {{ number_format($bill->static_amount, 2) }}</td></tr>
                    <tr><td class="text-muted">Common Total</td><td class="text-end">৳ {{ number_format($bill->common_amount, 2) }}</td></tr>
                    <tr class="table-light"><td class="fw-semibold">Subtotal</td><td class="text-end fw-semibold">৳ {{ number_format($bill->subtotal, 2) }}</td></tr>
                    @if($bill->adjustment_amount != 0)
                    <tr><td class="text-muted">Adjustment</td><td class="text-end {{ $bill->adjustment_amount < 0 ? 'text-danger' : 'text-warning' }}">৳ {{ number_format($bill->adjustment_amount, 2) }}</td></tr>
                    @endif
                    <tr class="table-primary">
                        <td class="fw-bold fs-6">TOTAL DUE</td>
                        <td class="text-end fw-bold fs-5">৳ {{ number_format($bill->final_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($bill->admin_note)
        <div class="mt-4 p-3 bg-light rounded">
            <div class="text-muted small fw-semibold text-uppercase mb-1">Admin Note</div>
            <div class="small">{{ $bill->admin_note }}</div>
        </div>
        @endif

        <div class="mt-4 text-muted small text-center border-top pt-3">
            Generated by {{ config('app.name') }} · {{ now()->format('d M Y H:i') }}
        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none { display: none !important; }
    .app-sidebar, .app-header, .main-footer, .scrollToTop, #responsive-overlay, .alert { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    #invoiceCard { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
