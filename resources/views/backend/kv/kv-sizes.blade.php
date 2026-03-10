@extends('backend.master')

@section('title', 'Key Visual Sizes')

@section('body')
    @include('backend.includes.temp.prototype-callouts')
    <div class="container m-t-50">
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-title mb-1">Key Visual Sizes</div>
                        <p class="text-muted fs-12 mb-0">Manage key visual size definitions.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-size">
                        <i class="ri-add-line me-1"></i> Add Size
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered text-nowrap w-100 align-middle">
                            <thead>
                                <tr>
                                    <th width="45">#</th>
                                    <th>Name</th>
                                    <th>Width</th>
                                    <th>Height</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="110">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kvSizes as $kvSize)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $kvSize->name }}</td>
                                        <td>{{ rtrim(rtrim((string) $kvSize->width, '0'), '.') }}</td>
                                        <td>{{ rtrim(rtrim((string) $kvSize->height, '0'), '.') }}</td>
                                        <td><span class="badge bg-primary-transparent text-uppercase">{{ $kvSize->unit_name }}</span></td>
                                        <td>
                                            @if((int) $kvSize->status === 1)
                                                <span class="badge bg-outline-success">Active</span>
                                            @else
                                                <span class="badge bg-outline-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($kvSize->created_at)->format('d M Y') }}</td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $kvSize->id }}" title="View">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $kvSize->id }}" title="Edit">
                                                    <i class="ri-edit-box-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $kvSize->id }}" data-name="{{ $kvSize->name }}" title="Delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
@include('backend.kv.Modals.keyVisualSize')
@endsection

@push('styles')
<style>
    .btn-list { display: flex; gap: 4px; }
</style>
@endpush

@push('scripts')
@include('backend.includes.plugins.datatable')
<script>
$(function () {
    const sizeModal   = new bootstrap.Modal(document.getElementById('sizeModal'));
    const viewModal   = new bootstrap.Modal(document.getElementById('viewModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    const apiUrl = (id = '') => base_url + 'key-visual-sizes' + (id ? '/' + id : '');

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    /* ---------- helpers ---------- */

    function showToast(message, type = 'success') {
        const $toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3"
                 style="z-index:99999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).appendTo('body');
        setTimeout(() => $toast.remove(), 3500);
    }

    function setLoading($btn, loading) {
        $btn.prop('disabled', loading);
        $btn.find('.spinner-border').toggleClass('d-none', !loading);
    }

    function gcd(a, b) { return b === 0 ? a : gcd(b, a % b); }

    function aspectRatio(w, h) {
        w = Math.round(w); h = Math.round(h);
        if (!w || !h) return 'N/A';
        const d = gcd(w, h);
        return (w / d) + ' : ' + (h / d);
    }

    function trimNum(val) {
        if (val === null || val === undefined || val === '') return 'N/A';
        const n = Number(val);
        return isNaN(n) ? val : (Number.isInteger(n) ? String(n) : String(n));
    }

    function formatDate(str) {
        if (!str) return 'N/A';
        return new Date(str).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    /* ---------- dimension preview ---------- */

    function updateDimPreview() {
        const w = $('#width').val();
        const h = $('#height').val();
        const u = $('#unit_name').val();
        const label = (w && h) ? `${w} x ${h} ${u}` : '-- x --';
        $('#dim-preview').text(label);
    }

    $('#width, #height, #unit_name').on('input change', updateDimPreview);

    /* ---------- status toggle ---------- */

    $('#status_toggle').on('change', function () {
        $('#status').val(this.checked ? '1' : '0');
    });

    /* ---------- form helpers ---------- */

    function clearErrors() {
        $('#sizeForm .is-invalid').removeClass('is-invalid');
        $('#sizeForm .invalid-feedback').text('');
    }

    function resetForm() {
        $('#sizeForm')[0].reset();
        $('#size_id').val('');
        $('#unit_name').val('px');
        $('#status').val('1');
        $('#status_toggle').prop('checked', true);
        $('#dim-preview').text('-- x --');
        clearErrors();
    }

    /* ---------- Add ------ */

    $('#btn-add-size').on('click', function () {
        resetForm();
        $('#sizeModalLabel').text('Add Key Visual Size');
        $('#btn-save .btn-text').text('Save');
        sizeModal.show();
    });

    /* ---------- Edit ---------- */

    $(document).on('click', '.btn-edit', function () {
        resetForm();
        const id = $(this).data('id');

        $.get(apiUrl(id) + '/edit')
            .done(function (data) {
                $('#size_id').val(data.id);
                $('#name').val(data.name || '');
                $('#width').val(data.width ?? '');
                $('#height').val(data.height ?? '');
                $('#unit_name').val(data.unit_name || 'px');
                const active = Number(data.status) === 1;
                $('#status').val(active ? '1' : '0');
                $('#status_toggle').prop('checked', active);
                updateDimPreview();
                $('#sizeModalLabel').text('Edit Key Visual Size');
                $('#btn-save .btn-text').text('Update');
                sizeModal.show();
            })
            .fail(function () { showToast('Failed to load size data.', 'danger'); });
    });

    /* ---------- View ---------- */

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.get(apiUrl(id))
            .done(function (data) {
                const active = Number(data.status) === 1;
                $('#view-name-title').text(data.name || 'N/A');
                $('#view-status-badge').html(active
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-width').text(trimNum(data.width));
                $('#view-height').text(trimNum(data.height));
                $('#view-unit').text((data.unit_name || 'N/A').toLowerCase());
                $('#view-ratio').text(aspectRatio(data.width, data.height));
                $('#view-created').text(formatDate(data.created_at));
                $('#view-updated').text(formatDate(data.updated_at));
                viewModal.show();
            })
            .fail(function () { showToast('Failed to load details.', 'danger'); });
    });

    /* ---------- Delete ---------- */

    $(document).on('click', '.btn-delete', function () {
        $('#delete-size-id').val($(this).data('id'));
        $('#delete-size-name').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btn-confirm-delete').on('click', function () {
        const id  = $('#delete-size-id').val();
        const $btn = $(this);
        setLoading($btn, true);
        $btn.find('.btn-text').text('Deleting…');

        $.ajax({
            url: apiUrl(id),
            type: 'DELETE',
            success: function (res) {
                deleteModal.hide();
                showToast(res.message || 'Deleted successfully.');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to delete.', 'danger');
            },
            complete: function () {
                setLoading($btn, false);
                $btn.find('.btn-text').text('Delete');
            },
        });
    });

    /* ---------- Save / Update ---------- */

    $('#sizeForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const id       = $('#size_id').val();
        const formData = new FormData(this);
        if (id) formData.append('_method', 'PUT');

        const $btn = $('#btn-save');
        setLoading($btn, true);

        $.ajax({
            url: apiUrl(id || ''),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                sizeModal.hide();
                showToast(res.message || 'Saved successfully.');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        $('#error-' + field).text(messages[0]);
                    });
                    return;
                }
                showToast(xhr.responseJSON?.message || 'Something went wrong.', 'danger');
            },
            complete: function () { setLoading($btn, false); },
        });
    });
});
</script>
@endpush
