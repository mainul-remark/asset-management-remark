@extends('backend.master')

@section('title', 'Assets')

@section('body')
    <div class="container-fluid mt-4">

        {{-- ── Filter Bar ─────────────────────────────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-sm-12 col-md-11 col-lg-10 col-xl-9 mx-auto">
                <div class="card custom-card">
                    <div class="card-body py-2">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <div class="">
                                    <label class="col-form-label fw-medium fs-13">Filter by Store</label>
                                </div>
                                <div class="">
                                    <select id="shortByStore" class="form-select form-select-sm select-ele">
                                        <option value="">All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->title }} ({{ $store->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label class="col-form-label fw-medium fs-13">Filter by Asset Category</label>
                                </div>
                                <div class="">
                                    <select id="shortByAssetType" class="form-select form-select-sm select-ele">
                                        <option value="">All Categories</option>
                                        @foreach($assetTypes as $assetType)
                                            <option value="{{ $assetType->id }}">{{ $assetType->name }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Table Card ─────────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-sm-12 col-md-11 col-lg-10 col-xl-9 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="card-title">Asset Management</div>
                        <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                            <a href="{{ route('asset-types.index') }}" class="btn btn-sm btn-secondary btn-wave">
                                <i class="ri-pages-line me-1"></i> Asset Category
                            </a>
                            <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-asset">
                                <i class="ri-add-line me-1"></i> Add Asset
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100" data-datatable-manual="true">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Store</th>
{{--                                        <th>Price</th>--}}
                                        <th>Status</th>
                                        <th width="110">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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
    <div class="modal fade" id="assetModal" >
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold" id="assetModalLabel">
                        <i class="ri-box-3-line me-2 text-primary"></i>Add Asset
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="assetForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    @csrf
                    <input type="hidden" id="asset_id" value="">

                    <div class="modal-body">

                        {{-- ── Section: Basic Information ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-information-line me-1"></i>Basic Information</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="asset_type_id" class="form-label">Asset Category <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-select" id="asset_type_id" name="asset_type_id[]" >
                                            <option value="">— Select Type —</option>
                                            @foreach($assetTypes as $assetType)
                                                <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-text open-asset-castegory-modal" style="cursor: pointer">+</span>
                                    </div>
                                    <div class="invalid-feedback" id="error-asset_type_id"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                                    <select class="form-select select-ele" id="store_id" required name="store_id">
                                        <option value="">— Select Store —</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->title }} ({{ $store->code }})</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="error-store_id"></div>
{{--                                    <div class="form-text" id="store-help-text">Disabled when asset is marked as Common.</div>--}}
                                </div>
                                <div class="col-md-12 d-none" id="asset-code-row">
                                    <div class="alert alert-primary-transparent d-flex align-items-center gap-2 py-2 mb-0">
                                        <i class="ri-qr-code-line fs-16"></i>
                                        <div class="fs-13">
                                            <span class="text-muted">Asset Code:</span>
                                            <strong id="asset_code_display" class="ms-1"></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Location & Pricing ── --}}
                        <div class="form-section mb-4">
{{--                            <p class="form-section-label"><i class="ri-store-2-line me-1"></i>Location & Pricing</p>--}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" readonly
                                           placeholder="e.g. Shelf Display Unit A1">
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>

{{--                                <div class="col-md-3">--}}
{{--                                    <label for="minimum_fee" class="form-label">Minimum Charge</label>--}}
{{--                                    <div class="input-group">--}}
{{--                                        <span class="input-group-text">৳</span>--}}
{{--                                        <input type="number" step="0.01" min="0" class="form-control"--}}
{{--                                            id="minimum_fee" name="minimum_fee" placeholder="0.00">--}}
{{--                                    </div>--}}
{{--                                    <div class="invalid-feedback d-block" id="error-minimum_fee"></div>--}}
{{--                                </div>--}}
                            </div>
                        </div>

                        {{-- ── Section: Media ── --}}
                        <div class="form-section mb-4 d-none" id="media-section">
                            <p class="form-section-label"><i class="ri-image-line me-1"></i>Media</p>
                            <div class="row g-3">
                                <div class="col-md-6 d-none" id="image-field-wrap">
                                    <label class="form-label">Default Image <span class="text-danger d-none" id="image-required-star">*</span></label>
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
                                            <i class="ri-delete-bin-line me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none" id="planogram-field-wrap">
                                    <label for="planogram_pdf" class="form-label">Planogram PDF <span class="text-danger d-none" id="planogram-required-star">*</span></label>
                                    <div class="pdf-upload-zone" id="pdfUploadZone">
                                        <div id="pdf-placeholder" class="upload-placeholder-inner">
                                            <i class="ri-file-pdf-2-line fs-2 text-muted"></i>
                                            <p class="mb-1 text-muted fs-12 mt-1">Click to select PDF</p>
                                            <p class="mb-0 fs-11" style="color:#adb5bd">PDF only &mdash; Max 10 MB</p>
                                        </div>
                                        <div id="pdf-selected" class="d-none text-center">
                                            <i class="ri-file-pdf-2-line fs-2 text-danger"></i>
                                            <p class="mb-0 fs-12 mt-1 text-truncate px-2" id="pdf-filename"></p>
                                        </div>
                                        <input type="file" class="d-none" id="planogram_pdf" name="planogram_pdf" accept=".pdf">
                                    </div>
                                    <div class="invalid-feedback d-block" id="error-planogram_pdf"></div>
                                    <div id="existing-pdf-wrap" class="d-none mt-1">
                                        <a href="#" target="_blank" id="existing-planogram-link" class="btn btn-sm btn-info-light">
                                            <i class="ri-external-link-line me-1"></i>View current PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Configuration ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-settings-3-line me-1"></i>Configuration</p>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-success" type="checkbox"
                                                role="switch" id="status" checked>
                                            <label class="form-check-label fw-medium" for="status">Active</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Enable this asset in the system</small>
                                        <div class="invalid-feedback" id="error-status"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 d-none" id="has-kv-slot-wrap">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-warning" type="checkbox"
                                                role="switch" id="has_kv_slot">
                                            <label class="form-check-label fw-medium" for="has_kv_slot">Has KV Slot</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Supports key-visual display slot</small>
                                        <div class="invalid-feedback" id="error-has_kv_slot"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 d-none" id="has-self-wrap">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-info" type="checkbox"
                                                role="switch" id="has_self">
                                            <label class="form-check-label fw-medium" for="has_self">Has Shelf</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset contains shelf units</small>
                                        <div class="invalid-feedback" id="error-has_self"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-primary" type="checkbox"
                                                role="switch" id="is_common_asset">
                                            <label class="form-check-label fw-medium" for="is_common_asset">Common Asset</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Shared across all stores</small>
                                        <div class="invalid-feedback" id="error-is_common_asset"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Shelf Settings (conditional) ── --}}
                        <div class="form-section d-none" id="shelf-settings-section">
                            <p class="form-section-label"><i class="ri-stack-line me-1"></i>Shelf Settings</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="total_self" class="form-label">Total Shelf Units</label>
                                    <input type="number" class="form-control" id="total_self" name="total_self"
                                        placeholder="0" min="0" max="127">
                                    <div class="invalid-feedback" id="error-total_self"></div>
                                    <div class="form-text">Maximum 127 shelf units.</div>
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

    {{-- ── Asset Category modal ───────────────────────────────────────────────────────── --}}
    <div class="modal fade" id="addAssetCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">
                        <i class="ri-eye-line me-2 text-info"></i>Add Asset Category
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assetCategoryForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    @csrf
                    <input type="hidden" id="asset_category_id" value="">

                    <div class="modal-body">

                        {{-- ── Section: Basic Information ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-information-line me-1"></i>Basic Information</p>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="asset-category-name" class="form-label">
                                        Asset Category Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="asset-category-name" name="name"
                                           placeholder="e.g. Billboard, LED Screen, Banner">
                                    <div class="invalid-feedback" id="asset-category-error-name"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Section: Dimensions ── --}}
                        <div class="form-section mb-4">
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                <p class="form-section-label mb-0 border-0 pb-0"><i class="ri-ruler-line me-1"></i>Dimensions</p>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input form-checked-success" type="checkbox"
                                           role="switch" id="asset-category-has_default_dimension" name="has_default_dimension">
                                    <label class="form-check-label fw-medium fs-12" for="asset-category-has_default_dimension">Has Default Dimension</label>
                                </div>
                            </div>
                            <div id="asset-category-dimension-fields" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <label for="asset-category-height" class="form-label">Height</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                               id="asset-category-height" name="height" placeholder="0">
                                        <div class="invalid-feedback" id="asset-category-error-height"></div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label for="asset-category-width" class="form-label">Width</label>
                                        <input type="number" step="0.01" min="0" class="form-control"
                                               id="asset-category-width" name="width" placeholder="0">
                                        <div class="invalid-feedback" id="asset-category-error-width"></div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label for="asset-category-dimension_unit_name" class="form-label">Unit</label>
                                        <select class="form-select" id="asset-category-dimension_unit_name" name="dimension_unit_name">
                                            <option value="">— Select —</option>
                                            <option value="px">px</option>
                                            <option value="in">in (inch)</option>
                                            <option value="ft">ft (feet)</option>
                                            <option value="cm">cm</option>
                                            <option value="mm">mm</option>
                                            <option value="m">m (meter)</option>
                                            <option value="yd">yd (yard)</option>
                                        </select>
                                        <div class="invalid-feedback" id="asset-category-error-dimension_unit_name"></div>
                                    </div>
                                </div>
                            </div>
                            <p id="asset-category-dimension-hint" class="text-muted fs-12 mb-0">Enable <strong>Has Default Dimension</strong> to set height, width and depth.</p>
                        </div>

                        {{-- ── Section: Storage & Image ── --}}
                        <div class="form-section mb-4">
                            <p class="form-section-label"><i class="ri-image-line me-1"></i>Storage & Image</p>
                            <div class="row g-3 align-items-start">
                                <div class="col-12 d-none">
                                    <label class="form-label">Default Image</label>
                                    <div class="image-upload-zone" id="asset-category-imageUploadZone">
                                        <div id="asset-category-upload-placeholder" class="upload-placeholder-inner">
                                            <i class="ri-upload-cloud-2-line fs-2 text-muted"></i>
                                            <p class="mb-1 text-muted fs-12 mt-1">Click or drag image here</p>
                                            <p class="mb-0 fs-11" style="color:#adb5bd">JPG, JPEG, PNG, WEBP &mdash; Max 2 MB</p>
                                        </div>
                                        <img id="asset-category-default-image-preview" class="d-none" src="" alt="Preview">
                                        <input type="file" class="d-none" id="asset-category-default_image" name="default_image"
                                               accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <div class="invalid-feedback d-block" id="asset-category-error-default_image"></div>
                                    <div id="asset-category-remove-image-wrap" class="d-none mt-1">
                                        <button type="button" class="btn btn-sm btn-danger-light" id="asset-category-btn-remove-image">
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
                                                   role="switch" id="asset-category-status" checked>
                                            <label class="form-check-label fw-medium" for="asset-category-status">Active Status</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Enable this asset type in the system</small>
                                        <div class="invalid-feedback" id="asset-category-error-status"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-info" type="checkbox"
                                                   role="switch" id="asset-category-is_digital">
                                            <label class="form-check-label fw-medium" for="asset-category-is_digital">Digital Asset</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Mark as a non-physical digital type</small>
                                        <div class="invalid-feedback" id="asset-category-error-is_digital"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-warning" type="checkbox"
                                                   role="switch" id="asset-category-has_kv_space" checked>
                                            <label class="form-check-label fw-medium" for="asset-category-has_kv_space">Has KV Space</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Supports key-value metadata storage</small>
                                        <div class="invalid-feedback" id="asset-category-error-has_kv_space"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-primary" type="checkbox"
                                                   role="switch" id="asset-category-need_asset_image">
                                            <label class="form-check-label fw-medium" for="asset-category-need_asset_image">Need Asset Image</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset requires an image upload</small>
                                        <div class="invalid-feedback" id="asset-category-error-need_asset_image"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-secondary" type="checkbox"
                                                   role="switch" id="asset-category-need_asset_planogram">
                                            <label class="form-check-label fw-medium" for="asset-category-need_asset_planogram">Need Planogram</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset requires a planogram layout</small>
                                        <div class="invalid-feedback" id="asset-category-error-need_asset_planogram"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="switch-option-card">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input form-checked-teal" type="checkbox"
                                                   role="switch" id="asset-category-has_asset_self">
                                            <label class="form-check-label fw-medium" for="asset-category-has_asset_self">Has Asset Self</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Asset contains self-managed sections</small>
                                        <div class="invalid-feedback" id="asset-category-error-has_asset_self"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-none" id="asset-category-total-self-wrap">
                                    <label for="asset-category-total_self" class="form-label">Total Shelf</label>
                                    <input type="number" min="0" class="form-control"
                                           id="asset-category-total_self" name="total_self" placeholder="0">
                                    <div class="invalid-feedback" id="asset-category-error-total_self"></div>
                                    <div class="form-text">Number of shelf units</div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save-asset-category">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Save</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner-asset-category"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── View Modal ───────────────────────────────────────────────────────── --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">
                        <i class="ri-eye-line me-2 text-info"></i>Asset Details
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
                                <tr><th width="38%" class="text-muted fw-medium fs-12">Name</th><td id="view-name" class="fw-semibold"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Asset Code</th><td id="view-code"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Asset Type</th><td id="view-asset-type"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Store</th><td id="view-store"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Asset Price</th><td id="view-price"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Minimum Charge</th><td id="view-minimum-fee"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Status</th><td id="view-status"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Common Asset</th><td id="view-common"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Has KV Slot</th><td id="view-kv"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Has Shelf</th><td id="view-self"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Total Shelf</th><td id="view-total-self"></td></tr>
                                <tr><th class="text-muted fw-medium fs-12">Planogram</th><td id="view-planogram"></td></tr>
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
                    <h6 class="fw-semibold mb-1">Delete Asset?</h6>
                    <p class="text-muted fs-13 mb-0">
                        You are about to delete<br>
                        <strong id="delete-asset-name" class="text-dark"></strong>
                    </p>
                    <p class="text-danger fs-11 mt-1 mb-0">This action cannot be undone.</p>
                    <input type="hidden" id="delete-asset-id">
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
    .image-upload-zone,
    .pdf-upload-zone {
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
    .image-upload-zone.drag-over,
    .pdf-upload-zone:hover { border-color: var(--primary-color); background: rgba(var(--primary-rgb), 0.04); }
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
    .switch-option-card.is-disabled { opacity: 0.55; pointer-events: none; }

    /* ── View Image Box ── */
    .view-image-box { border-radius: 8px; overflow: hidden; }
    .view-image-box img { width: 100%; border-radius: 8px; object-fit: cover; }
    #view-image-placeholder {
        width: 100%; aspect-ratio: 1 / 1;
        background: var(--light); border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 3rem; color: var(--text-muted);
        border: 1px dashed var(--default-border);
    }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {

        // ── Bootstrap modals & AJAX setup ─────────────────────────────────────
        const assetModal    = new bootstrap.Modal(document.getElementById('assetModal'));
        const viewModalEl   = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const apiUrl = id => base_url + 'assets' + (id ? '/' + id : '');
        const assetCategoryModal = new bootstrap.Modal(document.getElementById('addAssetCategoryModal'));
        const assetCategoryApiUrl = id => base_url + 'asset-types' + (id ? '/' + id : '');

        // ── Server-side DataTable ──────────────────────────────────────────────
        const dataTable = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 500,
            order: [],
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center gap-2"f>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            language: {
                searchPlaceholder: "Search data...",
                sSearch: "",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ data",
                infoEmpty: "No data found",
                zeroRecords: "No matching records found",
                paginate: { previous: "<i class='ri-arrow-left-s-line'></i>", next: "<i class='ri-arrow-right-s-line'></i>" }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: @json(route('assets.index')),
                data: function (d) {
                    d.store_id = $('#shortByStore').val();
                    d.asset_type_id = $('#shortByAssetType').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'image', name: 'assets.default_image', searchable: false, orderable: false },
                { data: 'name_display', name: 'assets.name' },
                { data: 'code_display', name: 'assets.asset_code' },
                { data: 'asset_type_display', name: 'asset_types.name' },
                { data: 'store_display', name: 'stores.title' },
                { data: 'status_badge', name: 'assets.status', searchable: false, orderable: false },
                { data: 'actions', name: 'actions', searchable: false, orderable: false },
            ],
        });

        $('#shortByStore, #shortByAssetType').on('change', function () {
            dataTable.ajax.reload();
        });

        // ── Utility helpers ───────────────────────────────────────────────────
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
            $(`<div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`).appendTo('body').delay(3000).queue(function () { $(this).remove(); });
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }

        function clearAssetCategoryErrors() {
            $('#addAssetCategoryModal .is-invalid').removeClass('is-invalid');
            $('#addAssetCategoryModal .invalid-feedback').text('');
        }

        // ── Image Upload Zone ──────────────────────────────────────────────────
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

        // ── PDF Upload Zone ────────────────────────────────────────────────────
        const pdfZone  = document.getElementById('pdfUploadZone');
        const pdfInput = document.getElementById('planogram_pdf');

        pdfZone.addEventListener('click', () => pdfInput.click());
        $('#planogram_pdf').on('change', function () {
            if (this.files[0]) {
                $('#pdf-placeholder').hide();
                $('#pdf-filename').text(this.files[0].name);
                $('#pdf-selected').removeClass('d-none');
            }
        });

        function clearPdfPreview() {
            $('#pdf-placeholder').show();
            $('#pdf-selected').addClass('d-none').find('#pdf-filename').text('');
            pdfInput.value = '';
        }

        // ── Asset type flags map ───────────────────────────────────────────────
        @php
            $assetTypeFlagsData = $assetTypes->keyBy('id')->map(fn($t) => [
                'need_asset_image'     => (int) $t->need_asset_image,
                'need_asset_planogram' => (int) $t->need_asset_planogram,
                'has_asset_self'       => (int) $t->has_asset_self,
                'is_digital'           => (int) $t->is_digital,
                'total_self'           => (int) $t->total_self,
                'has_kv_space'         => (int) $t->has_kv_space,
            ]);
        @endphp
        const assetTypeFlags = {!! json_encode($assetTypeFlagsData) !!};

        function upsertAssetTypeOption($select, value, label) {
            const optionValue = String(value ?? '');
            if (!optionValue) return;

            const $existingOption = $select.find('option').filter(function () {
                return String(this.value) === optionValue;
            });

            if ($existingOption.length) {
                $existingOption.text(label);
                return;
            }

            $select.append(new Option(label, optionValue));
        }

        function applyMediaFields(typeId) {
            const flags        = typeId ? (assetTypeFlags[typeId] || {}) : {};
            const needImage    = flags.need_asset_image    === 1;
            const needPlanogram = flags.need_asset_planogram === 1;

            $('#image-field-wrap').toggleClass('d-none', !needImage);
            $('#image-required-star').toggleClass('d-none', !needImage);
            if (!needImage) { clearImagePreview(); $('#error-default_image').text(''); }

            $('#planogram-field-wrap').toggleClass('d-none', !needPlanogram);
            $('#planogram-required-star').toggleClass('d-none', !needPlanogram);
            if (!needPlanogram) { clearPdfPreview(); $('#existing-pdf-wrap').addClass('d-none'); $('#error-planogram_pdf').text(''); }

            $('#media-section').toggleClass('d-none', !needImage && !needPlanogram);

            // Auto-check checkboxes based on category flags (only on type change, not edit populate)
            const hasSelf = typeId && flags.has_asset_self === 1;
            $('#has-self-wrap').toggleClass('d-none', !hasSelf);
            $('#has_self').prop('checked', hasSelf).trigger('change');
            if (hasSelf) $('#total_self').val(flags.total_self || '');

            const hasKvSpace = typeId && flags.has_kv_space === 1;
            $('#has-kv-slot-wrap').toggleClass('d-none', !hasKvSpace);
            $('#has_kv_slot').prop('checked', hasKvSpace);
        }

        // ── Auto-generate asset name ───────────────────────────────────────────
        let nameGenerationEnabled = false;

        function tryGenerateName() {
            if (!nameGenerationEnabled) return;
            const typeId  = $('#asset_type_id').val();
            const storeId = $('#store_id').val();
            if (!typeId || !storeId) { $('#name').val(''); return; }

            $('#name').val('Generating…').addClass('text-muted');
            $.get(base_url + 'assets/next-name', { asset_type_id: typeId, store_id: storeId })
                .done(res => $('#name').val(res.name).removeClass('text-muted'))
                .fail(() => $('#name').val('').removeClass('text-muted'));
        }

        $('#asset_type_id').on('change', function () {
            applyMediaFields($(this).val());
            tryGenerateName();
        });
        $('#store_id').on('change', tryGenerateName);

        // ── Is Common Asset toggle (disables store select) ─────────────────────
        $('#is_common_asset').on('change', function () {
            // toggleCommonAsset();
        });

        function toggleCommonAsset() {
            const isCommon = $('#is_common_asset').is(':checked');
            if (isCommon) {
                $('#store_id').val('').trigger('change');
                $('#store_id').prop('disabled', true);
                $('#store_id').closest('.col-md-6').find('.switch-option-card, .select2-container').addClass('is-disabled');
            } else {
                $('#store_id').prop('disabled', false);
                $('#store_id').closest('.col-md-6').find('.switch-option-card, .select2-container').removeClass('is-disabled');
            }
            $('#store_id').next('.select2-container').find('.select2-selection').css('opacity', isCommon ? '0.65' : '');
        }

        // ── Has Shelf toggle ───────────────────────────────────────────────────
        $('#has_self').on('change', function () {
            toggleShelfSection();
        });

        function toggleShelfSection() {
            const hasSelf = $('#has_self').is(':checked');
            $('#shelf-settings-section').toggleClass('d-none', !hasSelf);
            if (!hasSelf) {
                $('#total_self').val('').removeClass('is-invalid');
                $('#error-total_self').text('');
            }
        }

        function toggleAssetCategoryDimensionFields(show) {
            $('#asset-category-dimension-fields').toggle(show);
            $('#asset-category-dimension-hint').toggle(!show);
        }

        function toggleAssetCategoryTotalShelf(show) {
            $('#asset-category-total-self-wrap').toggleClass('d-none', !show);
            if (!show) {
                $('#asset-category-total_self').val('');
            }
        }

        function clearAssetCategoryImagePreview() {
            $('#asset-category-upload-placeholder').show();
            $('#asset-category-default-image-preview').addClass('d-none').attr('src', '');
            $('#asset-category-remove-image-wrap').addClass('d-none');
            const input = document.getElementById('asset-category-default_image');
            if (input) input.value = '';
        }

        function setAssetCategoryImagePreview(file) {
            const reader = new FileReader();
            reader.onload = e => {
                $('#asset-category-upload-placeholder').hide();
                $('#asset-category-default-image-preview').attr('src', e.target.result).removeClass('d-none');
                $('#asset-category-remove-image-wrap').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }

        function resetAssetCategoryForm() {
            const form = document.getElementById('assetCategoryForm');
            if (!form) return;

            form.reset();
            $('#asset_category_id').val('');
            $('#asset-category-status').prop('checked', true);
            $('#asset-category-is_digital').prop('checked', false);
            $('#asset-category-has_kv_space').prop('checked', true);
            $('#asset-category-has_default_dimension').prop('checked', false);
            $('#asset-category-need_asset_image').prop('checked', false);
            $('#asset-category-need_asset_planogram').prop('checked', false);
            $('#asset-category-has_asset_self').prop('checked', false);
            $('#asset-category-dimension_unit_name').val('');
            toggleAssetCategoryDimensionFields(false);
            toggleAssetCategoryTotalShelf(false);
            clearAssetCategoryImagePreview();
            clearAssetCategoryErrors();
        }

        $('#asset-category-has_default_dimension').on('change', function () {
            toggleAssetCategoryDimensionFields(this.checked);
        });

        $('#asset-category-has_asset_self').on('change', function () {
            toggleAssetCategoryTotalShelf(this.checked);
        });

        $('#asset-category-imageUploadZone').on('click', function (e) {
            if ($(e.target).closest('#asset-category-btn-remove-image').length === 0) {
                $('#asset-category-default_image').trigger('click');
            }
        });

        $('#asset-category-default_image').on('change', function () {
            if (this.files[0]) {
                setAssetCategoryImagePreview(this.files[0]);
            }
        });

        $('#asset-category-btn-remove-image').on('click', function (e) {
            e.stopPropagation();
            clearAssetCategoryImagePreview();
        });

        $('#assetCategoryForm').on('submit', function (e) {
            e.preventDefault();
            clearAssetCategoryErrors();

            if ($('#asset-category-has_default_dimension').is(':checked')) {
                let hasError = false;

                if (!String($('#asset-category-height').val() || '').trim()) {
                    $('#asset-category-height').addClass('is-invalid');
                    $('#asset-category-error-height').text('Height is required when Default Dimension is enabled.');
                    hasError = true;
                }

                if (!String($('#asset-category-width').val() || '').trim()) {
                    $('#asset-category-width').addClass('is-invalid');
                    $('#asset-category-error-width').text('Width is required when Default Dimension is enabled.');
                    hasError = true;
                }

                if (!$('#asset-category-dimension_unit_name').val()) {
                    $('#asset-category-dimension_unit_name').addClass('is-invalid');
                    $('#asset-category-error-dimension_unit_name').text('Unit is required when Default Dimension is enabled.');
                    hasError = true;
                }

                if (hasError) return;
            }

            const id = $('#asset_category_id').val();
            const formData = new FormData(this);

            [
                ['status', 'asset-category-status'],
                ['is_digital', 'asset-category-is_digital'],
                ['has_kv_space', 'asset-category-has_kv_space'],
                ['has_default_dimension', 'asset-category-has_default_dimension'],
                ['need_asset_image', 'asset-category-need_asset_image'],
                ['need_asset_planogram', 'asset-category-need_asset_planogram'],
                ['has_asset_self', 'asset-category-has_asset_self'],
            ].forEach(([field, idName]) => {
                formData.set(field, $(`#${idName}`).is(':checked') ? 1 : 0);
            });

            if (!$('#asset-category-has_asset_self').is(':checked')) {
                formData.delete('total_self');
            }

            if (id) {
                formData.append('_method', 'PUT');
            }

            const $btn = $('#btn-save-asset-category');
            setBtnLoading($btn, true);
            $('#btn-spinner-asset-category').removeClass('d-none');

            $.ajax({
                url: assetCategoryApiUrl(id || null),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: res => {
                    const assetType = res.data || {};

                    if (assetType.id && assetType.name) {
                        upsertAssetTypeOption($('#asset_type_id'), assetType.id, assetType.name);
                        upsertAssetTypeOption($('#shortByAssetType'), assetType.id, assetType.name);

                        assetTypeFlags[String(assetType.id)] = {
                            need_asset_image: Number(assetType.need_asset_image ?? 0),
                            need_asset_planogram: Number(assetType.need_asset_planogram ?? 0),
                            has_asset_self: Number(assetType.has_asset_self ?? 0),
                            is_digital: Number(assetType.is_digital ?? 0),
                            total_self: Number(assetType.total_self ?? 0),
                            has_kv_space: Number(assetType.has_kv_space ?? 0),
                        };

                        $('#asset_type_id').val(String(assetType.id)).trigger('change');
                    }

                    assetCategoryModal.hide();
                    resetAssetCategoryForm();
                    showToast(res.message, 'success');
                },
                error: xhr => {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors || {}, (field, messages) => {
                            $(`#asset-category-${field}`).addClass('is-invalid');
                            $(`#asset-category-error-${field}`).text(messages[0]);
                        });
                    } else {
                        showToast('Failed to save asset type.', 'danger');
                    }
                },
                complete: () => {
                    setBtnLoading($btn, false);
                    $('#btn-spinner-asset-category').addClass('d-none');
                    $btn.find('.btn-text').html('<i class="ri-save-line me-1"></i>Save');
                }
            });
        });

        $(document).on('click', '.open-asset-castegory-modal', function () {
            resetAssetCategoryForm();
            assetCategoryModal.show();
        });

        $('#addAssetCategoryModal').on('hidden.bs.modal', function () {
            resetAssetCategoryForm();
        });

        // ── Form: open / reset ─────────────────────────────────────────────────
        function openFormModal(mode) {
            nameGenerationEnabled = false;
            resetForm();
            const isEdit = mode === 'edit';
            $('#assetModalLabel').html(
                `<i class="ri-${isEdit ? 'edit-box' : 'box-3'}-line me-2 text-primary"></i>${isEdit ? 'Edit' : 'Add'} Asset`
            );
            $('#btn-save .btn-text').html(`<i class="ri-save-line me-1"></i>${isEdit ? 'Update' : 'Save'}`);
            if (mode === 'add') nameGenerationEnabled = true;
        }

        function resetForm() {
            $('#assetForm')[0].reset();
            $('#asset_id').val('');
            $('#asset-code-row').addClass('d-none');
            $('#asset_code_display').text('');
            clearImagePreview();
            clearPdfPreview();
            $('#existing-pdf-wrap').addClass('d-none');
            $('#existing-planogram-link').attr('href', '#');
            $('#status').prop('checked', true);
            $('#has_kv_slot').prop('checked', false);
            $('#has_self').prop('checked', false);
            $('#is_common_asset').prop('checked', false);
            $('#store_id').val('').prop('disabled', false).trigger('change');
            applyMediaFields(null);
            toggleShelfSection();
            clearErrors();
        }

        // ── Add ───────────────────────────────────────────────────────────────
        $('#btn-add-asset').on('click', () => {
            openFormModal('add');
            assetModal.show();
        });

        // ── Edit ──────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-edit', function () {
            openFormModal('edit');
            $.get(apiUrl($(this).data('id')) + '/edit', function (data) {
                $('#asset_id').val(data.id);
                $('#asset_type_id').val(data.asset_type_id);
                applyMediaFields(data.asset_type_id);
                $('#name').val(data.name);
                $('#asset-code-row').removeClass('d-none');
                $('#asset_code_display').text(data.asset_code || '—');
                $('#asset_price').val(data.asset_price);
                $('#minimum_fee').val(data.minimum_fee);
                $('#total_self').val(data.total_self);

                $('#status').prop('checked',         parseInt(data.status, 10) === 1);
                $('#has_kv_slot').prop('checked',    parseInt(data.has_kv_slot, 10) === 1);
                $('#has_self').prop('checked',       parseInt(data.has_self, 10) === 1);
                $('#is_common_asset').prop('checked',parseInt(data.is_common_asset, 10) === 1);

                // toggleCommonAsset();
                if (!$('#is_common_asset').is(':checked') && data.store_id) {
                    $('#store_id').val(data.store_id).trigger('change');
                }
                toggleShelfSection();

                if (data.default_image) {
                    $('#upload-placeholder').hide();
                    $('#default-image-preview').attr('src', base_url + data.default_image).removeClass('d-none');
                    $('#remove-image-wrap').removeClass('d-none');
                }

                if (data.planogram_pdf) {
                    $('#existing-pdf-wrap').removeClass('d-none');
                    $('#existing-planogram-link').attr('href', base_url + data.planogram_pdf);
                }

                nameGenerationEnabled = true; // enable after initial populate
                assetModal.show();
            });
        });

        // ── View ──────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-view', function () {
            $.get(apiUrl($(this).data('id')), function (data) {
                $('#view-name').text(data.name || '—');
                $('#view-code').html(`<code>${data.asset_code || '—'}</code>`);
                $('#view-asset-type').text(data.asset_type ? data.asset_type.name : '—');
                $('#view-store').html(
                    parseInt(data.is_common_asset, 10) === 1
                        ? '<span class="badge bg-primary-transparent">Common Asset</span>'
                        : (data.store ? data.store.title : '—')
                );
                $('#view-price').text(data.asset_price ? '৳ ' + Number(data.asset_price).toFixed(2) : '—');
                $('#view-minimum-fee').text(data.minimum_fee ? '৳ ' + Number(data.minimum_fee).toFixed(2) : '—');
                $('#view-status').html(badge(data.status == 1,          'success', 'Active',  'danger',  'Inactive'));
                $('#view-common').html(badge(data.is_common_asset == 1, 'primary', 'Yes',     null,      'No'));
                $('#view-kv').html(badge(data.has_kv_slot == 1,         'warning', 'Yes',     null,      'No'));
                $('#view-self').html(badge(data.has_self == 1,          'info',    'Yes',     null,      'No'));
                $('#view-total-self').text(parseInt(data.has_self, 10) === 1 ? (data.total_self ?? 0) + ' units' : '—');
                $('#view-planogram').html(data.planogram_pdf
                    ? `<a href="${base_url + data.planogram_pdf}" target="_blank" class="btn btn-sm btn-info-light btn-wave"><i class="ri-file-pdf-2-line me-1"></i>View PDF</a>`
                    : '—');

                $('#view-image').toggleClass('d-none', !data.default_image).attr('src', data.default_image ? base_url + data.default_image : '');
                $('#view-image-placeholder').toggle(!data.default_image);

                viewModalEl.show();
            });
        });

        // ── Delete ────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-delete', function () {
            $('#delete-asset-id').val($(this).data('id'));
            $('#delete-asset-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const $btn = $(this);
            setBtnLoading($btn, true, 'Deleting...');
            $.ajax({
                url:  apiUrl($('#delete-asset-id').val()),
                type: 'DELETE',
                success: res => {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    dataTable.ajax.reload(null, false);
                },
                error:    () => showToast('Failed to delete asset.', 'danger'),
                complete: () => { setBtnLoading($btn, false); $btn.find('.btn-text').text('Delete'); }
            });
        });

        // ── Form submit (create / update) ──────────────────────────────────────
        $('#assetForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id       = $('#asset_id').val();
            const formData = new FormData(this);

            ['status', 'has_kv_slot', 'has_self', 'is_common_asset']
                .forEach(f => formData.set(f, $(`#${f}`).is(':checked') ? 1 : 0));

            if ($('#is_common_asset').is(':checked')) formData.set('store_id', '');
            if (!$('#has_self').is(':checked'))        formData.delete('total_self');
            if (id) formData.append('_method', 'PUT');

            const $btn = $('#btn-save');
            setBtnLoading($btn, true);
            $('#btn-spinner').removeClass('d-none');

            $.ajax({
                url:  apiUrl(id || null),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: res => {
                    assetModal.hide();
                    showToast(res.message, 'success');
                    dataTable.ajax.reload(null, false);
                },
                error: xhr => {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors || {}, (field, messages) => {
                            $(`#${field}`).addClass('is-invalid');
                            $(`#error-${field}`).text(messages[0]);
                        });
                    } else {
                        showToast('Something went wrong. Please try again.', 'danger');
                    }
                },
                complete: () => {
                    setBtnLoading($btn, false);
                    $('#btn-spinner').addClass('d-none');
                    $btn.find('.btn-text').html(`<i class="ri-save-line me-1"></i>${id ? 'Update' : 'Save'}`);
                }
            });
        });

    });
    </script>
@endpush
