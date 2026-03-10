@extends('backend.master')

@section('title', 'Key Visuals')

@section('body')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-xl-11 col-lg-12 mx-auto">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Key Visual Management</h6>
                        <p class="text-muted fs-12 mb-0 mt-1">Manage all key visual assets across brands and categories.</p>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-key-visual">
                        <i class="ri-add-line me-1"></i> Add Key Visual
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th width="60">Thumb</th>
                                    <th>Name</th>
                                    <th>Unique Code</th>
                                    <th>Asset Type</th>
                                    <th>KV Type</th>
                                    <th>Min Resolution</th>
                                    <th>Status</th>
                                    <th width="110">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($keyVisuals as $keyVisual)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($keyVisual->kv_thumb)
                                            <img class="kv-thumb" src="{{ asset($keyVisual->kv_thumb) }}" alt="{{ $keyVisual->name }}">
                                        @else
                                            <div class="kv-thumb-empty"><i class="ri-image-line"></i></div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $keyVisual->name }}</td>
                                    <td><span class="badge bg-primary-transparent font-monospace kv-code-badge">{{ $keyVisual->unique_code }}</span></td>
                                    <td>{{ $keyVisual->assetType?->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $keyVisual->kv_type === 'video' ? 'bg-info-transparent' : 'bg-warning-transparent' }} text-uppercase">
                                            <i class="ri-{{ $keyVisual->kv_type === 'video' ? 'video' : 'image-2' }}-line me-1"></i>{{ $keyVisual->kv_type ?? 'image' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($keyVisual->minimum_res_width || $keyVisual->minimum_res_height)
                                            <span class="text-muted fs-11 font-monospace">{{ $keyVisual->minimum_res_width ?? 0 }} x {{ $keyVisual->minimum_res_height ?? 0 }} px</span>
                                        @else
                                            <span class="text-muted fs-12">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $keyVisual->status == 1 ? 'bg-success-transparent' : 'bg-danger-transparent' }}">
                                            {{ $keyVisual->status == 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-list">
                                            <button class="btn btn-icon btn-sm btn-info-light btn-wave size-btn-view"
                                                data-id="{{ $keyVisual->id }}" title="Size View">
                                                <i class="ri-font-size"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view"
                                                data-id="{{ $keyVisual->id }}" title="View">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit"
                                                data-id="{{ $keyVisual->id }}" title="Edit">
                                                <i class="ri-edit-box-line"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete"
                                                data-id="{{ $keyVisual->id }}" data-name="{{ $keyVisual->name }}" title="Delete">
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

{{-- ═══════════════════════════════════════════════════════
     MAIN KEY VISUAL FORM MODAL
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="keyVisualModal" tabindex="-1" aria-labelledby="keyVisualModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content kv-modal-content">
            <div class="modal-header kv-modal-header">
                <h6 class="modal-title fw-semibold" id="keyVisualModalLabel">
                    <i class="ri-image-add-line me-2 text-primary"></i>Add Key Visual
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="keyVisualForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                @csrf
                <input type="hidden" id="key_visual_id" name="_kv_id" value="">

                <div class="modal-body px-4 py-3">

                    {{-- ── SECTION: BASIC INFORMATION ────────────────────── --}}
                    <div class="kv-section-title">
                        <i class="ri-information-line"></i><span>Basic Information</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="e.g. Ramadan Hero Banner 2026">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="asset_type_id" class="form-label fw-medium">Asset Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="asset_type_id" name="asset_type_id">
                                <option value="">-- Select --</option>
                                @foreach($assetTypes as $assetType)
                                    <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-asset_type_id"></div>
                        </div>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col-md-5">
                            <label for="kv_type" class="form-label fw-medium">KV Type <span class="text-danger">*</span></label>
                            <div class="kv-type-selector">
                                <label class="kv-type-option" id="kv-type-image-opt">
                                    <input type="radio" name="kv_type" id="kv_type_image" value="image" checked>
                                    <span><i class="ri-image-2-line"></i> Image</span>
                                </label>
                                <label class="kv-type-option" id="kv-type-video-opt">
                                    <input type="radio" name="kv_type" id="kv_type_video" value="video">
                                    <span><i class="ri-video-line"></i> Video</span>
                                </label>
                            </div>
                            <div class="invalid-feedback" id="error-kv_type"></div>
                        </div>
                        <div class="col-md-7 d-flex align-items-end">
                            <div class="kv-status-wrapper w-100">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="status" name="status" value="1" checked>
                                    <label class="form-check-label fw-medium" for="status" id="status-label">Active</label>
                                </div>
                                <small class="text-muted ms-auto" id="status-sub-label">This KV will be visible</small>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION: CLASSIFICATION ───────────────────────── --}}
                    <div class="kv-section-title mt-3">
                        <i class="ri-price-tag-3-line"></i><span>Classification</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="brand_ids" class="form-label fw-medium mb-0">Brand <span class="text-danger">*</span></label>
                                <a href="#" class="fs-12 text-primary kv-open-brand-modal">
                                    <i class="ri-add-line"></i> Add new
                                </a>
                            </div>
                            <select class="form-select kv-select2-brand" id="brand_ids" name="brand_ids[]"
                                data-selected-brand-code="">
                                <option value=""></option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" data-brand-code="{{ $brand->code }}">
                                        {{ $brand->name }} ({{ $brand->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-brand_ids"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="category_ids" class="form-label fw-medium mb-0">Category <span class="text-danger">*</span></label>
                                <a href="#" class="fs-12 text-primary kv-open-category-modal">
                                    <i class="ri-add-line"></i> Add new
                                </a>
                            </div>
                            <select class="form-select kv-select2-category" id="category_ids" name="category_ids[]"
                                data-selected-category-code="">
                                <option value=""></option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-category-code="{{ $category->code }}">
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback d-block" id="error-category_ids"></div>
                        </div>
                    </div>

                    {{-- Unique Code Display --}}
                    <div id="unique-code-section" class="d-none mb-1">
                        <label class="form-label fw-medium">
                            Auto-Generated Unique Code
                            <span class="spinner-border spinner-border-sm text-primary ms-1 d-none" id="unique-code-spinner"></span>
                        </label>
                        <div class="kv-code-display-wrap">
                            <input type="text" class="form-control kv-code-field font-monospace"
                                id="unique_code" name="unique_code" readonly
                                placeholder="Select brand & category first">
                            <i class="ri-lock-line kv-code-lock-icon text-muted"></i>
                        </div>
                        <div class="invalid-feedback d-block" id="error-unique_code"></div>
                    </div>

                    {{-- ── SECTION: SPECIFICATIONS ───────────────────────── --}}
                    <div class="kv-section-title mt-3">
                        <i class="ri-aspect-ratio-line"></i><span>Specifications</span>
                    </div>

                    <div class="row g-3 mb-1">
                        <div class="col-md-6">
                            <label for="minimum_res_width" class="form-label fw-medium">
                                Minimum Width <span class="text-muted fw-normal">(px)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-arrow-left-right-line"></i></span>
                                <input type="number" min="0" class="form-control" id="minimum_res_width"
                                    name="minimum_res_width" placeholder="e.g. 1920">
                            </div>
                            <div class="invalid-feedback" id="error-minimum_res_width"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="minimum_res_height" class="form-label fw-medium">
                                Minimum Height <span class="text-muted fw-normal">(px)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-arrow-up-down-line"></i></span>
                                <input type="number" min="0" class="form-control" id="minimum_res_height"
                                    name="minimum_res_height" placeholder="e.g. 1080">
                            </div>
                            <div class="invalid-feedback" id="error-minimum_res_height"></div>
                        </div>
                    </div>

                    {{-- ── SECTION: MEDIA FILES ──────────────────────────── --}}
                    <div class="kv-section-title mt-3">
                        <i class="ri-folder-image-line"></i><span>Media Files</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="kv-upload-section">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-medium mb-0">Sample File</label>
                                    <span class="kv-upload-type-tag" id="kv-sample-type-tag">
                                        <i class="ri-image-2-line"></i> Image
                                    </span>
                                </div>
                                <input type="file" class="filepond-kv-sample" id="kv_sample_file"
                                    name="kv_sample_file" accept="image/jpeg,image/png,image/webp">
                                <div class="kv-upload-hint" id="kv-sample-help-text">
                                    <i class="ri-information-line me-1"></i>JPG / PNG / WEBP &mdash; max 5 MB &mdash; exact 1920 &times; 1080 px
                                </div>
                                <div class="invalid-feedback d-block" id="error-kv_sample_file"></div>
                                <div id="existing-sample-wrap" class="kv-existing-file d-none">
                                    <i class="ri-file-line me-1"></i>
                                    Current: <a href="#" id="existing-sample-link" target="_blank" rel="noopener">Open file</a>
                                    <small class="text-muted">(uploading a new file replaces it)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kv-upload-section">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-medium mb-0">Thumbnail</label>
                                    <span class="kv-upload-type-tag kv-upload-type-thumb">
                                        <i class="ri-image-line"></i> Image
                                    </span>
                                </div>
                                <input type="file" class="filepond-kv-thumb" id="kv_thumb"
                                    name="kv_thumb" accept="image/jpeg,image/png,image/webp">
                                <div class="kv-upload-hint">
                                    <i class="ri-information-line me-1"></i>JPG / PNG / WEBP &mdash; max 3 MB &mdash; auto-resized to 300 &times; 300
                                </div>
                                <div class="invalid-feedback d-block" id="error-kv_thumb"></div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end modal-body --}}

                <div class="modal-footer kv-modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="btn-save">
                        <span class="btn-text"><i class="ri-save-line me-1"></i>Save</span>
                        <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     VIEW MODAL
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="ri-eye-line me-2 text-info"></i>Key Visual Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="view-thumb-box">
                            <img id="view-thumb" class="d-none" src="" alt="">
                            <div id="view-thumb-placeholder"><i class="ri-image-line"></i></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm table-bordered mb-0">
                            <tr><th width="38%">Name</th><td id="view-name"></td></tr>
                            <tr><th>Unique Code</th><td id="view-unique-code"></td></tr>
                            <tr><th>Asset Type</th><td id="view-asset-type"></td></tr>
                            <tr><th>KV Type</th><td id="view-kv-type"></td></tr>
                            <tr><th>Min Resolution</th><td id="view-resolution"></td></tr>
                            <tr><th>Sample File</th><td id="view-sample-file"></td></tr>
                            <tr><th>Status</th><td id="view-status"></td></tr>
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

{{-- ═══════════════════════════════════════════════════════
     DELETE CONFIRMATION MODAL
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body p-4 pb-2">
                <div class="mb-3">
                    <span class="avatar avatar-lg bg-danger-transparent rounded-circle">
                        <i class="ri-delete-bin-line text-danger fs-24"></i>
                    </span>
                </div>
                <h6 class="fw-semibold mb-1">Delete Key Visual?</h6>
                <p class="text-muted fs-13 mb-0">
                    You are about to delete<br>
                    <strong id="delete-kv-name" class="text-dark"></strong>
                </p>
                <p class="text-danger fs-11 mt-1 mb-0">This action cannot be undone.</p>
                <input type="hidden" id="delete-kv-id">
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

{{-- ═══════════════════════════════════════════════════════
     CREATE BRAND MODAL (child)
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="brandModalLabel">
                    <i class="ri-building-line me-2 text-primary"></i>Add Brand
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="kvBrandForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kv_brand_name" class="form-label fw-medium">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kv_brand_name" name="name" placeholder="Enter brand name">
                        <div class="invalid-feedback" id="error-kv-brand-name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="kv_brand_code" class="form-label fw-medium">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase font-monospace fw-bold" id="kv_brand_code"
                            name="code" placeholder="2-3 letter code" maxlength="3" readonly>
                        <div class="form-text">Auto-generated from brand name. Click to override.</div>
                        <div class="invalid-feedback" id="error-kv-brand-code"></div>
                    </div>
                    <div class="mb-3">
                        <label for="kv_brand_description" class="form-label fw-medium">Description</label>
                        <textarea class="form-control" id="kv_brand_description" name="description"
                            rows="2" placeholder="Optional description"></textarea>
                        <div class="invalid-feedback" id="error-kv-brand-description"></div>
                    </div>
                    <div class="mb-3">
                        <label for="kv_brand_logo" class="form-label fw-medium">Logo</label>
                        <input type="file" class="filepond-kv-brand-logo" id="kv_brand_logo" name="logo"
                            accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp">
                        <div class="invalid-feedback d-block" id="error-kv-brand-logo"></div>
                    </div>
                    <div class="kv-status-wrapper">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="kv_brand_status_switch" checked>
                            <label class="form-check-label fw-medium" for="kv_brand_status_switch" id="kv-brand-status-label">Published</label>
                        </div>
                        <input type="hidden" id="kv_brand_status" name="status" value="1">
                        <div class="invalid-feedback" id="error-kv-brand-status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="kv-brand-btn-save">
                        <span class="btn-text"><i class="ri-save-line me-1"></i>Save Brand</span>
                        <span class="spinner-border spinner-border-sm d-none" id="kv-brand-btn-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     CREATE CATEGORY MODAL (child)
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="categoryModalLabel">
                    <i class="ri-list-check-3 me-2 text-primary"></i>Add Category
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="kvCategoryForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kv_category_name" class="form-label fw-medium">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kv_category_name" name="name" placeholder="Enter category name">
                        <div class="invalid-feedback" id="error-kv-category-name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="kv_category_code" class="form-label fw-medium">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase font-monospace fw-bold" id="kv_category_code"
                            name="code" placeholder="2-3 letter code" maxlength="3" readonly>
                        <div class="form-text">Auto-generated from category name. Click to override.</div>
                        <div class="invalid-feedback" id="error-kv-category-code"></div>
                    </div>
                    <div class="mb-3">
                        <label for="kv_category_description" class="form-label fw-medium">Description</label>
                        <textarea class="form-control" id="kv_category_description" name="description"
                            rows="2" placeholder="Optional description"></textarea>
                        <div class="invalid-feedback" id="error-kv-category-description"></div>
                    </div>
                    <div class="kv-status-wrapper">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="kv_category_status_switch" checked>
                            <label class="form-check-label fw-medium" for="kv_category_status_switch" id="kv-category-status-label">Published</label>
                        </div>
                        <input type="hidden" id="kv_category_status" name="status" value="1">
                        <div class="invalid-feedback" id="error-kv-category-status"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="kv-category-btn-save">
                        <span class="btn-text"><i class="ri-save-line me-1"></i>Save Category</span>
                        <span class="spinner-border spinner-border-sm d-none" id="kv-category-btn-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     SHOW KV SIZES MODAL (child)
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="KvSizesModal" >
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="categoryModalLabel">
                    <i class="ri-list-check-3 me-2 text-primary"></i>Show KV Available Sizes
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2-bootstrap-5-theme.min.css') }}">
<style>
/* ── TABLE THUMB ──────────────────────────────────────────── */
.kv-thumb {
    width: 44px; height: 44px; object-fit: cover;
    border-radius: 8px; border: 1px solid var(--default-border);
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.kv-thumb-empty {
    width: 44px; height: 44px; border-radius: 8px;
    border: 1px dashed var(--default-border);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 1.1rem;
    background: var(--light);
}
.kv-code-badge { font-size: 0.75rem; letter-spacing: 0.04em; }
.btn-list { display: flex; gap: 3px; }
.btn-list .btn-edit,
.btn-list .btn-delete,
.btn-list .btn-view,
.btn-list .btn-sizes {
    width: 26px; height: 26px; min-width: 26px;
    padding: 0; font-size: 0.7rem;
    display: inline-flex; align-items: center; justify-content: center;
}

/* ── MODAL ────────────────────────────────────────────────── */
.kv-modal-header {
    background: linear-gradient(135deg, rgba(var(--primary-rgb),.06) 0%, transparent 100%);
    border-bottom: 1px solid var(--default-border);
}
.kv-modal-footer {
    background: var(--light);
    border-top: 1px solid var(--default-border);
}

/* ── SECTION TITLES ───────────────────────────────────────── */
.kv-section-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: rgb(var(--primary-rgb));
    margin-bottom: 0.875rem;
}
.kv-section-title::after {
    content: ''; flex: 1; height: 1px;
    background: var(--default-border);
}
.kv-section-title:first-child { margin-top: 0; }

/* ── KV TYPE SELECTOR (radio buttons styled as toggle) ──── */
.kv-type-selector {
    display: flex; gap: 0.5rem;
}
.kv-type-option {
    flex: 1; cursor: pointer;
    border: 1.5px solid var(--default-border);
    border-radius: 8px; padding: 0.5rem 0.75rem;
    display: flex; align-items: center; justify-content: center;
    gap: 0.4rem; font-size: 0.875rem; font-weight: 500;
    transition: all .2s ease; color: var(--text-muted);
    background: var(--custom-white);
}
.kv-type-option input[type="radio"] { display: none; }
.kv-type-option:has(input:checked) {
    border-color: rgb(var(--primary-rgb));
    color: rgb(var(--primary-rgb));
    background: rgba(var(--primary-rgb), .07);
}
.kv-type-option:hover { border-color: rgba(var(--primary-rgb), .4); }

/* ── STATUS WRAPPER ───────────────────────────────────────── */
.kv-status-wrapper {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 1rem;
    background: var(--light);
    border-radius: 8px;
    border: 1px solid var(--default-border);
}
.kv-status-wrapper .ms-auto { margin-left: auto; }

/* ── UNIQUE CODE ──────────────────────────────────────────── */
.kv-code-display-wrap {
    position: relative;
}
.kv-code-field {
    font-family: 'Courier New', Courier, monospace !important;
    font-size: 1rem !important; font-weight: 700 !important;
    letter-spacing: 0.06em;
    background: rgba(var(--primary-rgb), .05) !important;
    border-color: rgba(var(--primary-rgb), .3) !important;
    color: rgb(var(--primary-rgb)) !important;
    text-align: center; padding-right: 2.5rem;
}
.kv-code-field::placeholder { color: rgba(var(--primary-rgb),.35); font-weight: 400; font-size: 0.825rem; letter-spacing: 0; }
.kv-code-lock-icon {
    position: absolute; right: 0.85rem; top: 50%;
    transform: translateY(-50%); font-size: 0.85rem;
    pointer-events: none;
}

/* ── FILE UPLOAD AREAS ────────────────────────────────────── */
.kv-upload-type-tag {
    display: inline-flex; align-items: center; gap: 0.25rem;
    font-size: 0.64rem; font-weight: 700; text-transform: uppercase;
    padding: 0.15rem 0.55rem; border-radius: 20px;
}
#kv-sample-type-tag, .kv-upload-type-thumb {
    background: rgba(var(--warning-rgb), .12);
    color: rgb(var(--warning-rgb));
}
#kv-sample-type-tag.is-video {
    background: rgba(var(--info-rgb), .12);
    color: rgb(var(--info-rgb));
}
.kv-upload-hint {
    font-size: 0.74rem; color: var(--text-muted);
    margin-top: 0.35rem; line-height: 1.4;
}
.kv-existing-file {
    font-size: 0.78rem; margin-top: 0.4rem;
    padding: 0.35rem 0.6rem;
    background: rgba(var(--info-rgb),.07);
    border-radius: 6px; border: 1px solid rgba(var(--info-rgb),.2);
    color: var(--text-muted);
}
.kv-existing-file a { font-weight: 600; }

/* ── SELECT2 IN MODAL ─────────────────────────────────────── */
.select2-container { z-index: 99999 !important; width: 100% !important; }

/* ── FILEPOND ─────────────────────────────────────────────── */
.filepond--root { margin-bottom: 0; }
.filepond--panel-root { border-radius: 8px !important; }

/* ── VIEW MODAL THUMB ─────────────────────────────────────── */
.view-thumb-box img {
    width: 100%; border-radius: 10px; object-fit: cover;
    border: 1px solid var(--default-border);
}
#view-thumb-placeholder {
    width: 100%; aspect-ratio: 1/1;
    background: var(--light); border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem; color: var(--text-muted);
    border: 1px dashed var(--default-border);
}
</style>
@endpush

@push('scripts')
@include('backend.includes.plugins.datatable')
<script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
<script src="{{ asset('backend/build/select2-4.1.0/select2.min.js') }}"></script>
<script>
$(function () {
    // ── CONSTANTS ─────────────────────────────────────────
    const BASE          = base_url;
    const apiUrl        = (id) => BASE + 'key-visuals' + (id ? '/' + id : '');
    const NEXT_CODE_URL = @json(route('key-visuals.next-unique-code'));
    const BRAND_URL     = BASE + 'brands';
    const CAT_URL       = BASE + 'categories';

    // ── MODAL INSTANCES ───────────────────────────────────
    const kvModal      = new bootstrap.Modal('#keyVisualModal');
    const viewModalEl  = new bootstrap.Modal('#viewModal');
    const deleteModal  = new bootstrap.Modal('#deleteModal');
    const brandModal   = new bootstrap.Modal('#brandModal');
    const catModal     = new bootstrap.Modal('#categoryModal');
    let restoreKvModal = false;

    // ── UNIQUE CODE REQUEST HANDLE ────────────────────────
    let codeRequest = null;

    // ── FILE UPLOAD MODES ─────────────────────────────────
    const FILE_MODES = {
        image: {
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
            accept: 'image/jpeg,image/png,image/webp',
            maxFileSize: '5MB',
            labelIdle: '<i class="ri-image-2-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop image or <span class="filepond--label-action">browse</span></span>',
            helpText: 'JPG / PNG / WEBP - max 5 MB - exact 1920 x 1080 px',
            tagLabel: '<i class="ri-image-2-line"></i> Image',
            tagClass: '',
        },
        video: {
            acceptedFileTypes: ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm'],
            accept: 'video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm',
            maxFileSize: '30MB',
            labelIdle: '<i class="ri-video-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop video or <span class="filepond--label-action">browse</span></span>',
            helpText: 'MP4 / MOV / AVI / MKV / WEBM - max 30 MB',
            tagLabel: '<i class="ri-video-line"></i> Video',
            tagClass: 'is-video',
        },
    };

    // ── FILEPOND ──────────────────────────────────────────
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize
    );

    const pondConfig = (labelIdle, types, size) => ({
        allowMultiple: false, instantUpload: false, allowProcess: false,
        allowRevert: false, maxFiles: 1, credits: false,
        acceptedFileTypes: types, maxFileSize: size, labelIdle,
    });

    const kvSamplePond = FilePond.create(
        document.querySelector('.filepond-kv-sample'),
        pondConfig(FILE_MODES.image.labelIdle, FILE_MODES.image.acceptedFileTypes, FILE_MODES.image.maxFileSize)
    );

    const kvThumbPond = FilePond.create(
        document.querySelector('.filepond-kv-thumb'),
        pondConfig(
            '<i class="ri-image-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop thumbnail or <span class="filepond--label-action">browse</span></span>',
            ['image/jpeg', 'image/png', 'image/webp'], '3MB'
        )
    );

    const brandLogoPond = FilePond.create(
        document.querySelector('.filepond-kv-brand-logo'),
        pondConfig(
            '<i class="ri-upload-cloud-2-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop logo or <span class="filepond--label-action">browse</span></span>',
            ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'], '2MB'
        )
    );

    // Clear file errors on add/remove
    kvSamplePond.on('addfile',   (err) => !err && clearFieldError('kv_sample_file'));
    kvSamplePond.on('removefile', ()   => clearFieldError('kv_sample_file'));
    kvThumbPond.on('addfile',    (err) => !err && clearFieldError('kv_thumb'));
    kvThumbPond.on('removefile', ()    => clearFieldError('kv_thumb'));
    brandLogoPond.on('addfile',  (err) => !err && $('#error-kv-brand-logo').text(''));
    brandLogoPond.on('removefile', ()  => $('#error-kv-brand-logo').text(''));

    function updateSampleUploader(kvType, clearFile = true) {
        const mode = FILE_MODES[kvType] || FILE_MODES.image;
        kvSamplePond.setOptions({
            acceptedFileTypes: mode.acceptedFileTypes,
            maxFileSize: mode.maxFileSize,
            labelIdle: mode.labelIdle,
        });
        $('#kv_sample_file').attr('accept', mode.accept);
        $('#kv-sample-help-text').html('<i class="ri-information-line me-1"></i>' + mode.helpText);
        $('#kv-sample-type-tag').html(mode.tagLabel).toggleClass('is-video', kvType === 'video');
        if (clearFile) kvSamplePond.removeFiles();
        clearFieldError('kv_sample_file');
    }

    // ── SELECT2 ───────────────────────────────────────────
    const s2Opts = (placeholder, parent) => ({
        placeholder, allowClear: true, theme: 'bootstrap-5',
        dropdownParent: $(parent), width: '100%',
    });

    $('#brand_ids').select2(s2Opts('Select Brand', '#keyVisualModal'));
    $('#category_ids').select2(s2Opts('Select Category', '#keyVisualModal'));

    // ── HELPER FUNCTIONS ──────────────────────────────────
    function showToast(message, type = 'success') {
        const $t = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:99999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).appendTo('body');
        setTimeout(() => $t.remove(), 3500);
    }

    function clearErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('.filepond--root').removeClass('is-invalid');
    }

    function clearFieldError(field) {
        $('#error-' + field).text('');
    }

    function setError(inputSel, errorId, msg) {
        $(inputSel).addClass('is-invalid');
        $(errorId).text(msg);
    }

    function setLoading($btn, loading) {
        $btn.prop('disabled', loading);
        $btn.find('.spinner-border').toggleClass('d-none', !loading);
    }

    function generateShortCode(rawName) {
        const name  = String(rawName || '').trim();
        if (name.length < 2) return '';
        const words = name.split(/\s+/).filter(Boolean);
        let code = '';
        if (words.length >= 3) {
            code = words.map(w => w[0]).join('').substring(0, 3);
        } else if (words.length === 2) {
            code = words[0].substring(0, 2) + words[1][0];
        } else {
            const consonants = name.replace(/[aeiou]/gi, '');
            code = consonants.length >= 2
                ? name[0] + consonants[1] + (consonants[2] || name[name.length - 1])
                : name.substring(0, 3);
        }
        return code.toUpperCase().substring(0, 3);
    }

    function validateKvForm() {
        let ok  = true;
        const id = String($('#key_visual_id').val() || '').trim();

        if (!$('#name').val().trim()) {
            setError('#name', '#error-name', 'Key visual name is required.'); ok = false;
        }
        if (!$('#asset_type_id').val()) {
            setError('#asset_type_id', '#error-asset_type_id', 'Please select an asset type.'); ok = false;
        }
        if (!id) {
            if (!$('#brand_ids').val()) {
                setError('#brand_ids', '#error-brand_ids', 'Please select a brand.'); ok = false;
            }
            if (!$('#category_ids').val()) {
                setError('#category_ids', '#error-category_ids', 'Please select a category.'); ok = false;
            }
        }
        const code = ($('#unique_code').val() || '').trim();
        if (!id && code && code.includes('...')) {
            setError('#unique_code', '#error-unique_code', 'Please wait for the code to finish generating.'); ok = false;
        }
        return ok;
    }

    // ── UNIQUE CODE ───────────────────────────────────────
    function clearCode() {
        $('#unique_code').val('');
        $('#unique-code-section').addClass('d-none');
        $('#unique-code-spinner').addClass('d-none');
    }

    function fetchNextCode() {
        if ($('#key_visual_id').val()) return; // skip on edit

        const brandCode = ($('#brand_ids').find('option:selected').data('brand-code') || '').trim();
        const catCode   = ($('#category_ids').find('option:selected').data('category-code') || '').trim();

        if (!brandCode || !catCode) { clearCode(); return; }

        $('#unique-code-section').removeClass('d-none');
        $('#unique_code').val(`${brandCode}-${catCode}-...`);
        $('#unique-code-spinner').removeClass('d-none');

        if (codeRequest) codeRequest.abort();

        codeRequest = $.get(NEXT_CODE_URL, { brand_code: brandCode, category_code: catCode })
            .done(function (res) {
                $('#unique_code').val(res.unique_code || '');
                $('#unique-code-section').toggleClass('d-none', !res.unique_code);
            })
            .fail(function (xhr) {
                if (xhr.status !== 0) clearCode();
            })
            .always(function () {
                $('#unique-code-spinner').addClass('d-none');
                codeRequest = null;
            });
    }

    // ── FORM MANAGEMENT ───────────────────────────────────
    function resetForm() {
        $('#keyVisualForm')[0].reset();
        $('#key_visual_id').val('');
        // Reset KV type radio to image
        $('input[name="kv_type"][value="image"]').prop('checked', true);
        // Reset Select2
        $('#brand_ids').val(null).trigger('change');
        $('#category_ids').val(null).trigger('change');
        // Reset file uploaders
        kvSamplePond.removeFiles();
        kvThumbPond.removeFiles();
        updateSampleUploader('image', false);
        // Reset unique code
        clearCode();
        // Reset existing file link
        $('#existing-sample-link').attr('href', '#');
        $('#existing-sample-wrap').addClass('d-none');
        // Reset status
        $('#status').prop('checked', true);
        clearErrors();
    }

    function openFormModal(mode) {
        resetForm();
        const isEdit = mode === 'edit';
        $('#keyVisualModalLabel').html(
            `<i class="ri-${isEdit ? 'edit-box' : 'image-add'}-line me-2 text-primary"></i>${isEdit ? 'Edit' : 'Add'} Key Visual`
        );
        $('#btn-save .btn-text').html(
            `<i class="ri-save-line me-1"></i>${isEdit ? 'Update' : 'Save'}`
        );
    }

    function populateForm(data) {
        $('#name').val(data.name || '');
        $('#asset_type_id').val(data.asset_type_id || '');
        // Resolution specs
        $('#minimum_res_width').val(data.minimum_res_width || '');
        $('#minimum_res_height').val(data.minimum_res_height || '');
        // KV type radio
        const kvType = data.kv_type || 'image';
        $(`input[name="kv_type"][value="${kvType}"]`).prop('checked', true);
        updateSampleUploader(kvType, true);
        // Status
        $('#status').prop('checked', data.status == 1);
        // Unique code (editing - already generated)
        if (data.unique_code) {
            $('#unique_code').val(data.unique_code);
            $('#unique-code-section').removeClass('d-none');
        }
        // Existing sample file link
        if (data.kv_sample_file) {
            $('#existing-sample-link').attr('href', BASE + data.kv_sample_file);
            $('#existing-sample-wrap').removeClass('d-none');
        }
        // Pre-select Brand
        if (data.selected_brand_id) {
            $('#brand_ids')
                .val(data.selected_brand_id)
                .attr('data-selected-brand-code', data.selected_brand_code || '')
                .trigger('change');
        }
        // Pre-select Category
        if (data.selected_category_id) {
            $('#category_ids')
                .val(data.selected_category_id)
                .attr('data-selected-category-code', data.selected_category_code || '')
                .trigger('change');
        }
    }

    // ── UPSERT SELECT OPTIONS AFTER CHILD MODAL SAVES ────
    function upsertBrandOption(brand) {
        if (!brand?.id) return;
        const id   = String(brand.id);
        const code = String(brand.code || '').toUpperCase();
        const text = `${brand.name} (${code})`;
        const $sel = $('#brand_ids');

        if ($sel.find(`option[value="${id}"]`).length === 0) {
            $sel.append($('<option>', { value: id, text }).attr('data-brand-code', code));
        } else {
            $sel.find(`option[value="${id}"]`).text(text).attr('data-brand-code', code);
        }
        $sel.val(id).attr('data-selected-brand-code', code).trigger('change');
    }

    function upsertCategoryOption(category) {
        if (!category?.id) return;
        const id   = String(category.id);
        const code = String(category.code || '').toUpperCase();
        const text = `${category.name} (${code})`;
        const $sel = $('#category_ids');

        if ($sel.find(`option[value="${id}"]`).length === 0) {
            $sel.append($('<option>', { value: id, text }).attr('data-category-code', code));
        } else {
            $sel.find(`option[value="${id}"]`).text(text).attr('data-category-code', code);
        }
        $sel.val(id).attr('data-selected-category-code', code).trigger('change');
    }

    // ── CHILD MODAL FLOW ─────────────────────────────────
    function openChildModal(childModal) {
        const kvOpen = $('#keyVisualModal').hasClass('show');
        restoreKvModal = kvOpen;
        if (kvOpen) {
            $('#keyVisualModal').one('hidden.bs.modal', () => childModal.show());
            kvModal.hide();
        } else {
            childModal.show();
        }
    }

    $('#brandModal, #categoryModal').on('hidden.bs.modal', function () {
        if (!restoreKvModal) return;
        kvModal.show();
        restoreKvModal = false;
    });

    // ── EVENT: SELECT2 BRAND / CATEGORY CHANGE ───────────
    $('#brand_ids').on('change', function () {
        const code = $(this).find('option:selected').data('brand-code') || '';
        $(this).attr('data-selected-brand-code', code);
        fetchNextCode();
    });

    $('#category_ids').on('change', function () {
        const code = $(this).find('option:selected').data('category-code') || '';
        $(this).attr('data-selected-category-code', code);
        fetchNextCode();
    });

    // ── EVENT: KV TYPE RADIO CHANGE ───────────────────────
    $('input[name="kv_type"]').on('change', function () {
        updateSampleUploader($(this).val(), true);
        // Update resolution hint for image type
        const isImage = $(this).val() === 'image';
        $('#minimum_res_width').attr('placeholder',  isImage ? '1920' : 'e.g. 3840');
        $('#minimum_res_height').attr('placeholder', isImage ? '1080' : 'e.g. 2160');
    });

    // ── EVENT: STATUS SWITCH ──────────────────────────────
    $('#status').on('change', function () {
        const on = $(this).is(':checked');
        $('#status-label').text(on ? 'Active' : 'Inactive');
        $('#status-sub-label').text(on ? 'This KV will be visible' : 'This KV is hidden');
    });

    // ── EVENT: CODE FIELD OVERRIDE (click to unlock) ─────
    $('#kv_brand_code').on('click', function () { $(this).removeAttr('readonly'); });
    $('#kv_category_code').on('click', function () { $(this).removeAttr('readonly'); });

    // ── EVENT: BRAND / CATEGORY CODE AUTO-GEN ────────────
    $('#kv_brand_name').on('input', function () {
        if (!$('#kv_brand_code').is('[readonly]') === false || !$('#kv_brand_code').val()) {
            $('#kv_brand_code').val(generateShortCode($(this).val()));
        }
    });
    $('#kv_category_name').on('input', function () {
        if (!$('#kv_category_code').val() || !$('#kv_category_code').is(':focus')) {
            $('#kv_category_code').val(generateShortCode($(this).val()));
        }
    });

    // ── EVENT: STATUS SWITCHES (child modals) ─────────────
    $('#kv_brand_status_switch').on('change', function () {
        const on = $(this).is(':checked');
        $('#kv_brand_status').val(on ? '1' : '0');
        $('#kv-brand-status-label').text(on ? 'Published' : 'Unpublished');
    }).trigger('change');

    $('#kv_category_status_switch').on('change', function () {
        const on = $(this).is(':checked');
        $('#kv_category_status').val(on ? '1' : '0');
        $('#kv-category-status-label').text(on ? 'Published' : 'Unpublished');
    }).trigger('change');

    // ── EVENT: OPEN CHILD MODALS ──────────────────────────
    $(document).on('click', '.kv-open-brand-modal',    (e) => { e.preventDefault(); openChildModal(brandModal); });
    $(document).on('click', '.kv-open-category-modal', (e) => { e.preventDefault(); openChildModal(catModal); });

    // Reset child forms when their modals open
    $('#brandModal').on('show.bs.modal', function () {
        $('#kvBrandForm')[0].reset();
        $('#kv_brand_code').val('').attr('readonly', true);
        brandLogoPond.removeFiles();
        $('#kv_brand_status_switch').prop('checked', true).trigger('change');
        $('#kvBrandForm .is-invalid').removeClass('is-invalid');
        $('#kvBrandForm .invalid-feedback').text('');
    });
    $('#categoryModal').on('show.bs.modal', function () {
        $('#kvCategoryForm')[0].reset();
        $('#kv_category_code').val('').attr('readonly', true);
        $('#kv_category_status_switch').prop('checked', true).trigger('change');
        $('#kvCategoryForm .is-invalid').removeClass('is-invalid');
        $('#kvCategoryForm .invalid-feedback').text('');
    });

    // ── EVENT: ADD BUTTON ─────────────────────────────────
    $('#btn-add-key-visual').on('click', function () {
        openFormModal('add');
        kvModal.show();
    });

    // ── EVENT: EDIT BUTTON ────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        openFormModal('edit');
        const id = $(this).data('id');
        $.get(apiUrl(id) + '/edit')
            .done(function (data) {
                $('#key_visual_id').val(data.id);
                populateForm(data);
                kvModal.show();
            })
            .fail(function () {
                showToast('Failed to load key visual data.', 'danger');
            });
    });

    // ── EVENT: KV SIZES VIEW BUTTON ────────────────────────────────
    $(document).on('click', '.size-btn-view', function () {
        const id = $(this).data('id');
        $('#KvSizesModal').modal('show');
    })

    // ── EVENT: VIEW BUTTON ────────────────────────────────
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $.get(apiUrl(id))
            .done(function (data) {
                $('#view-name').text(data.name || 'N/A');
                $('#view-unique-code').html(`<span class="badge bg-primary-transparent font-monospace">${data.unique_code || 'N/A'}</span>`);
                $('#view-asset-type').text(data.asset_type?.name ?? 'N/A');
                $('#view-kv-type').html(`<span class="badge ${data.kv_type === 'video' ? 'bg-info-transparent' : 'bg-warning-transparent'} text-uppercase">
                    <i class="ri-${data.kv_type === 'video' ? 'video' : 'image-2'}-line me-1"></i>${data.kv_type || 'image'}</span>`);
                const res = data.minimum_res_width || data.minimum_res_height;
                $('#view-resolution').html(res
                    ? `<span class="font-monospace">${data.minimum_res_width || 0} x ${data.minimum_res_height || 0} px</span>`
                    : 'N/A');
                $('#view-sample-file').html(data.kv_sample_file
                    ? `<a href="${BASE + data.kv_sample_file}" target="_blank" rel="noopener"><i class="ri-external-link-line me-1"></i>Open file</a>`
                    : 'N/A');
                $('#view-status').html(data.status == 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-thumb').toggleClass('d-none', !data.kv_thumb).attr('src', data.kv_thumb ? BASE + data.kv_thumb : '');
                $('#view-thumb-placeholder').toggle(!data.kv_thumb);
                viewModalEl.show();
            })
            .fail(function () {
                showToast('Failed to load details.', 'danger');
            });
    });

    // ── EVENT: DELETE BUTTON ──────────────────────────────
    $(document).on('click', '.btn-delete', function () {
        $('#delete-kv-id').val($(this).data('id'));
        $('#delete-kv-name').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btn-confirm-delete').on('click', function () {
        const $btn = $(this);
        setLoading($btn, true);
        $.ajax({
            url: apiUrl($('#delete-kv-id').val()),
            type: 'DELETE',
            success: function (res) {
                deleteModal.hide();
                showToast(res.message || 'Deleted successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function () { showToast('Failed to delete key visual.', 'danger'); },
            complete: function () { setLoading($btn, false); },
        });
    });

    // ── EVENT: MAIN FORM SUBMIT ───────────────────────────
    $('#keyVisualForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();
        if (!validateKvForm()) return;

        const id       = $('#key_visual_id').val();
        const formData = new FormData(this);

        // Ensure correct status value (checkbox may not submit when unchecked)
        formData.set('status', $('#status').is(':checked') ? 1 : 0);

        // Replace FilePond virtual input with actual file object
        formData.delete('kv_sample_file');
        const sampleFile = kvSamplePond.getFile();
        if (sampleFile?.file) formData.append('kv_sample_file', sampleFile.file);

        formData.delete('kv_thumb');
        const thumbFile = kvThumbPond.getFile();
        if (thumbFile?.file) formData.append('kv_thumb', thumbFile.file);

        // Method spoofing for PUT
        if (id) formData.append('_method', 'PUT');

        const $saveBtn = $('#btn-save');
        setLoading($saveBtn, true);

        $.ajax({
            url: apiUrl(id || null),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                kvModal.hide();
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        if (field === 'kv_sample_file') {
                            $('#kv_sample_file').closest('.filepond--root').addClass('is-invalid');
                        } else if (field === 'kv_thumb') {
                            $('#kv_thumb').closest('.filepond--root').addClass('is-invalid');
                        } else {
                            $('#' + field).addClass('is-invalid');
                        }
                        $('#error-' + field).text(messages[0]);
                    });
                } else {
                    showToast(xhr.responseJSON?.message || 'Something went wrong. Please try again.', 'danger');
                }
            },
            complete: function () { setLoading($saveBtn, false); },
        });
    });

    // ── EVENT: BRAND FORM SUBMIT ──────────────────────────
    $('#kvBrandForm').on('submit', function (e) {
        e.preventDefault();
        $('#kvBrandForm .is-invalid').removeClass('is-invalid');
        $('#kvBrandForm .invalid-feedback').text('');

        const formData = new FormData(this);
        formData.set('status', $('#kv_brand_status').val());
        formData.delete('logo');
        const logoFile = brandLogoPond.getFile();
        if (logoFile?.file) formData.append('logo', logoFile.file);

        const $brandBtn = $('#kv-brand-btn-save');
        setLoading($brandBtn, true);

        $.ajax({
            url: BRAND_URL, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (res) {
                upsertBrandOption(res.data || null);
                brandModal.hide();
                showToast(res.message || 'Brand created successfully.', 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const map = { name: '#kv_brand_name', code: '#kv_brand_code', description: '#kv_brand_description' };
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        if (map[field]) $(map[field]).addClass('is-invalid');
                        if (field === 'logo') $('#kv_brand_logo').closest('.filepond--root').addClass('is-invalid');
                        $(`#error-kv-brand-${field}`).text(messages[0]);
                    });
                } else {
                    showToast('Failed to create brand.', 'danger');
                }
            },
            complete: function () { setLoading($brandBtn, false); },
        });
    });

    // ── EVENT: CATEGORY FORM SUBMIT ───────────────────────
    $('#kvCategoryForm').on('submit', function (e) {
        e.preventDefault();
        $('#kvCategoryForm .is-invalid').removeClass('is-invalid');
        $('#kvCategoryForm .invalid-feedback').text('');

        const formData = new FormData(this);
        formData.set('status', $('#kv_category_status').val());

        const $catBtn = $('#kv-category-btn-save');
        setLoading($catBtn, true);

        $.ajax({
            url: CAT_URL, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (res) {
                upsertCategoryOption(res.data || null);
                catModal.hide();
                showToast(res.message || 'Category created successfully.', 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const map = { name: '#kv_category_name', code: '#kv_category_code', description: '#kv_category_description' };
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        if (map[field]) $(map[field]).addClass('is-invalid');
                        $(`#error-kv-category-${field}`).text(messages[0]);
                    });
                } else {
                    showToast('Failed to create category.', 'danger');
                }
            },
            complete: function () { setLoading($catBtn, false); },
        });
    });

}); // end document.ready
</script>
@endpush
