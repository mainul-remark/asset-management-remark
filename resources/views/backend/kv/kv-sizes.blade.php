@extends('backend.master')

@section('title', 'Key Visual Sizes')

@section('body')
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
                                    <th>Height</th>
                                    <th>Width</th>
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
                                        <td>{{ rtrim(rtrim((string) $kvSize->height, '0'), '.') }}</td>
                                        <td>{{ rtrim(rtrim((string) $kvSize->width, '0'), '.') }}</td>
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
    const sizeModal = new bootstrap.Modal(document.getElementById('sizeModal'));
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    const apiUrl = (id = '') => base_url + 'key-visual-sizes' + (id ? '/' + id : '');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showToast(message, type = 'success') {
        const $toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:99999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).appendTo('body');
        setTimeout(() => $toast.remove(), 3500);
    }

    function clearErrors() {
        $('#sizeForm .is-invalid').removeClass('is-invalid');
        $('#sizeForm .invalid-feedback').text('');
    }

    function resetForm() {
        $('#sizeForm')[0].reset();
        $('#size_id').val('');
        $('#unit_name').val('px');
        $('#status').val('1');
        clearErrors();
    }

    function formatDecimal(value) {
        if (value === null || value === undefined || value === '') {
            return 'N/A';
        }
        const number = Number(value);
        if (Number.isNaN(number)) {
            return value;
        }
        return Number.isInteger(number) ? String(number) : String(number);
    }

    $('#btn-add-size').on('click', function () {
        resetForm();
        $('#sizeModalLabel').text('Add Key Visual Size');
        $('#btn-save .btn-text').text('Save');
        sizeModal.show();
    });

    $(document).on('click', '.btn-edit', function () {
        resetForm();
        const id = $(this).data('id');

        $.get(apiUrl(id) + '/edit')
            .done(function (data) {
                $('#size_id').val(data.id);
                $('#name').val(data.name || '');
                $('#height').val(data.height ?? '');
                $('#width').val(data.width ?? '');
                $('#unit_name').val(data.unit_name || 'px');
                $('#status').val(Number(data.status) === 1 ? '1' : '0');
                $('#sizeModalLabel').text('Edit Key Visual Size');
                $('#btn-save .btn-text').text('Update');
                sizeModal.show();
            })
            .fail(function () {
                showToast('Failed to load size data.', 'danger');
            });
    });

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.get(apiUrl(id))
            .done(function (data) {
                $('#view-name').text(data.name || 'N/A');
                $('#view-height').text(formatDecimal(data.height));
                $('#view-width').text(formatDecimal(data.width));
                $('#view-unit').text((data.unit_name || 'N/A').toUpperCase());
                $('#view-status').html(Number(data.status) === 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-created').text(
                    data.created_at
                        ? new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                        : 'N/A'
                );
                $('#view-updated').text(
                    data.updated_at
                        ? new Date(data.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
                        : 'N/A'
                );
                viewModal.show();
            })
            .fail(function () {
                showToast('Failed to load details.', 'danger');
            });
    });

    $(document).on('click', '.btn-delete', function () {
        $('#delete-size-id').val($(this).data('id'));
        $('#delete-size-name').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btn-confirm-delete').on('click', function () {
        const id = $('#delete-size-id').val();
        const $btn = $(this);

        $btn.prop('disabled', true);
        $btn.find('.btn-text').text('Deleting...');
        $btn.find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: apiUrl(id),
            type: 'DELETE',
            success: function (res) {
                deleteModal.hide();
                showToast(res.message || 'Deleted successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to delete size.', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').text('Yes, Delete');
                $btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    $('#sizeForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const id = $('#size_id').val();
        const formData = new FormData(this);

        if (id) {
            formData.append('_method', 'PUT');
        }

        $('#btn-save').prop('disabled', true);
        $('#btn-spinner').removeClass('d-none');

        $.ajax({
            url: apiUrl(id || ''),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                sizeModal.hide();
                showToast(res.message || 'Saved successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        const msg = messages[0];
                        const $field = $('#' + field);
                        $field.addClass('is-invalid');
                        $('#error-' + field).text(msg);
                    });
                    return;
                }

                showToast(xhr.responseJSON?.message || 'Something went wrong. Please try again.', 'danger');
            },
            complete: function () {
                $('#btn-save').prop('disabled', false);
                $('#btn-spinner').addClass('d-none');
            }
        });
    });
});
</script>
@endpush
