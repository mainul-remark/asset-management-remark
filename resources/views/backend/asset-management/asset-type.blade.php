@extends('backend.master')

@section('title', 'Asset Types')

@section('body')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Asset Category Management</div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-asset-type">
                            <i class="ri-add-line me-1"></i> Add Asset Category
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
{{--                                        <th>Default Fee</th>--}}
                                        <th>Dimensions</th>
                                        <th>Properties</th>
                                        <th>Status</th>
                                        <th width="110">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($assetTypes as $assetType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($assetType->default_image)
                                                <img class="asset-thumb" src="{{ asset($assetType->default_image) }}" alt="{{ $assetType->name }}">
                                            @else
                                                <div class="asset-thumb-empty"><i class="ri-image-line"></i></div>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $assetType->name }}</td>
{{--                                        <td>{{ $assetType->default_price ? '৳ '.number_format($assetType->default_price, 2) : '—' }}</td>--}}
                                        <td>
                                            @if($assetType->height || $assetType->width)
                                                <span class="text-muted fs-11">
                                                    {{ $assetType->height ?? 0 }} × {{ $assetType->width ?? 0 }}@if($assetType->depth > 0) × {{ $assetType->depth }}@endif
                                                    {{ $assetType->dimension_unit_name ?? '' }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assetType->is_digital)
                                                <span class="badge bg-info-transparent me-1">Digital</span>
                                            @endif
                                            @if($assetType->has_kv_space)
                                                <span class="badge bg-warning-transparent me-1">KV Space</span>
                                            @endif
                                            <span class="badge bg-light text-dark border">{{ $assetType->total_self ?? 0 }} Shelf</span>
                                        </td>
                                        <td>
                                            @if($assetType->status == 1)
                                                <span class="badge bg-success-transparent">Active</span>
                                            @else
                                                <span class="badge bg-danger-transparent">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view"
                                                    data-id="{{ $assetType->id }}" title="View">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit"
                                                    data-id="{{ $assetType->id }}" title="Edit">
                                                    <i class="ri-edit-box-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete"
                                                    data-id="{{ $assetType->id }}" data-name="{{ $assetType->name }}" title="Delete">
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

    {{-- ── Create / Edit Modal ──────────────────────────────────────────────── --}}
    <div class="modal fade" id="assetTypeModal" tabindex="-1" aria-labelledby="assetTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold" id="assetTypeModalLabel">
                        <i class="ri-archive-line me-2 text-primary"></i>Add Asset Type
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="assetTypeForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    @csrf
                    <input type="hidden" id="asset_type_id" value="">

                    <div class="modal-body">

                        {{-- ── Section: Basic Information ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-information-line me-1"></i>Basic Information</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">
                                        Asset Category Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="e.g. Billboard, LED Screen, Banner">
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Dimensions ── --}}
                        <div class="form-section mb-4">
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                <p class="form-section-label mb-0 border-0 pb-0"><i class="ri-ruler-line me-1"></i>Dimensions</p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input form-checked-success" type="checkbox"
                                        role="switch" id="has_default_dimension" name="has_default_dimension">
                                    <label class="form-check-label fw-medium fs-12" for="has_default_dimension">Has Default Dimension</label>
                                </div>
                            </div>
                            <div id="dimension-fields" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <label for="height" class="form-label">Height</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            id="height" name="height" placeholder="0">
                                        <div class="invalid-feedback" id="error-height"></div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label for="width" class="form-label">Width</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                            id="width" name="width" placeholder="0">
                                        <div class="invalid-feedback" id="error-width"></div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label for="dimension_unit_name" class="form-label">Unit</label>
                                        <select class="form-select" id="dimension_unit_name" name="dimension_unit_name">
                                            <option value="">— Select —</option>
                                            <option value="px">px</option>
                                            <option value="in">in (inch)</option>
                                            <option value="ft">ft (feet)</option>
                                            <option value="cm">cm</option>
                                            <option value="mm">mm</option>
                                            <option value="m">m (meter)</option>
                                            <option value="yd">yd (yard)</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-dimension_unit_name"></div>
                                    </div>
                                </div>
                            </div>
                            <p id="dimension-hint" class="text-muted fs-12 mb-0">Enable <strong>Has Default Dimension</strong> to set height, width and depth.</p>
                        </div>

                        {{-- ── Section: Storage & Image ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-image-line me-1"></i>Storage & Image</p>
                            <div class="row g-3 align-items-start">
                                <div class="col-12">
                                    <label class="form-label">Default Image</label>
                                    <div class="image-upload-zone" id="imageUploadZone">
                                        <div id="upload-placeholder" class="upload-placeholder-inner">
                                            <i class="ri-upload-cloud-2-line fs-2 text-muted"></i>
                                            <p class="mb-1 text-muted fs-12 mt-1">Click or drag image here</p>
                                            <p class="mb-0 fs-11" style="color:#adb5bd">JPG, JPEG, PNG, WEBP &mdash; Max 2 MB</p>
                                        </div>
                                        <img id="default-image-preview" class="d-none" src="" alt="Preview">
                                        <input type="file" class="d-none" id="default_image" name="default_image"
                                            accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="invalid-feedback d-block" id="error-default_image"></div>
                                    <div id="remove-image-wrap" class="d-none mt-1">
                                        <button type="button" class="btn btn-sm btn-danger-light" id="btn-remove-image">
                                            <i class="ri-delete-bin-line me-1"></i>Remove Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- ── Section: Options ── --}}
                        <div class="form-section">
                            <p class="form-section-label"><i class="ri-settings-3-line me-1"></i>Options</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-success" type="checkbox"
                                                role="switch" id="status" checked>
                                            <label class="form-check-label fw-medium" for="status">Active Status</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Enable this asset type in the system</small>
                                        <div class="invalid-feedback" id="error-status"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-info" type="checkbox"
                                                role="switch" id="is_digital">
                                            <label class="form-check-label fw-medium" for="is_digital">Digital Asset</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Mark as a non-physical digital type</small>
                                        <div class="invalid-feedback" id="error-is_digital"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-warning" type="checkbox"
                                                role="switch" id="has_kv_space" checked>
                                            <label class="form-check-label fw-medium" for="has_kv_space">Has KV Space</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Supports key-value metadata storage</small>
                                        <div class="invalid-feedback" id="error-has_kv_space"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-primary" type="checkbox"
                                                role="switch" id="need_asset_image">
                                            <label class="form-check-label fw-medium" for="need_asset_image">Need Asset Image</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset requires an image upload</small>
                                        <div class="invalid-feedback" id="error-need_asset_image"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-secondary" type="checkbox"
                                                role="switch" id="need_asset_planogram">
                                            <label class="form-check-label fw-medium" for="need_asset_planogram">Need Planogram</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset requires a planogram layout</small>
                                        <div class="invalid-feedback" id="error-need_asset_planogram"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-teal" type="checkbox"
                                                role="switch" id="has_asset_self">
                                            <label class="form-check-label fw-medium" for="has_asset_self">Has Asset Self</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset contains self-managed sections</small>
                                        <div class="invalid-feedback" id="error-has_asset_self"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-none" id="total-self-wrap">
                                    <label for="total_self" class="form-label">Total Shelf</label>
                                    <input type="number" min="0" class="form-control"
                                        id="total_self" name="total_self" placeholder="0">
                                    <div class="invalid-feedback" id="error-total_self"></div>
                                    <div class="form-text">Number of shelf units</div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Save</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ── View Modal ───────────────────────────────────────────────────────── --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">
                        <i class="ri-eye-line me-2 text-info"></i>Asset Type Details
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="view-image-box">
                                <img id="view-image" class="d-none" src="" alt="">
                                <div id="view-image-placeholder">
                                    <i class="ri-image-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered table-sm align-middle mb-0">
                                <tr>
                                    <th width="38%" class="text-muted fw-medium fs-12">Name</th>
                                    <td id="view-name" class="fw-semibold"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Default Price</th>
                                    <td id="view-price"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Dimensions</th>
                                    <td id="view-dimension"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Total Shelf</th>
                                    <td id="view-total-self"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Status</th>
                                    <td id="view-status"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Digital Asset</th>
                                    <td id="view-digital"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">KV Space</th>
                                    <td id="view-kv"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Default Dimension</th>
                                    <td id="view-has-default-dimension"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Need Asset Image</th>
                                    <td id="view-need-asset-image"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Need Planogram</th>
                                    <td id="view-need-planogram"></td>
                                </tr>
                                <tr>
                                    <th class="text-muted fw-medium fs-12">Has Asset Self</th>
                                    <td id="view-has-asset-self"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Delete Confirmation Modal ────────────────────────────────────────── --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4 pb-2">
                    <div class="mb-3">
                        <span class="avatar avatar-lg bg-danger-transparent rounded-circle">
                            <i class="ri-delete-bin-line text-danger fs-24"></i>
                        </span>
                    </div>
                    <h6 class="fw-semibold mb-1">Delete Asset Type?</h6>
                    <p class="text-muted fs-13 mb-0">
                        You are about to delete<br>
                        <strong id="delete-asset-type-name" class="text-dark"></strong>
                    </p>
                    <p class="text-danger fs-11 mt-1 mb-0">This action cannot be undone.</p>
                    <input type="hidden" id="delete-asset-type-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger px-4" id="btn-confirm-delete">
                        <span class="btn-text">Delete</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    /* ── Table ── */
    .btn-list { display: flex; gap: 4px; }
    .asset-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid var(--default-border); }
    .asset-thumb-empty { width: 40px; height: 40px; border-radius: 6px; border: 1px dashed var(--default-border); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1rem; }

    /* ── Form Sections ── */
    .form-section-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--primary-color);
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--default-border);
        margin-bottom: 1rem;
    }

    /* ── Image Upload Zone ── */
    .image-upload-zone {
        border: 2px dashed var(--default-border);
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease;
        min-height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .image-upload-zone:hover,
    .image-upload-zone.drag-over { border-color: var(--primary-color); background: rgba(var(--primary-rgb), 0.04); }
    .upload-placeholder-inner { pointer-events: none; }
    .image-upload-zone #default-image-preview { max-height: 120px; max-width: 100%; border-radius: 6px; object-fit: cover; }

    /* ── Switch Option Cards ── */
    .switch-option-card {
        background: var(--custom-white);
        border: 1px solid var(--default-border);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        height: 100%;
    }

    /* ── View Image Box ── */
    .view-image-box { border-radius: 8px; overflow: hidden; }
    .view-image-box img { width: 100%; border-radius: 8px; object-fit: cover; }
    #view-image-placeholder {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: var(--light);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--text-muted);
        border: 1px dashed var(--default-border);
    }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    <script>
    $(document).ready(function () {

        // ── Bootstrap modals & global AJAX setup ──────────────────────────────
        const assetTypeModal = new bootstrap.Modal(document.getElementById('assetTypeModal'));
        const viewModalEl    = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl  = new bootstrap.Modal(document.getElementById('deleteModal'));

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // ── Utility helpers ───────────────────────────────────────────────────
        const apiUrl = id => base_url + 'asset-types' + (id ? '/' + id : '');

        const badge = (cond, trueColor, trueLabel, falseColor, falseLabel) =>
            cond ? `<span class="badge bg-${trueColor}-transparent">${trueLabel}</span>`
                 : (falseColor ? `<span class="badge bg-${falseColor}-transparent">${falseLabel}</span>`
                               : `<span class="text-muted">${falseLabel}</span>`);

        function setBtnLoading($btn, loading, loadingText = '') {
            $btn.prop('disabled', loading);
            if (loadingText) $btn.find('.btn-text').text(loadingText);
            $btn.find('.spinner-border').toggleClass('d-none', !loading);
        }

        function showToast(message, type) {
            const $toast = $(`
                <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `).appendTo('body');
            setTimeout(() => $toast.remove(), 3000);
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        // ── Image upload zone ──────────────────────────────────────────────────
        const uploadZone = document.getElementById('imageUploadZone');
        const fileInput  = document.getElementById('default_image');

        uploadZone.addEventListener('click',     e  => { if (!e.target.closest('#btn-remove-image')) fileInput.click(); });
        uploadZone.addEventListener('dragover',  e  => { e.preventDefault(); uploadZone.classList.add('drag-over'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault();
            uploadZone.classList.remove('drag-over');
            if (e.dataTransfer.files[0]) setImagePreview(e.dataTransfer.files[0]);
        });

        $('#default_image').on('change', function () {
            if (this.files[0]) setImagePreview(this.files[0]);
        });

        function setImagePreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                $('#upload-placeholder').hide();
                $('#default-image-preview').attr('src', e.target.result).removeClass('d-none');
                $('#remove-image-wrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }

        function clearImagePreview() {
            $('#upload-placeholder').show();
            $('#default-image-preview').addClass('d-none').attr('src', '');
            $('#remove-image-wrap').addClass('d-none');
            fileInput.value = '';
        }

        $('#btn-remove-image').on('click', e => { e.stopPropagation(); clearImagePreview(); });

        // ── Form: open / populate / reset ─────────────────────────────────────
        function openFormModal(mode) {
            resetForm();
            const isEdit = mode === 'edit';
            $('#assetTypeModalLabel').html(
                `<i class="ri-${isEdit ? 'edit-box' : 'archive'}-line me-2 text-primary"></i>${isEdit ? 'Edit' : 'Add'} Asset Type`
            );
            $('#btn-save .btn-text').html(`<i class="ri-save-line me-1"></i>${isEdit ? 'Update' : 'Save'}`);
        }

        // ── has_default_dimension toggle ──────────────────────────────────────
        function toggleDimensionFields(show) {
            $('#dimension-fields').toggle(show);
            $('#dimension-hint').toggle(!show);
        }

        $('#has_default_dimension').on('change', function () {
            toggleDimensionFields(this.checked);
        });

        // ── has_asset_self toggle (shows total shelf) ─────────────────────────
        function toggleTotalShelf(show) {
            $('#total-self-wrap').toggleClass('d-none', !show);
        }

        $('#has_asset_self').on('change', function () {
            toggleTotalShelf(this.checked);
        });

        function populateForm(data) {
            ['name', 'height', 'width', 'total_self']
                .forEach(f => $('#' + f).val(data[f] ?? ''));

            // DB column is `dimention_unit_name` (typo), form field is `dimension_unit_name`
            $('#dimension_unit_name').val(data.dimention_unit_name || '');

            $('#status').prop('checked',                data.status == 1);
            $('#is_digital').prop('checked',            data.is_digital == 1);
            $('#has_kv_space').prop('checked',          data.has_kv_space == 1);
            $('#has_default_dimension').prop('checked', data.has_default_dimension == 1);
            $('#need_asset_image').prop('checked',      data.need_asset_image == 1);
            $('#need_asset_planogram').prop('checked',  data.need_asset_planogram == 1);
            $('#has_asset_self').prop('checked',        data.has_asset_self == 1);

            toggleDimensionFields(data.has_default_dimension == 1);
            toggleTotalShelf(data.has_asset_self == 1);

            if (data.default_image) {
                $('#upload-placeholder').hide();
                $('#default-image-preview').attr('src', base_url + data.default_image).removeClass('d-none');
                $('#remove-image-wrap').removeClass('d-none');
            }
        }

        function resetForm() {
            $('#assetTypeForm')[0].reset();
            $('#asset_type_id').val('');
            clearImagePreview();
            $('#dimension_unit_name').val('');
            $('#status').prop('checked', true);
            $('#is_digital').prop('checked', false);
            $('#has_kv_space').prop('checked', true);
            $('#has_default_dimension').prop('checked', false);
            $('#need_asset_image').prop('checked', false);
            $('#need_asset_planogram').prop('checked', false);
            $('#has_asset_self').prop('checked', false);
            toggleDimensionFields(false);
            toggleTotalShelf(false);
            clearErrors();
        }

        // ── Add ───────────────────────────────────────────────────────────────
        $('#btn-add-asset-type').on('click', () => {
            openFormModal('add');
            assetTypeModal.show();
        });

        // ── Edit ──────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-edit', function () {
            openFormModal('edit');
            $.get(apiUrl($(this).data('id')) + '/edit', data => {
                $('#asset_type_id').val(data.id);
                populateForm(data);
                assetTypeModal.show();
            });
        });

        // ── View ──────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-view', function () {
            $.get(apiUrl($(this).data('id')), data => {
                const depth  = data.depth > 0 ? ` × ${data.depth}` : '';
                const unit   = data.dimention_unit_name ? ` ${data.dimention_unit_name}` : '';
                const hasDim = data.has_default_dimension == 1 && (data.height || data.width);

                $('#view-name').text(data.name);
                $('#view-price').text(data.default_price ? `৳ ${Number(data.default_price).toFixed(2)}` : '—');
                $('#view-dimension').text(hasDim ? `${data.height ?? 0} × ${data.width ?? 0}${depth}${unit}` : '—');
                $('#view-total-self').text(`${data.total_self ?? 0} unit(s)`);
                $('#view-status').html(badge(data.status == 1,                 'success',   'Active',    'danger',  'Inactive'));
                $('#view-digital').html(badge(data.is_digital == 1,            'info',      'Yes',       null,      'No'));
                $('#view-kv').html(badge(data.has_kv_space == 1,               'warning',   'Yes',       null,      'No'));
                $('#view-has-default-dimension').html(badge(data.has_default_dimension == 1, 'success', 'Yes', null, 'No'));
                $('#view-need-asset-image').html(badge(data.need_asset_image == 1,           'primary',  'Yes', null, 'No'));
                $('#view-need-planogram').html(badge(data.need_asset_planogram == 1,         'secondary','Yes', null, 'No'));
                $('#view-has-asset-self').html(badge(data.has_asset_self == 1,               'teal',     'Yes', null, 'No'));

                $('#view-image')
                    .toggleClass('d-none', !data.default_image)
                    .attr('src', data.default_image ? base_url + data.default_image : '');
                $('#view-image-placeholder').toggle(!data.default_image);

                viewModalEl.show();
            });
        });

        // ── Delete ────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-delete', function () {
            $('#delete-asset-type-id').val($(this).data('id'));
            $('#delete-asset-type-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const $btn = $(this);
            setBtnLoading($btn, true, 'Deleting...');

            $.ajax({
                url:  apiUrl($('#delete-asset-type-id').val()),
                type: 'DELETE',
                success: res => {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error:    () => showToast('Failed to delete asset type.', 'danger'),
                complete: () => { setBtnLoading($btn, false); $btn.find('.btn-text').text('Delete'); }
            });
        });

        // ── Form submit (create / update) ─────────────────────────────────────
        $('#assetTypeForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id       = $('#asset_type_id').val();
            const formData = new FormData(this);

            ['status', 'is_digital', 'has_kv_space', 'has_default_dimension', 'need_asset_image', 'need_asset_planogram', 'has_asset_self']
                .forEach(f => formData.set(f, $(`#${f}`).is(':checked') ? 1 : 0));

            if (id) formData.append('_method', 'PUT');

            const $btn = $('#btn-save').prop('disabled', true);
            $('#btn-spinner').removeClass('d-none');

            $.ajax({
                url: apiUrl(id || null),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: res => {
                    assetTypeModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: xhr => {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, (field, messages) => {
                            $(`#${field}`).addClass('is-invalid');
                            $(`#error-${field}`).text(messages[0]);
                        });
                    } else {
                        showToast('Something went wrong. Please try again.', 'danger');
                    }
                },
                complete: () => {
                    $btn.prop('disabled', false);
                    $('#btn-spinner').addClass('d-none');
                }
            });
        });

    });
    </script>
@endpush
