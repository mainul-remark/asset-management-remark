@extends('backend.master')

@section('title', 'Assets')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="shortByStore">Filter by Store</label>
                            <select name="" id="shortByStore" class="form-control select-ele">
                                <option value="">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->title }} ({{ $store->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Asset Management</div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-asset">
                            <i class="ri-add-line me-1"></i> Add asset
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
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Store</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($assets as $asset)
                                    <tr data-store-id="{{ $asset->store_id ?? '' }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($asset->default_image)
                                                <img class="asset-thumb" src="{{ asset($asset->default_image) }}" alt="{{ $asset->name }}">
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $asset->name }}</div>
                                            <div class="mt-1">
                                                @if((int) $asset->has_kv_slot === 1)
                                                    <span class="badge bg-primary-transparent">Has KV</span>
                                                @endif
                                                @if((int) $asset->has_self === 1)
                                                    <span class="badge bg-primary-transparent">Has Self</span>
                                                    <span class="badge bg-primary-transparent">Total Self: {{ $asset->total_self ?? 0 }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $asset->asset_code }}</td>
                                        <td>{{ $asset->assetType?->name ?? '-' }}</td>
                                        <td>
                                            @if((int) $asset->is_common_asset === 1)
                                                <span class="badge bg-info-transparent">Common</span>
                                            @else
                                                {{ $asset->store?->title ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            {{ !is_null($asset->asset_price) ? number_format($asset->asset_price, 2) : '-' }}
                                            <small class="d-block text-muted">
                                                Min fee: {{ !is_null($asset->minimum_fee) ? number_format($asset->minimum_fee, 2) : '-' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if((int) $asset->status === 1)
                                                <span class="badge bg-outline-success">Active</span>
                                            @else
                                                <span class="badge bg-outline-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $asset->id }}" title="View">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $asset->id }}" title="Edit">
                                                    <i class="ri-edit-box-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $asset->id }}" data-name="{{ $asset->name }}" title="Delete">
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
    <div class="modal fade" id="assetModal" >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="assetModalLabel">Add Asset</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assetForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="asset_id" value="">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="asset_type_id" class="form-label">Asset Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="asset_type_id" name="asset_type_id">
                                    <option value="">-- Select Asset Type --</option>
                                    @foreach($assetTypes as $assetType)
                                        <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-asset_type_id"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter asset name">
                                <div class="invalid-feedback" id="error-name"></div>
                            </div>
                        </div>

                        <div class="row d-none" id="asset-code-row">
                            <div class="col-md-12 mb-3">
                                <label for="asset_code_display" class="form-label">Asset Code</label>
                                <input type="text" class="form-control" id="asset_code_display" value="" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select class="form-select select-ele" id="store_id" name="store_id">
                                    <option value="">-- Select Store --</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }} ({{ $store->code }})</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-store_id"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="asset_price" class="form-label">Asset Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="asset_price" name="asset_price" placeholder="0.00">
                                <div class="invalid-feedback" id="error-asset_price"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="minimum_fee" class="form-label">Minimum Fee</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="minimum_fee" name="minimum_fee" placeholder="0.00">
                                <div class="invalid-feedback" id="error-minimum_fee"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="default_image" class="form-label">Default Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="default_image" name="default_image" accept=".jpg,.jpeg,.png,.webp">
                                <div class="invalid-feedback" id="error-default_image"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="planogram_pdf" class="form-label">Planogram PDF</label>
                                <input type="file" class="form-control" id="planogram_pdf" name="planogram_pdf" accept=".pdf">
                                <div class="invalid-feedback" id="error-planogram_pdf"></div>
                                <a href="#" target="_blank" id="existing-planogram-link" class="d-none small mt-2 d-inline-block">View current planogram</a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <img id="default-image-preview" class="asset-preview d-none" src="" alt="Preview">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block mb-2">Active</label>
                                <div class="toggle-switch">
                                    <label class="switch">
                                        <input type="checkbox" id="status" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block" id="error-status"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block mb-2">Has KV Slot</label>
                                <div class="toggle-switch">
                                    <label class="switch">
                                        <input type="checkbox" id="has_kv_slot">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block" id="error-has_kv_slot"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block mb-2">Has Self</label>
                                <div class="toggle-switch">
                                    <label class="switch">
                                        <input type="checkbox" id="has_self">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block" id="error-has_self"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label d-block mb-2">Is Common Asset</label>
                                <div class="toggle-switch">
                                    <label class="switch">
                                        <input type="checkbox" id="is_common_asset">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                <div class="invalid-feedback d-block" id="error-is_common_asset"></div>
                            </div>
                        </div>
                        <div class="row d-none" id="total-self-row">
                            <div class="col-md-4 mb-3">
                                <label for="total_self" class="form-label">Total Self</label>
                                <input type="number" class="form-control" id="total_self" name="total_self" placeholder="0" min="0" max="127">
                                <div class="invalid-feedback" id="error-total_self"></div>
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

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Asset Details</h6>
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
                                <tr><th>Code</th><td id="view-code"></td></tr>
                                <tr><th>Asset Type</th><td id="view-asset-type"></td></tr>
                                <tr><th>Store</th><td id="view-store"></td></tr>
                                <tr><th>Asset Price</th><td id="view-price"></td></tr>
                                <tr><th>Minimum Fee</th><td id="view-minimum-fee"></td></tr>
                                <tr><th>Status</th><td id="view-status"></td></tr>
                                <tr><th>Common Asset</th><td id="view-common"></td></tr>
                                <tr><th>Has KV Slot</th><td id="view-kv"></td></tr>
                                <tr><th>Has Self</th><td id="view-self"></td></tr>
                                <tr><th>Total Self</th><td id="view-total-self"></td></tr>
                                <tr><th>Planogram</th><td id="view-planogram"></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
                    <h6>Delete Asset</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-asset-name"></strong>?</p>
                    <input type="hidden" id="delete-asset-id">
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
    .toggle-switch { display: flex; align-items: center; }
    .toggle-switch .switch { position: relative; display: inline-block; width: 44px; height: 24px; margin-bottom: 0; }
    .toggle-switch .switch input { opacity: 0; width: 0; height: 0; }
    .toggle-switch .slider { position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; }
    .toggle-switch .slider.round { border-radius: 24px; }
    .toggle-switch .slider.round:before { border-radius: 50%; }
    .toggle-switch .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    .toggle-switch .switch input:checked + .slider { background-color: #5b6edf; }
    .toggle-switch .switch input:checked + .slider:before { transform: translateX(20px); }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
{{--    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>--}}
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {

        const dataTable = $('#data-table').DataTable();
        const assetModal = new bootstrap.Modal(document.getElementById('assetModal'));
        const viewModalEl = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));
        let selectedStoreFilter = '';

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable !== $('#data-table')[0]) {
                return true;
            }

            if (!selectedStoreFilter) {
                return true;
            }

            const rowNode = dataTable.row(dataIndex).node();
            const rowStoreId = String($(rowNode).attr('data-store-id') || '');

            return rowStoreId === selectedStoreFilter;
        });

        $('#shortByStore').on('change', function () {
            selectedStoreFilter = String($(this).val() || '');
            dataTable.draw();
        });

        $('#btn-add-asset').on('click', function () {
            resetForm();
            $('#assetModalLabel').text('Add Asset');
            $('#btn-save .btn-text').text('Save');
            $('#default_image').prop('required', true);
            assetModal.show();
        });

        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#assetModalLabel').text('Edit Asset');
            $('#btn-save .btn-text').text('Update');
            $('#default_image').prop('required', false);

            $.get(base_url + 'assets/' + id + '/edit', function (data) {
                const selectedStoreId = data.store_id ? String(data.store_id) : '';

                $('#asset_id').val(data.id);
                $('#asset_type_id').val(data.asset_type_id);
                $('#name').val(data.name);
                $('#asset-code-row').removeClass('d-none');
                $('#asset_code_display').val(data.asset_code || '-');
                $('#store_id').val(selectedStoreId).trigger('change');
                $('#asset_price').val(data.asset_price);
                $('#minimum_fee').val(data.minimum_fee);
                $('#total_self').val(data.total_self);
                $('#status').prop('checked', parseInt(data.status, 10) === 1);
                $('#has_kv_slot').prop('checked', parseInt(data.has_kv_slot, 10) === 1);
                $('#has_self').prop('checked', parseInt(data.has_self, 10) === 1);
                $('#is_common_asset').prop('checked', parseInt(data.is_common_asset, 10) === 1);

                if (data.default_image) {
                    $('#default-image-preview').attr('src', base_url + data.default_image).removeClass('d-none');
                }

                if (data.planogram_pdf) {
                    $('#existing-planogram-link')
                        .attr('href', base_url + data.planogram_pdf)
                        .removeClass('d-none');
                }

                toggleStoreField();
                if (selectedStoreId && !$('#is_common_asset').is(':checked')) {
                    $('#store_id').val(selectedStoreId).trigger('change');
                }
                toggleTotalSelfField();
                assetModal.show();
            });
        });

        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'assets/' + id, function (data) {
                $('#view-name').text(data.name || '-');
                $('#view-code').text(data.asset_code || '-');
                $('#view-asset-type').text(data.asset_type ? data.asset_type.name : '-');
                if (parseInt(data.is_common_asset, 10) === 1) {
                    $('#view-store').html('<span class="badge bg-info-transparent">Common</span>');
                } else {
                    $('#view-store').text(data.store ? data.store.title : '-');
                }
                $('#view-price').text(data.asset_price ? Number(data.asset_price).toFixed(2) : '-');
                $('#view-minimum-fee').text(data.minimum_fee ? Number(data.minimum_fee).toFixed(2) : '-');
                $('#view-status').html(parseInt(data.status, 10) === 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-common').text(parseInt(data.is_common_asset, 10) === 1 ? 'Yes' : 'No');
                $('#view-kv').text(parseInt(data.has_kv_slot, 10) === 1 ? 'Yes' : 'No');
                $('#view-self').text(parseInt(data.has_self, 10) === 1 ? 'Yes' : 'No');
                $('#view-total-self').text(parseInt(data.has_self, 10) === 1 ? (data.total_self ?? 0) : '-');
                $('#view-planogram').html(data.planogram_pdf
                    ? '<a href="' + (base_url + data.planogram_pdf) + '" target="_blank" class="btn btn-sm btn-info-light btn-wave">View PDF</a>'
                    : '-');

                if (data.default_image) {
                    $('#view-image').attr('src', base_url + data.default_image).show();
                } else {
                    $('#view-image').hide();
                }

                viewModalEl.show();
            });
        });

        $(document).on('click', '.btn-delete', function () {
            $('#delete-asset-id').val($(this).data('id'));
            $('#delete-asset-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-asset-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'assets/' + id,
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    showToast('Failed to delete asset.', 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        $('#assetForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#asset_id').val();
            const url = id ? base_url + 'assets/' + id : base_url + 'assets';
            const formData = new FormData(this);

            formData.set('status', $('#status').is(':checked') ? 1 : 0);
            formData.set('has_kv_slot', $('#has_kv_slot').is(':checked') ? 1 : 0);
            formData.set('has_self', $('#has_self').is(':checked') ? 1 : 0);
            formData.set('is_common_asset', $('#is_common_asset').is(':checked') ? 1 : 0);

            if ($('#is_common_asset').is(':checked')) {
                formData.set('store_id', '');
            }

            if (!$('#has_self').is(':checked')) {
                formData.set('total_self', '');
            }

            if (id) {
                formData.append('_method', 'PUT');
            }

            $('#btn-save').prop('disabled', true);
            $('#btn-spinner').removeClass('d-none');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    assetModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors || {};
                        $.each(errors, function (field, messages) {
                            const input = $('#' + field);
                            const errorBox = $('#error-' + field);

                            if (input.length) {
                                input.addClass('is-invalid');
                            }
                            if (errorBox.length) {
                                errorBox.text(messages[0]);
                            }
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

        $('#is_common_asset').on('change', function () {
            toggleStoreField();
        });

        $('#store_id').on('select2:opening', function (e) {
            if ($('#is_common_asset').is(':checked')) {
                e.preventDefault();
            }
        });

        $('#has_self').on('change', function () {
            toggleTotalSelfField();
        });

        function toggleStoreField() {
            const $store = $('#store_id');
            const $select2Container = $store.next('.select2-container');
            const isCommon = $('#is_common_asset').is(':checked');

            if (isCommon) {
                $store.val('').prop('disabled', true).trigger('change');
                $select2Container.addClass('select2-container--disabled');
                $select2Container.find('.select2-selection')
                    .attr('aria-disabled', 'true')
                    .css('pointer-events', 'none')
                    .css('opacity', '0.65');
            } else {
                $store.prop('disabled', false).trigger('change');
                $select2Container.removeClass('select2-container--disabled');
                $select2Container.find('.select2-selection')
                    .attr('aria-disabled', 'false')
                    .css('pointer-events', '')
                    .css('opacity', '');
            }
        }

        function toggleTotalSelfField() {
            const hasSelf = $('#has_self').is(':checked');
            if (hasSelf) {
                $('#total-self-row').removeClass('d-none');
            } else {
                $('#total-self-row').addClass('d-none');
                $('#total_self').val('').removeClass('is-invalid');
                $('#error-total_self').text('');
            }
        }

        function resetForm() {
            $('#assetForm')[0].reset();
            $('#asset_id').val('');
            $('#asset-code-row').addClass('d-none');
            $('#asset_code_display').val('');
            $('#default-image-preview').addClass('d-none').attr('src', '');
            $('#existing-planogram-link').addClass('d-none').attr('href', '#');
            $('#status').prop('checked', true);
            $('#has_kv_slot').prop('checked', false);
            $('#has_self').prop('checked', false);
            $('#is_common_asset').prop('checked', false);
            $('#store_id').val('').prop('disabled', false).trigger('change.select2');
            toggleTotalSelfField();
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
