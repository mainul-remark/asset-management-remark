@extends('backend.master')

@section('title', 'Asset Types')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Asset Type Management</div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-asset-type">
                            <i class="ri-add-line me-1"></i> Add asset type
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Resolution</th>
                                        <th>Info</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($assetTypes as $assetType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($assetType->default_image)
                                                <img class="asset-thumb" src="{{ asset($assetType->default_image) }}" alt="{{ $assetType->name ?? '--' }}">
                                            @endif
                                        </td>
                                        <td>{{ $assetType->name }}</td>
                                        <td>{{ $assetType->default_price ? number_format($assetType->default_price, 2) : '-' }}</td>
                                        <td>
                                            <span>
                                                {{ $assetType->height }}x{{ $assetType->width }}@if(!is_null($assetType->depth)){{ 'x'.$assetType->depth }}@endif
                                                {{ $assetType->dimension_unit_name ?? '' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($assetType->is_digital == 1)
                                                <span class="badge bg-primary-transparent">Digital</span>
                                            @endif
                                            @if($assetType->has_kv_space == 1)
                                                <span class="badge bg-primary-transparent">Has KV</span>
                                            @endif
                                            <span class="badge bg-primary-transparent">Total {{ $assetType->total_self ?? 0 }} Self</span>
                                        </td>
                                        <td>
                                            @if($assetType->status == 1)
                                                <span class="badge bg-outline-success">Active</span>
                                            @else
                                                <span class="badge bg-outline-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $assetType->id }}" title="View"><i class="ri-eye-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $assetType->id }}" title="Edit"><i class="ri-edit-box-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $assetType->id }}" data-name="{{ $assetType->name }}" title="Delete"><i class="ri-delete-bin-line"></i></button>
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
    {{-- Create / Edit Modal --}}
    <div class="modal fade" id="assetTypeModal" tabindex="-1" aria-labelledby="assetTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="assetTypeModalLabel">Add Asset Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assetTypeForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="asset_type_id" value="">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter asset type name">
                                <div class="invalid-feedback" id="error-name"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="default_price" class="form-label">Default Price</label>
                                <input type="number" step="0.01" class="form-control" id="default_price" name="default_price" placeholder="0.00">
                                <div class="invalid-feedback" id="error-default_price"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="height" class="form-label">Height</label>
                                <input type="number" step="0.01" class="form-control" id="height" name="height" placeholder="0">
                                <div class="invalid-feedback" id="error-height"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="width" class="form-label">Width</label>
                                <input type="number" step="0.01" class="form-control" id="width" name="width" placeholder="0">
                                <div class="invalid-feedback" id="error-width"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="depth" class="form-label">Depth</label>
                                <input type="number" step="0.01" class="form-control" id="depth" name="depth" placeholder="0">
                                <div class="invalid-feedback" id="error-depth"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dimension_unit_name" class="form-label">Dimension Unit</label>
                                <select class="form-select" id="dimension_unit_name" name="dimension_unit_name">
                                    <option value="">� Select Unit �</option>
                                    <option value="px">px</option>
                                    <option value="in">in</option>
                                    <option value="ft">ft</option>
                                    <option value="cm">cm</option>
                                    <option value="mm">mm</option>
                                    <option value="m">m</option>
                                    <option value="yd">yd</option>
                                </select>
                                <div class="invalid-feedback" id="error-dimension_unit_name"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="total_self" class="form-label">Total Self</label>
                                <input type="number" class="form-control" id="total_self" name="total_self" placeholder="0">
                                <div class="invalid-feedback" id="error-total_self"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="default_image" class="form-label">Default Image</label>
                                <input type="file" class="form-control" id="default_image" name="default_image" accept=".jpg,.jpeg,.png,.webp">
                                <div class="invalid-feedback" id="error-default_image"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <img id="default-image-preview" class="asset-preview d-none" src="" alt="Preview">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="status" checked>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                                <div class="invalid-feedback" id="error-status"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_digital">
                                    <label class="form-check-label" for="is_digital">Digital Asset</label>
                                </div>
                                <div class="invalid-feedback" id="error-is_digital"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_kv_space" checked>
                                    <label class="form-check-label" for="has_kv_space">Has KV Space</label>
                                </div>
                                <div class="invalid-feedback" id="error-has_kv_space"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <span class="btn-text">Save</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Asset Type Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="view-image" class="asset-preview" src="" alt="" style="display:none;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-sm">
                                <tr><th width="40%">Name</th><td id="view-name"></td></tr>
                                <tr><th>Default Price</th><td id="view-price"></td></tr>
                                <tr><th>Dimension</th><td id="view-dimension"></td></tr>
                                <tr><th>Status</th><td id="view-status"></td></tr>
                                <tr><th>Digital</th><td id="view-digital"></td></tr>
                                <tr><th>KV Space</th><td id="view-kv"></td></tr>
                                <tr><th>Total Self</th><td id="view-total-self"></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
                    <h6>Delete Asset Type</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-asset-type-name"></strong>?</p>
                    <input type="hidden" id="delete-asset-type-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .btn-list { display: flex; gap: 4px; }
    .asset-thumb { width: 42px; height: 42px; object-fit: cover; border-radius: 6px; border: 1px solid #e6e8f0; }
    .asset-preview { width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid #e6e8f0; }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    <script>
    $(document).ready(function () {
        const assetTypeModal = new bootstrap.Modal(document.getElementById('assetTypeModal'));
        const viewModalEl = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));

        $('#btn-add-asset-type').on('click', function () {
            resetForm();
            $('#assetTypeModalLabel').text('Add Asset Type');
            $('#btn-save .btn-text').text('Save');
            assetTypeModal.show();
        });

        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#assetTypeModalLabel').text('Edit Asset Type');
            $('#btn-save .btn-text').text('Update');

            $.get(base_url + 'asset-types/' + id + '/edit', function (data) {
                $('#asset_type_id').val(data.id);
                $('#name').val(data.name);
                $('#default_price').val(data.default_price);
                $('#height').val(data.height);
                $('#width').val(data.width);
                $('#depth').val(data.depth);
                $('#dimension_unit_name').val(data.dimension_unit_name || '');
                $('#total_self').val(data.total_self);
                $('#status').prop('checked', data.status == 1);
                $('#is_digital').prop('checked', data.is_digital == 1);
                $('#has_kv_space').prop('checked', data.has_kv_space == 1);

                if (data.default_image) {
                    $('#default-image-preview').attr('src', base_url + data.default_image).removeClass('d-none');
                }

                assetTypeModal.show();
            });
        });

        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'asset-types/' + id, function (data) {
                $('#view-name').text(data.name);
                $('#view-price').text(data.default_price ? Number(data.default_price).toFixed(2) : '�');
                $('#view-dimension').text((data.height ?? 0) + 'x' + (data.width ?? 0) + (data.depth !== null ? 'x' + data.depth : '') + ' ' + (data.dimension_unit_name || ''));
                $('#view-status').html(data.status == 1 ? '<span class="badge bg-success-transparent">Active</span>' : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-digital').text(data.is_digital == 1 ? 'Yes' : 'No');
                $('#view-kv').text(data.has_kv_space == 1 ? 'Yes' : 'No');
                $('#view-total-self').text(data.total_self ?? 0);

                if (data.default_image) {
                    $('#view-image').attr('src', base_url + data.default_image).show();
                } else {
                    $('#view-image').hide();
                }

                viewModalEl.show();
            });
        });

        $(document).on('click', '.btn-delete', function () {
            $('#delete-asset-type-id').val($(this).data('id'));
            $('#delete-asset-type-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-asset-type-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'asset-types/' + id,
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    showToast('Failed to delete asset type.', 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        $('#assetTypeForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#asset_type_id').val();
            const url = id ? base_url + 'asset-types/' + id : base_url + 'asset-types';
            const formData = new FormData(this);

            formData.set('status', $('#status').is(':checked') ? 1 : 0);
            formData.set('is_digital', $('#is_digital').is(':checked') ? 1 : 0);
            formData.set('has_kv_space', $('#has_kv_space').is(':checked') ? 1 : 0);

            if (id) formData.append('_method', 'PUT');

            $('#btn-save').prop('disabled', true);
            $('#btn-spinner').removeClass('d-none');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    assetTypeModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            $('#' + field).addClass('is-invalid');
                            $('#error-' + field).text(messages[0]);
                        });
                    } else {
                        showToast('Something went wrong.', 'danger');
                    }
                },
                complete: function () {
                    $('#btn-save').prop('disabled', false);
                    $('#btn-spinner').addClass('d-none');
                }
            });
        });

        $('#default_image').on('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                $('#default-image-preview').addClass('d-none').attr('src', '');
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#default-image-preview').attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        });

        function resetForm() {
            $('#assetTypeForm')[0].reset();
            $('#asset_type_id').val('');
            $('#default-image-preview').addClass('d-none').attr('src', '');
            $('#dimension_unit_name').val('');
            $('#status').prop('checked', true);
            $('#has_kv_space').prop('checked', true);
            clearErrors();
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function showToast(message, type) {
            const toast = $(`
                <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `).appendTo('body');
            setTimeout(() => toast.remove(), 3000);
        }
    });
    </script>
@endpush
