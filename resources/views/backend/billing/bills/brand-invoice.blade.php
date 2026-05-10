@extends('backend.master')
@section('title', 'Brand Invoice — ' . $brand->name)

@section('body')
<div class="container" style="max-width:860px">
    <div class="d-flex justify-content-between align-items-center mt-3 mb-3 d-print-none">
        <a href="{{ route('billing.periods.show', $period) }}" class="btn btn-sm btn-outline-secondary">
            <i class="las la-arrow-left me-1"></i> Back to Period
        </a>
        <button class="btn btn-sm btn-primary <!--print-invoice-->" onclick="window.print()">
            <i class="las la-print me-1"></i> Print / Save PDF
        </button>
    </div>

    <div class="card border shadow-sm p-4" id="invoiceCard">

        {{-- Header --}}
        <div class="row mb-4 align-items-start">
            <div class="col-6">
                <h3 class="fw-bold text-primary mb-1">INVOICE</h3>
                <div class="text-muted small">Invoice #: <strong id="bInvoiceId">BINV-{{ str_pad($period->id, 4, '0', STR_PAD_LEFT) }}-{{ str_pad($brand->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
                <div class="text-muted small">Billing Period: <strong>{{ $period->name }}</strong></div>
                <div class="text-muted small">Period: <strong>{{ $period->period_start?->format('d M Y') }} — {{ $period->period_end?->format('d M Y') }}</strong></div>
                <div class="text-muted small">Date: <strong>{{ now()->format('d M Y') }}</strong></div>
            </div>
            <div class="col-6 text-end">
                <div class="fw-bold fs-5">{{ config('app.name') }}</div>
                <div class="text-muted small mt-1">Asset Management System</div>
            </div>
        </div>

        <hr class="my-3">

        {{-- Bill To --}}
        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Bill To</div>
                <div class="fw-bold fs-6">{{ $brand->name }}</div>
                @if($brand->code)
                    <div class="small text-muted">Brand Code: {{ $brand->code }}</div>
                @endif
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small text-uppercase fw-semibold mb-1">Total Stores</div>
                <div class="fw-bold fs-4 text-primary">{{ $bills->count() }}</div>
            </div>
        </div>

        {{-- Store-wise Bill Table --}}
        <table class="table table-bordered table-sm align-middle mb-4">
            <thead class="table-light">
                <tr>
                    <th style="width:40px">#</th>
                    <th>Store</th>
                    <th>Code</th>
                    <th class="text-center" style="width:130px">Status</th>
                    <th class="text-end" style="width:140px">Amount Due (৳)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $i => $bill)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $bill->store?->title }}</td>
                    <td class="text-muted small">{{ $bill->store?->code }}</td>
                    <td class="text-center">
                        @php
                            $sc = match($bill->bill_status) {
                                'draft'      => 'secondary',
                                'issued'     => 'primary',
                                'disputed'   => 'danger',
                                'adjusted'   => 'warning',
                                'finalized'  => 'success',
                                'paid'       => 'dark',
                                default      => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $sc }} text-capitalize">{{ $bill->bill_status }}</span>
                    </td>
                    <td class="text-end fw-semibold">{{ number_format($bill->final_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No bills found for this brand in this period.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th colspan="4" class="text-end fw-bold">GRAND TOTAL</th>
                    <th class="text-end fw-bold fs-6 text-primary">৳ {{ number_format($bills->sum('final_amount'), 2) }}</th>
                </tr>
            </tfoot>
        </table>

        {{-- Payment Note --}}
        <div class="p-3 bg-light rounded small text-muted mb-4">
            <strong>Note:</strong> Please pay the total amount due for each store as per their respective bill status.
            Bills marked as <span class="badge bg-primary">issued</span> or <span class="badge bg-success">finalized</span> are ready for payment.
        </div>

        {{-- Footer --}}
        <div class="mt-3 text-muted small text-center border-top pt-3">
            Generated by {{ config('app.name') }} &nbsp;·&nbsp; {{ now()->format('d M Y H:i') }}
        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none { display: none !important; }
    .footer { visibility: hidden !important; }
    .app-sidebar, .app-header, .main-footer, .scrollToTop, #responsive-overlay, .alert { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    #invoiceCard { border: none !important; box-shadow: none !important; }
    .badge { border: 1px solid #ccc !important; }
}
</style>

@endsection

@push('scripts')

{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>--}}
{{--    <script>--}}
{{--        $(function () {--}}
{{--            $(document).on('click', '.print-invoice', function () {--}}
{{--                // console.log('triggred');--}}
{{--                // let invoiceCard = $('#invoiceCard');--}}
{{--                // html2pdf(document.getElementById('invoiceCard'), {--}}
{{--                //     filename: $('#bInvoiceId').text().trim() || 'brand-invoice.pdf',--}}
{{--                // });--}}
{{--                html2pdf().set({--}}
{{--                    filename: $('#bInvoiceId').text().trim() || 'brand-invoice.pdf',--}}
{{--                    // margin:      10,--}}
{{--                    image:       { type: 'png', quality: 0.98 },--}}
{{--                    html2canvas: { scale: 2, useCORS: true, logging: false },--}}
{{--                    jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' }--}}
{{--                }).from(document.getElementById('invoiceCard')).save();--}}
{{--            })--}}
{{--        })--}}
{{--    </script>--}}
@endpush
