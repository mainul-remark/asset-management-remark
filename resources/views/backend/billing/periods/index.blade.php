@extends('backend.master')
@section('title', 'Billing Periods')

@section('body')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4 mt-3">
        <div>
            <h4 class="fw-semibold mb-1">Billing Periods</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Billing Periods</li>
                </ol>
            </nav>
        </div>
        @allowed('billing.periods.create')
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPeriodModal">
            <i class="las la-plus me-1"></i> New Period
        </button>
        @endallowed
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Period Name</th>
                            <th>Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Bills</th>
                            <th>Status</th>
                            <th>Generated At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $period->name }}</td>
                            <td><span class="badge bg-secondary text-capitalize">{{ $period->period_type }}</span></td>
                            <td>{{ $period->period_start?->format('d M Y') }}</td>
                            <td>{{ $period->period_end?->format('d M Y') }}</td>
                            <td><span class="badge bg-info">{{ $period->store_brand_bills_count }}</span></td>
                            <td>
                                @php
                                    $statusColor = match($period->status) {
                                        'open'       => 'warning',
                                        'generating' => 'info',
                                        'generated'  => 'primary',
                                        'finalized'  => 'success',
                                        default      => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-capitalize">{{ $period->status }}</span>
                            </td>
                            <td class="text-muted small">{{ $period->generated_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    @if(!$period->isFinalized())
                                        @allowed('billing.periods.generate')
                                        <button class="btn btn-sm btn-outline-primary btn-generate"
                                            data-id="{{ $period->id }}"
                                            data-name="{{ $period->name }}"
                                            title="Generate Bills">
                                            <i class="las la-cog"></i>
                                        </button>
                                        @endallowed
                                    @endif
                                    @allowed('billing.periods.show')
                                    <a href="{{ route('billing.periods.show', $period) }}"
                                       class="btn btn-sm btn-outline-secondary" title="View Bills">
                                        <i class="las la-eye"></i>
                                    </a>
                                    @endallowed
                                    @if($period->isGenerated() && !$period->isFinalized())
                                        @allowed('billing.periods.finalize')
                                        <button class="btn btn-sm btn-outline-success btn-finalize-period"
                                            data-id="{{ $period->id }}"
                                            data-name="{{ $period->name }}"
                                            title="Finalize Period">
                                            <i class="las la-lock"></i>
                                        </button>
                                        @endallowed
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="las la-file-invoice-dollar fs-2 d-block mb-2"></i>
                                No billing periods created yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($periods->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $periods->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('modal')
@allowed('billing.periods.create')
<!-- Create Period Modal -->
<div class="modal fade" id="createPeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Billing Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="createPeriodAlert" class="d-none"></div>
                <form id="createPeriodForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Period Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. May 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Period Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="period_type" required>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="period_start" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="period_end" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary btn-sm" id="savePeriodBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="savePeriodSpinner"></span>
                    Create Period
                </button>
            </div>
        </div>
    </div>
</div>
@endallowed
@endsection

@push('scripts')
    @include('backend.includes.plugins.sweetalert2')
    @include('backend.includes.plugins.toastr')
<script>
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Create Period
    document.getElementById('savePeriodBtn').addEventListener('click', function () {
        const form    = document.getElementById('createPeriodForm');
        const btn     = this;
        const spinner = document.getElementById('savePeriodSpinner');
        const alert   = document.getElementById('createPeriodAlert');

        alert.className = 'd-none';
        spinner.classList.remove('d-none');
        btn.disabled = true;

        fetch('{{ route('billing.periods.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(Object.fromEntries(new FormData(form))),
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                window.location.href = res.data.redirect;
            } else {
                alert.className = 'alert alert-danger';
                alert.textContent = res.message ?? 'An error occurred.';
            }
        })
        .catch(() => {
            alert.className = 'alert alert-danger';
            alert.textContent = 'Network error.';
        })
        .finally(() => { spinner.classList.add('d-none'); btn.disabled = false; });
    });

    // Generate Bills
    document.querySelectorAll('.btn-generate').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const name = this.dataset.name;
            // if (!confirm(`Generate bills for "${name}"?\n\nExisting draft bills for this period will be regenerated.`)) return;
            Swal.fire({
                title: "Are you sure?",
                text: `Generate bills for "${name}"?\n\nExisting draft bills for this period will be regenerated.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Generate!"
            }).then((result) => {
                if (!result.isConfirmed) return;

                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/billing/periods/${id}/generate`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            // alert(res.message);
                            // toastr.success(res.message)
                            Swal.fire({
                                position: "middle",
                                icon: "success",
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        } else {
                            // alert('Error: ' + res.message);
                            toastr.success('Error: ' + res.message)
                            // setTimeout(function () {
                            //     location.reload();
                            // }, 1500);
                            this.disabled = false;
                            this.innerHTML = '<i class="las la-cog"></i>';
                        }
                    })
                    .catch(error => {
                        toastr.error('Something went wrong.');

                        btn.disabled = false;
                        btn.innerHTML = 'Generate';

                        // console.error(error);
                    });
            });


        });
    });

    // Finalize Period
    document.querySelectorAll('.btn-finalize-period').forEach(btn => {
        btn.addEventListener('click', function () {
            const id   = this.dataset.id;
            const name = this.dataset.name;
            // if (!confirm(`Finalize period "${name}"?\n\n This will lock the period. No more bill regeneration will be allowed.`)) return;
            Swal.fire({
                title: "Are you sure?",
                text: `Finalize period "${name}"?\n\n This will lock the period. No more bill regeneration will be allowed.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Finalize!"
            }).then((result) => {
                if (!result.isConfirmed) return ;

                fetch(`/billing/periods/${id}/finalize`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                })
                    .then(r => r.json())
                    .then(res => {
                        // alert(res.message);
                        toastr.success(res.message);
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    })
                    .catch(error => {
                        toastr.error('Something went wrong.');
                    });
            });


        });
    });
})();
</script>
@endpush
