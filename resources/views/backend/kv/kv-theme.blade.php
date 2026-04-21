@extends('backend.master')

@section('title', 'Key Visuals')

@section('body')
@php
    $activeCount = $keyVisuals->where('status', 1)->count();
    $archivedCount = $keyVisuals->where('status', 0)->count();
@endphp

<div class="container px-3 px-lg-4 py-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active"><i class="bi bi-images me-1"></i>KV</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
        <div>
            <h1 class="page-title mb-1">Key Visual Management</h1>
            <p class="page-subtitle mb-1">Manage brand assets, categories, and visual content for retail branding</p>
            <small class="text-muted">Role: Admin &bull; Current View: Active KVs</small>
        </div>
        <div class="page-header-actions d-flex gap-2 flex-wrap">
{{--            <button type="button" class="btn btn-outline-secondary btn-sm"><i class="bi bi-journal-text me-1"></i>Audit Log (0)</button>--}}
            <button type="button" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i>Export Active</button>
            <button type="button" class="btn btn-warning btn-sm text-white btn-add-key-visual" id="btn-add-key-visual"><i class="bi bi-plus-circle me-1"></i>Add Key Visual</button>
        </div>
    </div>

    <div class="d-flex gap-2 mb-3">
        <button type="button" class="btn btn-sm kv-toggle-btn kv-toggle-active" id="btnActiveToggle" data-filter-status="1">
            <i class="bi bi-eye me-1"></i>Active ({{ $activeCount }})
        </button>
        <button type="button" class="btn btn-sm kv-toggle-btn kv-toggle-inactive" id="btnArchivedToggle" data-filter-status="0">
            <i class="bi bi-archive me-1"></i>Archived ({{ $archivedCount }})
        </button>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#eef1f6;color:#2c3e6b;"><i class="bi bi-images"></i></div>
                <div>
                    <div class="stat-value">{{ $activeCount }}</div>
                    <div class="stat-label">Active Visuals</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-tag"></i></div>
                <div>
                    <div class="stat-value" id="stat-brands-count">{{ $brands->count() }}</div>
                    <div class="stat-label">Active Brands</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-folder"></i></div>
                <div>
                    <div class="stat-value" id="stat-categories-count">{{ $categories->count() }}</div>
                    <div class="stat-label">Categories</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background:#ede7f6;color:#5e35b1;"><i class="bi bi-graph-up-arrow"></i></div>
                <div>
                    <div class="stat-value">{{ $keyVisuals->count() }}</div>
                    <div class="stat-label">Total Visuals</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="content-card kv-brand-card">
                <div class="p-3 border-bottom">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="kv-section-icon"><i class="bi bi-gear"></i></span>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size:0.95rem;">Brand & Category Management</h6>
                            <small class="text-muted">Manage brands and categories</small>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs-custom nav-tabs border-bottom px-3 pt-2" id="brandCatTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#kvBrandsPane" type="button">
                            <i class="bi bi-tag me-1"></i>Brands ({{ $brands->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#kvCategoriesPane" type="button">
                            <i class="bi bi-folder me-1"></i>Categories ({{ $categories->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="kvBrandsPane">
                        <div class="p-3 border-bottom">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#brandModal">
                                <i class="bi bi-plus me-1"></i>Add Brand
                            </button>
                        </div>
                        <div class="kv-list-scroll">
                            @forelse($brands as $brand)
                                <div class="kv-brand-item" data-brand-id="{{ $brand->id }}">
                                    <div>
                                        <span class="kv-brand-name">{{ $brand->name }}</span>
                                        <span class="kv-brand-code">{{ $brand->code }}</span>
                                        <div class="kv-brand-desc">{{ str()->words($brand->description, 10, '') }}</div>
                                    </div>
                                    <div class="kv-brand-actions">
                                        <button type="button" class="btn-action kv-sidebar-brand-edit"
                                            data-id="{{ $brand->id }}" title="Edit Brand">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn-action text-danger kv-sidebar-brand-delete"
                                            data-id="{{ $brand->id }}" data-name="{{ $brand->name }}" title="Delete Brand">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 text-muted small" id="brands-empty-state">No brands found.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="tab-pane fade" id="kvCategoriesPane">
                        <div class="p-3 border-bottom">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="bi bi-plus me-1"></i>Add Category
                            </button>
                        </div>
                        <div class="kv-list-scroll">
                            @forelse($categories as $category)
                                <div class="kv-brand-item" data-category-id="{{ $category->id }}">
                                    <div>
                                        <span class="kv-brand-name">{{ $category->name }}</span>
                                        <span class="kv-brand-code">{{ $category->code }}</span>
                                        <div class="kv-brand-desc">{{ str()->words($category->description, 10, '') }}</div>
                                    </div>
                                    <div class="kv-brand-actions">
                                        <button type="button" class="btn-action kv-sidebar-category-edit"
                                            data-id="{{ $category->id }}" title="Edit Category">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn-action text-danger kv-sidebar-category-delete"
                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="Delete Category">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-3 text-muted small" id="categories-empty-state">No categories found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="content-card" id="kvMainContent">
                <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" id="kvGridHeader">
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:1rem;">Active Visual Repository</h6>
                        <small class="text-muted">{{ $activeCount }} active visuals available</small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="kvViewToggleBtn" data-bs-toggle="dropdown">Grid View</button>
                            <ul class="dropdown-menu kv-view-menu">
                                <li>
                                    <a class="dropdown-item kv-view-option active" href="#" data-view="grid">
                                        <i class="bi bi-grid-3x3-gap me-2"></i>Grid View
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item kv-view-option" href="#" data-view="list">
                                        <i class="bi bi-list-ul me-2"></i>List View
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm text-white btn-add-key-visual"><i class="bi bi-plus-circle me-1"></i>Add Key Visual</button>
                    </div>
                </div>

                <div class="px-3 pt-3 pb-2" id="kvGridSearch">
                    <input type="text" class="form-control form-control-sm" id="kv-search-input"
                        placeholder="Search visuals by KV ID, title, brand, or category...">
                </div>

                <div class="p-3" id="kvGridView">
                    <div class="row g-3" id="kvCardsGrid">
                        @forelse($keyVisuals as $keyVisual)
                            @php
                                $isVideo = ($keyVisual->kv_type ?? 'image') === 'video';
                                $isActive = (int) $keyVisual->status === 1;
                                $firstBrand = $keyVisual->brands->first();
                                $firstCategory = $keyVisual->categories->first();
                                $searchIndex = strtolower(trim(implode(' ', array_filter([
                                    $keyVisual->unique_code,
                                    $keyVisual->name,
                                    $firstBrand?->name,
                                    $firstBrand?->code,
                                    $firstCategory?->name,
                                    $firstCategory?->code,
                                    $keyVisual->assetType?->name,
                                ]))));
                            @endphp
                            <div class="col-12 col-sm-6 col-xl-4 kv-card-col"
                                data-status="{{ $isActive ? 1 : 0 }}"
                                data-search="{{ $searchIndex }}">
                                <div class="kv-card h-100">
                                    <div class="kv-card-thumb">
                                        @if($keyVisual->kv_thumb)
                                            <img src="{{ asset($keyVisual->kv_thumb) }}" alt="{{ $keyVisual->name }}">
                                        @else
                                            <div class="kv-thumb-empty w-100 h-100"><i class="ri-image-line"></i></div>
                                        @endif
                                        <span class="kv-card-type-badge {{ $isVideo ? 'kv-type-video' : 'kv-type-image' }}">
                                            <i class="bi {{ $isVideo ? 'bi-camera-video' : 'bi-image' }} me-1"></i>{{ strtoupper($keyVisual->kv_type ?? 'image') }}
                                        </span>
                                        <button type="button" class="kv-card-expand" data-id="{{ $keyVisual->id }}" title="View Details">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                    </div>
                                    <div class="kv-card-body">
                                        <h6 class="kv-card-title" title="{{ $keyVisual->name }}">{{ $keyVisual->name }}</h6>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="kv-card-id">{{ $keyVisual->unique_code }}</span>
                                            @if($isActive)
                                                <span class="kv-card-status-active"><i class="bi bi-check-circle me-1"></i>Active</span>
                                            @else
                                                <span class="badge bg-danger-transparent"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                                            @endif
                                        </div>
                                        <div class="kv-card-meta">{{ $firstBrand ? $firstBrand->name . ' (' . $firstBrand->code . ')' : 'No brand assigned' }}</div>
                                        <div class="kv-card-meta">{{ $firstCategory ? $firstCategory->name . ' (' . $firstCategory->code . ')' : 'No category assigned' }}</div>
                                        <div class="kv-card-meta"><i class="bi bi-folder2-open me-1"></i>{{ $keyVisual->assetType?->name ?? 'No asset type' }}</div>
                                        @if($keyVisual->minimum_res_width || $keyVisual->minimum_res_height)
                                            <div class="kv-card-meta"><i class="bi bi-aspect-ratio me-1"></i>{{ $keyVisual->minimum_res_width ?? 0 }} x {{ $keyVisual->minimum_res_height ?? 0 }} px</div>
                                        @endif
                                        <div class="d-flex gap-1 mt-2">
                                            <a href="{{ route('key-visual-files.index', ['kv' => $keyVisual->id]) }}" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line me-1"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $keyVisual->id }}">
                                                <i class="ri-edit-box-line me-1"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $keyVisual->id }}" data-name="{{ $keyVisual->name }}">
                                                <i class="ri-delete-bin-line me-1"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-5 border rounded-3 bg-light">No key visuals found.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="p-3 d-none" id="kvListView">
                    <div id="kvListRows">
                        @forelse($keyVisuals as $keyVisual)
                            @php
                                $isVideo = ($keyVisual->kv_type ?? 'image') === 'video';
                                $isActive = (int) $keyVisual->status === 1;
                                $firstBrand = $keyVisual->brands->first();
                                $firstCategory = $keyVisual->categories->first();
                                $searchIndex = strtolower(trim(implode(' ', array_filter([
                                    $keyVisual->unique_code,
                                    $keyVisual->name,
                                    $firstBrand?->name,
                                    $firstBrand?->code,
                                    $firstCategory?->name,
                                    $firstCategory?->code,
                                    $keyVisual->assetType?->name,
                                ]))));
                            @endphp
                            <div class="kv-list-row"
                                data-status="{{ $isActive ? 1 : 0 }}"
                                data-search="{{ $searchIndex }}">
                                <div class="kv-list-main">
                                    @if($keyVisual->kv_thumb)
                                        <img class="kv-list-thumb" src="{{ asset($keyVisual->kv_thumb) }}" alt="{{ $keyVisual->name }}">
                                    @else
                                        <div class="kv-list-thumb-empty"><i class="ri-image-line"></i></div>
                                    @endif
                                    <div>
                                        <h6 class="kv-list-title" title="{{ $keyVisual->name }}">{{ $keyVisual->name }}</h6>
                                        <div class="kv-list-sub">
                                            <span class="kv-card-id">{{ $keyVisual->unique_code }}</span>
                                            <span class="badge {{ $isVideo ? 'bg-info-transparent' : 'bg-warning-transparent' }} text-uppercase">
                                                <i class="ri-{{ $isVideo ? 'video' : 'image-2' }}-line me-1"></i>{{ $keyVisual->kv_type ?? 'image' }}
                                            </span>
                                            @if($isActive)
                                                <span class="kv-card-status-active"><i class="bi bi-check-circle me-1"></i>Active</span>
                                            @else
                                                <span class="badge bg-danger-transparent"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="kv-list-col">
                                    <small class="kv-list-col-label">Brand</small>
                                    <div class="kv-list-col-value">{{ $firstBrand ? $firstBrand->name . ' (' . $firstBrand->code . ')' : 'No brand assigned' }}</div>
                                </div>
                                <div class="kv-list-col">
                                    <small class="kv-list-col-label">Category</small>
                                    <div class="kv-list-col-value">{{ $firstCategory ? $firstCategory->name . ' (' . $firstCategory->code . ')' : 'No category assigned' }}</div>
                                </div>
                                <div class="kv-list-col">
                                    <small class="kv-list-col-label">Specification</small>
                                    <div class="kv-list-col-value">{{ $keyVisual->assetType?->name ?? 'No asset type' }}</div>
                                    @if($keyVisual->minimum_res_width || $keyVisual->minimum_res_height)
                                        <div class="text-muted fs-11 font-monospace mt-1">{{ $keyVisual->minimum_res_width ?? 0 }} x {{ $keyVisual->minimum_res_height ?? 0 }} px</div>
                                    @endif
                                </div>
                                <div class="kv-list-actions">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-view" data-id="{{ $keyVisual->id }}" title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $keyVisual->id }}" title="Edit">
                                        <i class="ri-edit-box-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $keyVisual->id }}" data-name="{{ $keyVisual->name }}" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5 border rounded-3 bg-light">No key visuals found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="text-center text-muted py-4 d-none" id="kv-no-results">
                    No key visuals match the current filter.
                </div>

                {{-- ── KV INLINE DETAIL VIEW ──────────────────────────── --}}
                <div class="d-none" id="kvDetailView">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0" style="font-size:1rem;"><i class="ri-image-line me-2 text-primary"></i>Visual Details</h6>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-back-to-grid">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Back to Grid
                        </button>
                    </div>
                    <div class="p-3" id="kvDetailContent">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modal')

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     MAIN KEY VISUAL FORM MODAL
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
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

                    {{--  SECTION: BASIC INFORMATION  --}}
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
                                <option value="">  Select  </option>
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

                    {{-- SECTION: CLASSIFICATION  --}}
                    <div class="kv-section-title mt-3">
                        <i class="ri-price-tag-3-line"></i><span>Classification</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label for="brand_ids" class="form-label fw-medium">Brand <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select kv-select2-brand select-ele" id="brand_ids" name="brand_ids[]"
                                    data-selected-brand-code="" multiple>
                                    <option disabled>  Select Brand  </option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" data-brand-code="{{ $brand->code }}">
                                            {{ $brand->name }} ({{ $brand->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary kv-open-brand-modal"
                                    title="Create new brand">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block" id="error-brand_ids"></div>
                        </div>
                        <div class="col-md-12">
                            <label for="category_ids" class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select class="form-select kv-select2-category select-ele" id="category_ids" name="category_ids[]"
                                    data-selected-category-code="" multiple>
                                    <option disabled><-- Select Category --></option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" data-category-code="{{ $category->code }}">
                                            {{ $category->name }} ({{ $category->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary kv-open-category-modal"
                                    title="Create new category">
                                    <i class="ri-add-line"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback d-block" id="error-category_ids"></div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end" id="unique_code-row" style="display:none!important;">
                            {{-- hidden placeholder for grid alignment; real code shown below --}}
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

                    {{-- SECTION: SPECIFICATIONS --}}
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

                    {{-- SECTION: MEDIA FILES  --}}
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
                                    <i class="ri-information-line me-1"></i>JPG / PNG / WEBP   max 5 MB   exact 1920  — 1080 px
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
                                    <i class="ri-information-line me-1"></i>JPG / PNG / WEBP   max 3 MB   auto-resized to 300  — 300
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

{{--  VIEW MODAL --}}
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

{{--      DELETE CONFIRMATION MODAL --}}
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

{{--  CREATE BRAND MODAL (child) --}}
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
                <input type="hidden" id="kv_brand_id" value="">
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

{{--      CREATE CATEGORY MODAL (child) --}}
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
                <input type="hidden" id="kv_category_id" value="">
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

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2-bootstrap-5-theme.min.css') }}">
<style>
/*  TABLE THUMB */
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
.kv-card-thumb .kv-thumb-empty {
    width: 100%;
    height: 100%;
    border-radius: 0;
    border: 0;
    font-size: 2rem;
}
.kv-code-badge { font-size: 0.75rem; letter-spacing: 0.04em; }
.btn-list { display: flex; gap: 4px; }

/* â”€â”€ MODAL */
.kv-view-menu .dropdown-item.active {
    background: rgba(var(--primary-rgb), 0.12);
    color: rgb(var(--primary-rgb));
    font-weight: 600;
}
#kvListRows {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.kv-list-row {
    display: grid;
    grid-template-columns: minmax(280px, 2.2fr) minmax(130px, 1.1fr) minmax(130px, 1.1fr) minmax(160px, 1.2fr) auto;
    gap: 0.75rem;
    align-items: center;
    padding: 0.9rem 1rem;
    border: 1px solid var(--default-border);
    border-radius: 12px;
    background: var(--custom-white);
    transition: .2s ease;
}
.kv-list-row:hover {
    border-color: rgba(var(--primary-rgb), .3);
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
}
.kv-list-main {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
}
.kv-list-thumb,
.kv-list-thumb-empty {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    border: 1px solid var(--default-border);
    object-fit: cover;
    flex-shrink: 0;
}
.kv-list-thumb-empty {
    background: var(--light);
    color: var(--text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-style: dashed;
    font-size: 1.2rem;
}
.kv-list-title {
    margin: 0 0 0.25rem;
    font-size: 0.9rem;
    line-height: 1.25;
    color: var(--default-text-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kv-list-sub {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    align-items: center;
}
.kv-list-col {
    min-width: 0;
}
.kv-list-col-label {
    display: block;
    font-size: 0.66rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 0.18rem;
}
.kv-list-col-value {
    font-size: 0.82rem;
    line-height: 1.3;
    color: var(--default-text-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kv-list-actions {
    /*display: inline-flex;*/
    align-items: center;
    gap: 0.35rem;
    padding: 3px;
}
.kv-list-actions .btn {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}
@media (max-width: 1199.98px) {
    .kv-list-row {
        grid-template-columns: minmax(260px, 2fr) minmax(120px, 1fr) minmax(120px, 1fr);
    }
    .kv-list-actions {
        grid-column: 1 / -1;
    }
}
@media (max-width: 767.98px) {
    .kv-list-row {
        grid-template-columns: 1fr;
        padding: 0.8rem;
    }
    .kv-list-actions {
        justify-content: flex-start;
    }
}

.kv-modal-header {
    background: linear-gradient(135deg, rgba(var(--primary-rgb),.06) 0%, transparent 100%);
    border-bottom: 1px solid var(--default-border);
}
.kv-modal-footer {
    background: var(--light);
    border-top: 1px solid var(--default-border);
}

/* â”€â”€ SECTION TITLES */
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

/* â”€â”€ KV TYPE SELECTOR (radio buttons styled as toggle) â”€â”€â”€â”€ */
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

/* â”€â”€ STATUS WRAPPER */
.kv-status-wrapper {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 1rem;
    background: var(--light);
    border-radius: 8px;
    border: 1px solid var(--default-border);
}
.kv-status-wrapper .ms-auto { margin-left: auto; }

/* â”€â”€ UNIQUE CODE */
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

/* â”€â”€ FILE UPLOAD AREAS */
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

/* â”€â”€ SELECT2 IN MODAL */
.select2-container { z-index: 99999 !important; width: 100% !important; flex: 1; }
.input-group .select2-container .select2-selection {
    border-radius: 0.375rem 0 0 0.375rem !important;
    border-right: 0 !important;
}

/* â”€â”€ FILEPOND */
.filepond--root { margin-bottom: 0; }
.filepond--panel-root { border-radius: 8px !important; }

/* â”€â”€ VIEW MODAL THUMB */
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

/* ── PAGE HEADER ──────────────────────────────────────────── */
.page-title { font-size: 1.5rem; font-weight: 700; color: var(--default-text-color); }
.page-subtitle { color: var(--text-muted); font-size: 0.9rem; }

/* ── TABS ─────────────────────────────────────────────────── */
.nav-tabs-custom .nav-link {
    border: none; color: var(--text-muted); font-size: 0.9rem;
    padding: 0.6rem 1rem; border-bottom: 2px solid transparent;
}
.nav-tabs-custom .nav-link.active {
    color: rgb(var(--warning-rgb)); border-bottom-color: rgb(var(--warning-rgb));
    background: transparent; font-weight: 600;
}
.nav-tabs-custom .nav-link i { font-size: 0.85rem; }

/* ── STAT CARDS ───────────────────────────────────────────── */
.stat-card {
    background: var(--custom-white); border: 1px solid var(--default-border);
    border-radius: 10px; padding: 1.1rem 1.25rem;
    display: flex; align-items: center; gap: 0.85rem;
}
.stat-card .stat-icon {
    width: 40px; height: 40px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
    flex-shrink: 0;
}
.stat-card .stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.1; }
.stat-card .stat-label { font-size: 0.8rem; color: var(--text-muted); }

/* ── CONTENT CARD ─────────────────────────────────────────── */
.content-card {
    background: var(--custom-white); border: 1px solid var(--default-border);
    border-radius: 10px;
}

/* ── BTN ACTION ───────────────────────────────────────────── */
.btn-action {
    width: 32px; height: 32px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; background: transparent; color: var(--text-muted);
    border-radius: 6px; cursor: pointer;
}
.btn-action:hover { color: var(--default-text-color); background: var(--light); }

/* ── KV TOGGLE BUTTONS ────────────────────────────────────── */
.kv-toggle-btn {
    font-size: 0.85rem; border-radius: 20px; padding: 0.35rem 1rem;
    border: 1px solid var(--default-border); color: var(--text-muted);
    background: var(--custom-white); transition: all .15s;
}
.kv-toggle-active { background: rgb(var(--warning-rgb)); color: #fff; border-color: rgb(var(--warning-rgb)); }
.kv-toggle-active:hover { opacity: 0.9; color: #fff; }
.kv-toggle-inactive:hover { background: var(--light); }

/* ── KV SECTION ICON ──────────────────────────────────────── */
.kv-section-icon {
    width: 36px; height: 36px; background: var(--light); border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 1rem; flex-shrink: 0;
}

/* ── KV SIDEBAR BRAND/CATEGORY LIST ──────────────────────── */
.kv-list-scroll { max-height: 420px; overflow-y: auto; }
.kv-brand-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 1rem; border-bottom: 1px solid var(--default-border);
}
.kv-brand-item:last-child { border-bottom: none; }
.kv-brand-item:hover { background: var(--light); }
.kv-brand-name { font-weight: 600; font-size: 0.9rem; color: var(--default-text-color); }
.kv-brand-code {
    display: inline-block; background: rgba(var(--primary-rgb), .1);
    color: rgb(var(--primary-rgb)); font-size: 0.7rem;
    padding: 1px 6px; border-radius: 4px; font-weight: 600;
    margin-left: 6px; vertical-align: middle;
}
.kv-brand-desc { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
.kv-brand-actions { display: flex; gap: 2px; flex-shrink: 0; }

/* ── KV GRID CARDS ────────────────────────────────────────── */
.kv-card {
    background: var(--custom-white); border: 1px solid var(--default-border);
    border-radius: 10px; overflow: hidden;
    transition: box-shadow .2s, transform .15s; cursor: pointer;
}
.kv-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.09); transform: translateY(-2px); }
.kv-card-thumb {
    position: relative; height: 160px; overflow: hidden; background: var(--light);
}
.kv-card-thumb img { width: 100%; height: 100%; object-fit: cover; }
.kv-card-type-badge {
    position: absolute; top: 8px; right: 8px;
    font-size: 0.68rem; font-weight: 600; padding: 2px 8px; border-radius: 4px;
}
.kv-type-image { background: rgba(46,125,50,.85); color: #fff; }
.kv-type-video { background: rgba(230,126,34,.9); color: #fff; }
.kv-card-expand {
    position: absolute; bottom: 8px; right: 8px;
    width: 28px; height: 28px; border-radius: 6px;
    background: rgba(255,255,255,.85); border: none; color: #495057;
    font-size: 0.75rem; display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .2s; cursor: pointer;
}
.kv-card:hover .kv-card-expand { opacity: 1; }
.kv-card-body { padding: 0.75rem; }
.kv-card-title {
    font-weight: 600; font-size: 0.85rem; color: var(--default-text-color);
    margin-bottom: 0.35rem; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.kv-card-id {
    display: inline-block; background: rgba(var(--primary-rgb), .1);
    color: rgb(var(--primary-rgb)); font-size: 0.68rem;
    padding: 1px 6px; border-radius: 4px; font-weight: 600;
}
.kv-card-status-active { font-size: 0.72rem; color: #2e7d32; font-weight: 500; }
.kv-card-meta { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

/* ── KV INLINE DETAIL VIEW ────────────────────────────────── */
.kv-detail-preview { text-align: center; background: var(--light); border-radius: 10px; padding: 1rem; margin-bottom: 1rem; }
.kv-detail-preview img { max-height: 320px; border-radius: 8px; object-fit: contain; max-width: 100%; }
.kv-detail-no-thumb {
    height: 160px; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 2.5rem;
}
.kv-detail-section-title {
    font-weight: 700; font-size: 0.88rem; color: var(--default-text-color);
    padding-bottom: 0.4rem; border-bottom: 1px solid var(--default-border); margin-bottom: 0.5rem;
}
.kv-detail-table { font-size: 0.85rem; }
.kv-detail-table td { padding: 0.35rem 0.5rem; border-color: var(--default-border); vertical-align: middle; }
.kv-detail-label { color: var(--text-muted); width: 130px; white-space: nowrap; }

/* ── KV BRAND CARD (sidebar) ──────────────────────────────── */
.kv-brand-card { min-height: 200px; }

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media (max-width: 991.98px) {
    .kv-card-thumb { height: 140px; }
    .stat-card .stat-value { font-size: 1.25rem; }
    .stat-card { padding: 0.85rem 1rem; }
}
@media (max-width: 767.98px) {
    .kv-card-thumb { height: 130px; }
    .page-header-actions { margin-top: 0.75rem; }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
{{--<script src="{{ asset('backend/build/select2-4.1.0/select2.min.js') }}"></script>--}}
@include('backend.includes.plugins.select2')
<script>
$(function () {
    // â”€â”€ CONSTANTS
    const BASE          = base_url;
    const apiUrl        = (id) => BASE + 'key-visuals' + (id ? '/' + id : '');
    const NEXT_CODE_URL = @json(route('key-visuals.next-unique-code'));
    const BRAND_URL     = BASE + 'brands';
    const CAT_URL       = BASE + 'categories';
    const brandEditUrl  = (id) => `${BRAND_URL}/${id}/edit`;
    const catEditUrl    = (id) => `${CAT_URL}/${id}/edit`;

    // â”€â”€ MODAL INSTANCES
    const kvModal      = new bootstrap.Modal('#keyVisualModal');
    const viewModalEl  = new bootstrap.Modal('#viewModal');
    const deleteModal  = new bootstrap.Modal('#deleteModal');
    const brandModal   = new bootstrap.Modal('#brandModal');
    const catModal     = new bootstrap.Modal('#categoryModal');
    let restoreKvModal = false;

    // â”€â”€ UNIQUE CODE REQUEST HANDLE
    let codeRequest = null;
    let cardStatusFilter = 'all';
    let currentView = 'grid';

    // â”€â”€ FILE UPLOAD MODES
    const FILE_MODES = {
        image: {
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
            accept: 'image/jpeg,image/png,image/webp',
            maxFileSize: '5MB',
            labelIdle: '<i class="ri-image-2-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop image or <span class="filepond--label-action">browse</span></span>',
            helpText: 'JPG / PNG / WEBP   max 5 MB   exact 1920  — 1080 px',
            tagLabel: '<i class="ri-image-2-line"></i> Image',
            tagClass: '',
        },
        video: {
            acceptedFileTypes: ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm'],
            accept: 'video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm',
            maxFileSize: '30MB',
            labelIdle: '<i class="ri-video-line" style="font-size:1.5rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop video or <span class="filepond--label-action">browse</span></span>',
            helpText: 'MP4 / MOV / AVI / MKV / WEBM   max 30 MB',
            tagLabel: '<i class="ri-video-line"></i> Video',
            tagClass: 'is-video',
        },
    };

    // FILEPOND
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

    //  SELECT2
    const s2Opts = (placeholder, parent) => ({
        placeholder, allowClear: true, theme: 'bootstrap-5',
        dropdownParent: $(parent), width: '100%',
    });

    // $('#brand_ids').select2(s2Opts('  Select Brand  ', '#keyVisualModal'));
    // $('#category_ids').select2(s2Opts('  Select Category  ', '#keyVisualModal'));

    // â”€â”€ HELPER FUNCTIONS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

    // UNIQUE CODE
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

    // FORM MANAGEMENT
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
        // Unique code (editing   already generated)
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

    // UPSERT SELECT OPTIONS AFTER CHILD MODAL SAVES â”€â”€â”€â”€
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

    function truncateWords(str, n) {
        if (!str) return '';
        const words = String(str).trim().split(/\s+/);
        return words.slice(0, n).join(' ');
    }

    function buildBrandPaneItem(brand) {
        const id   = String(brand.id);
        const name = brand.name || '';
        const code = String(brand.code || '').toUpperCase();
        const desc = truncateWords(brand.description, 10);
        const $item = $('<div class="kv-brand-item">').attr('data-brand-id', id);
        const $info = $('<div>');
        $info.append($('<span class="kv-brand-name">').text(name));
        $info.append($('<span class="kv-brand-code">').text(code));
        $info.append($('<div class="kv-brand-desc">').text(desc));
        const $actions = $('<div class="kv-brand-actions">');
        $actions.append(
            $('<button type="button" class="btn-action kv-sidebar-brand-edit" title="Edit Brand">')
                .attr('data-id', id).html('<i class="bi bi-pencil"></i>')
        );
        $actions.append(
            $('<button type="button" class="btn-action text-danger kv-sidebar-brand-delete" title="Delete Brand">')
                .attr('data-id', id).attr('data-name', name).html('<i class="bi bi-trash"></i>')
        );
        return $item.append($info).append($actions);
    }

    function upsertBrandPaneItem(brand) {
        if (!brand?.id) return;
        const $scroll   = $('#kvBrandsPane .kv-list-scroll');
        const $existing = $scroll.find(`[data-brand-id="${brand.id}"]`);
        const $newItem  = buildBrandPaneItem(brand);
        if ($existing.length) {
            $existing.replaceWith($newItem);
        } else {
            $('#brands-empty-state').remove();
            $scroll.append($newItem);
            const $stat = $('#stat-brands-count');
            $stat.text(parseInt($stat.text(), 10) + 1);
        }
    }

    function removeBrandPaneItem(id) {
        const $scroll = $('#kvBrandsPane .kv-list-scroll');
        if ($scroll.find(`[data-brand-id="${id}"]`).length) {
            $scroll.find(`[data-brand-id="${id}"]`).remove();
            const $stat = $('#stat-brands-count');
            $stat.text(Math.max(0, parseInt($stat.text(), 10) - 1));
        }
        if ($scroll.find('.kv-brand-item').length === 0) {
            $scroll.append('<div class="p-3 text-muted small" id="brands-empty-state">No brands found.</div>');
        }
    }

    function buildCategoryPaneItem(category) {
        const id   = String(category.id);
        const name = category.name || '';
        const code = String(category.code || '').toUpperCase();
        const desc = truncateWords(category.description, 10);
        const $item = $('<div class="kv-brand-item">').attr('data-category-id', id);
        const $info = $('<div>');
        $info.append($('<span class="kv-brand-name">').text(name));
        $info.append($('<span class="kv-brand-code">').text(code));
        $info.append($('<div class="kv-brand-desc">').text(desc));
        const $actions = $('<div class="kv-brand-actions">');
        $actions.append(
            $('<button type="button" class="btn-action kv-sidebar-category-edit" title="Edit Category">')
                .attr('data-id', id).html('<i class="bi bi-pencil"></i>')
        );
        $actions.append(
            $('<button type="button" class="btn-action text-danger kv-sidebar-category-delete" title="Delete Category">')
                .attr('data-id', id).attr('data-name', name).html('<i class="bi bi-trash"></i>')
        );
        return $item.append($info).append($actions);
    }

    function upsertCategoryPaneItem(category) {
        if (!category?.id) return;
        const $scroll   = $('#kvCategoriesPane .kv-list-scroll');
        const $existing = $scroll.find(`[data-category-id="${category.id}"]`);
        const $newItem  = buildCategoryPaneItem(category);
        if ($existing.length) {
            $existing.replaceWith($newItem);
        } else {
            $('#categories-empty-state').remove();
            $scroll.append($newItem);
            const $stat = $('#stat-categories-count');
            $stat.text(parseInt($stat.text(), 10) + 1);
        }
    }

    function removeCategoryPaneItem(id) {
        const $scroll = $('#kvCategoriesPane .kv-list-scroll');
        if ($scroll.find(`[data-category-id="${id}"]`).length) {
            $scroll.find(`[data-category-id="${id}"]`).remove();
            const $stat = $('#stat-categories-count');
            $stat.text(Math.max(0, parseInt($stat.text(), 10) - 1));
        }
        if ($scroll.find('.kv-brand-item').length === 0) {
            $scroll.append('<div class="p-3 text-muted small" id="categories-empty-state">No categories found.</div>');
        }
    }

    function setBrandLogoInPond(logoPath) {
        brandLogoPond.removeFiles();
        if (!logoPath) return;

        const raw = String(logoPath);
        const logoUrl = /^(https?:)?\/\//i.test(raw) ? raw : (BASE + raw.replace(/^\/+/, ''));
        brandLogoPond.addFile(logoUrl).catch(() => {});
    }

    // CHILD MODAL FLOW
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

    function setViewMode(mode = 'grid') {
        currentView = mode === 'list' ? 'list' : 'grid';

        $('#kvGridView').toggleClass('d-none', currentView !== 'grid');
        $('#kvListView').toggleClass('d-none', currentView !== 'list');
        $('#kvViewToggleBtn').text(currentView === 'grid' ? 'Grid View' : 'List View');

        $('.kv-view-option').removeClass('active');
        $(`.kv-view-option[data-view="${currentView}"]`).addClass('active');

        applyCardFilters();
    }

    function applyCardFilters() {
        const keyword = String($('#kv-search-input').val() || '').toLowerCase().trim();
        let gridVisibleCount = 0;
        let listVisibleCount = 0;

        const shouldShow = function (status, search) {
            const statusOk = cardStatusFilter === 'all' ? true : status === cardStatusFilter;
            const searchOk = !keyword || search.includes(keyword);
            return statusOk && searchOk;
        };

        $('#kvCardsGrid .kv-card-col').each(function () {
            const $card = $(this);
            const status = String($card.data('status'));
            const search = String($card.data('search') || '').toLowerCase();
            const show = shouldShow(status, search);

            $card.toggleClass('d-none', !show);
            if (show) gridVisibleCount++;
        });

        $('#kvListRows .kv-list-row').each(function () {
            const $row = $(this);
            const status = String($row.data('status'));
            const search = String($row.data('search') || '').toLowerCase();
            const show = shouldShow(status, search);

            $row.toggleClass('d-none', !show);
            if (show) listVisibleCount++;
        });

        const totalGridCards = $('#kvCardsGrid .kv-card-col').length;
        const totalListRows = $('#kvListRows .kv-list-row').length;
        const activeVisibleCount = currentView === 'grid' ? gridVisibleCount : listVisibleCount;
        const activeTotalCount = currentView === 'grid' ? totalGridCards : totalListRows;

        $('#kv-no-results').toggleClass('d-none', activeVisibleCount > 0 || activeTotalCount === 0);
    }

    // EVENT: SELECT2 BRAND / CATEGORY CHANGE
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

    // EVENT: KV TYPE RADIO CHANGE
    $('input[name="kv_type"]').on('change', function () {
        updateSampleUploader($(this).val(), true);
        // Update resolution hint for image type
        const isImage = $(this).val() === 'image';
        $('#minimum_res_width').attr('placeholder',  isImage ? '1920' : 'e.g. 3840');
        $('#minimum_res_height').attr('placeholder', isImage ? '1080' : 'e.g. 2160');
    });

    // â”€â”€ EVENT: STATUS SWITCH
    $('#status').on('change', function () {
        const on = $(this).is(':checked');
        $('#status-label').text(on ? 'Active' : 'Inactive');
        $('#status-sub-label').text(on ? 'This KV will be visible' : 'This KV is hidden');
    });

    // â”€â”€ EVENT: CODE FIELD OVERRIDE (click to unlock)
    $('#kv_brand_code').on('click', function () { $(this).removeAttr('readonly'); });
    $('#kv_category_code').on('click', function () { $(this).removeAttr('readonly'); });

    // â”€â”€ EVENT: BRAND / CATEGORY CODE AUTO-GEN
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

    // â”€â”€ EVENT: STATUS SWITCHES (child modals)
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

    // â”€â”€ EVENT: OPEN CHILD MODALS
    $(document).on('click', '.kv-open-brand-modal',    (e) => { e.preventDefault(); openChildModal(brandModal); });
    $(document).on('click', '.kv-open-category-modal', (e) => { e.preventDefault(); openChildModal(catModal); });

    $(document).on('click', '.kv-sidebar-brand-edit', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (!id) return;

        openChildModal(brandModal);
        $.get(brandEditUrl(id), { json: 1 })
            .done(function (data) {
                const applyBrandData = function () {
                    $('#kv_brand_id').val(data.id || '');
                    $('#kv_brand_name').val(data.name || '');
                    $('#kv_brand_code').val(String(data.code || '').toUpperCase()).attr('readonly', true);
                    $('#kv_brand_description').val(data.description || '');
                    setBrandLogoInPond(data.logo || '');
                    $('#kv_brand_status_switch')
                        .prop('checked', Number(data.status) === 1 || String(data.status) === '1')
                        .trigger('change');

                    $('#brandModalLabel').html('<i class="ri-edit-box-line me-2 text-primary"></i>Edit Brand');
                    $('#kv-brand-btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Update Brand');
                };

                const $brandModal = $('#brandModal');
                if ($brandModal.hasClass('show')) {
                    applyBrandData();
                } else {
                    $brandModal
                        .off('shown.bs.modal.kvBrandEdit')
                        .one('shown.bs.modal.kvBrandEdit', applyBrandData);
                }
            })
            .fail(function () {
                showToast('Failed to load brand data.', 'danger');
            });
    });

    $(document).on('click', '.kv-sidebar-category-edit', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (!id) return;

        openChildModal(catModal);
        $.get(catEditUrl(id))
            .done(function (data) {
                $('#kv_category_id').val(data.id || '');
                $('#kv_category_name').val(data.name || '');
                $('#kv_category_code').val(String(data.code || '').toUpperCase()).attr('readonly', true);
                $('#kv_category_description').val(data.description || '');
                $('#kv_category_status_switch')
                    .prop('checked', Number(data.status) === 1 || String(data.status) === '1')
                    .trigger('change');

                $('#categoryModalLabel').html('<i class="ri-edit-box-line me-2 text-primary"></i>Edit Category');
                $('#kv-category-btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Update Category');
            })
            .fail(function () {
                showToast('Failed to load category data.', 'danger');
            });
    });

    $(document).on('click', '.kv-sidebar-brand-delete', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name') || 'this brand';
        if (!id) return;
        if (!window.confirm(`Delete brand "${name}"?`)) return;

        $.ajax({
            url: `${BRAND_URL}/${id}`,
            type: 'DELETE',
            success: function (res) {
                removeBrandPaneItem(id);
                showToast(res.message || 'Brand deleted successfully.', 'success');
            },
            error: function () {
                showToast('Failed to delete brand.', 'danger');
            },
        });
    });

    $(document).on('click', '.kv-sidebar-category-delete', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name') || 'this category';
        if (!id) return;
        if (!window.confirm(`Delete category "${name}"?`)) return;

        $.ajax({
            url: `${CAT_URL}/${id}`,
            type: 'DELETE',
            success: function (res) {
                removeCategoryPaneItem(id);
                showToast(res.message || 'Category deleted successfully.', 'success');
            },
            error: function () {
                showToast('Failed to delete category.', 'danger');
            },
        });
    });

    // Reset child forms when their modals open
    $('#brandModal').on('show.bs.modal', function () {
        $('#kvBrandForm')[0].reset();
        $('#kv_brand_id').val('');
        $('#kv_brand_code').val('').attr('readonly', true);
        brandLogoPond.removeFiles();
        $('#kv_brand_status_switch').prop('checked', true).trigger('change');
        $('#brandModalLabel').html('<i class="ri-building-line me-2 text-primary"></i>Add Brand');
        $('#kv-brand-btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Save Brand');
        $('#kvBrandForm .is-invalid').removeClass('is-invalid');
        $('#kvBrandForm .invalid-feedback').text('');
    });
    $('#categoryModal').on('show.bs.modal', function () {
        $('#kvCategoryForm')[0].reset();
        $('#kv_category_id').val('');
        $('#kv_category_code').val('').attr('readonly', true);
        $('#kv_category_status_switch').prop('checked', true).trigger('change');
        $('#categoryModalLabel').html('<i class="ri-list-check-3 me-2 text-primary"></i>Add Category');
        $('#kv-category-btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Save Category');
        $('#kvCategoryForm .is-invalid').removeClass('is-invalid');
        $('#kvCategoryForm .invalid-feedback').text('');
    });

    // â”€â”€ EVENT: ADD BUTTON
    $(document).on('click', '#btn-add-key-visual, .btn-add-key-visual', function (e) {
        e.preventDefault();
        openFormModal('add');
        kvModal.show();
    });

    $(document).on('click', '.kv-view-option', function (e) {
        e.preventDefault();
        setViewMode($(this).data('view'));
    });

    $('#btnActiveToggle').on('click', function () {
        cardStatusFilter = cardStatusFilter === '1' ? 'all' : '1';
        applyCardFilters();
        $(this).removeClass('kv-toggle-inactive').addClass('kv-toggle-active');
        $('#btnArchivedToggle').removeClass('kv-toggle-active').addClass('kv-toggle-inactive');
    });

    $('#btnArchivedToggle').on('click', function () {
        cardStatusFilter = cardStatusFilter === '0' ? 'all' : '0';
        applyCardFilters();
        $(this).removeClass('kv-toggle-inactive').addClass('kv-toggle-active');
        $('#btnActiveToggle').removeClass('kv-toggle-active').addClass('kv-toggle-inactive');
    });

    $('#kv-search-input').on('input', applyCardFilters);
    setViewMode('grid');

    // â”€â”€ EVENT: EDIT BUTTON
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

    // â”€â”€ EVENT: VIEW BUTTON
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $.get(apiUrl(id))
            .done(function (data) {
                $('#view-name').text(data.name || ' ');
                $('#view-unique-code').html(`<span class="badge bg-primary-transparent font-monospace">${data.unique_code || ' '}</span>`);
                $('#view-asset-type').text(data.asset_type?.name ?? ' ');
                $('#view-kv-type').html(`<span class="badge ${data.kv_type === 'video' ? 'bg-info-transparent' : 'bg-warning-transparent'} text-uppercase">
                    <i class="ri-${data.kv_type === 'video' ? 'video' : 'image-2'}-line me-1"></i>${data.kv_type || 'image'}</span>`);
                const res = data.minimum_res_width || data.minimum_res_height;
                $('#view-resolution').html(res
                    ? `<span class="font-monospace">${data.minimum_res_width || 0}  — ${data.minimum_res_height || 0} px</span>`
                    : ' ');
                $('#view-sample-file').html(data.kv_sample_file
                    ? `<a href="${BASE + data.kv_sample_file}" target="_blank" rel="noopener"><i class="ri-external-link-line me-1"></i>Open file</a>`
                    : ' ');
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

    // â”€â”€ EVENT: DELETE BUTTON
    $(document).on('click', '.btn-delete', function () {
        $('#delete-kv-id').val($(this).data('id'));
        $('#delete-kv-name').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btn-confirm-delete').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');
        $.ajax({
            url: apiUrl($('#delete-kv-id').val()),
            type: 'DELETE',
            success: function (res) {
                deleteModal.hide();
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function () { showToast('Failed to delete key visual.', 'danger'); },
            complete: function () {
                $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
            },
        });
    });

    // â”€â”€ EVENT: MAIN FORM SUBMIT
    // ── INLINE DETAIL VIEW ────────────────────────────────────
    function showKvDetail(id) {
        $('#kvGridView, #kvListView, #kvGridSearch, #kv-no-results, #kvGridHeader').addClass('d-none');
        $('#kvDetailView').removeClass('d-none');
        $('#kvDetailContent').html(
            '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>'
        );

        $.get(apiUrl(id))
            .done(function (data) {
                const isVideo  = data.kv_type === 'video';
                const isActive = data.status == 1;
                const thumbSrc = data.kv_thumb ? (BASE + data.kv_thumb) : null;
                const sample   = data.kv_sample_file ? (BASE + data.kv_sample_file) : null;
                const resText  = (data.minimum_res_width || data.minimum_res_height)
                    ? `<span class="font-monospace">${data.minimum_res_width || 0} \u00d7 ${data.minimum_res_height || 0} px</span>`
                    : '\u2014';
                const statusBadge = isActive
                    ? '<span class="kv-card-status-active"><i class="bi bi-check-circle me-1"></i>Active</span>'
                    : '<span class="badge bg-danger-transparent"><i class="bi bi-x-circle me-1"></i>Inactive</span>';
                const typeBadge = `<span class="kv-card-type-badge ${isVideo ? 'kv-type-video' : 'kv-type-image'}" style="position:static"><i class="bi bi-${isVideo ? 'camera-video' : 'image'} me-1"></i>${(data.kv_type || 'image').toUpperCase()}</span>`;

                $('#kvDetailContent').html(`
                    <div class="kv-detail-preview">
                        ${thumbSrc
                            ? `<img src="${thumbSrc}" alt="${data.name || ''}">`
                            : `<div class="kv-detail-no-thumb"><i class="ri-image-line"></i><small class="d-block mt-1 fs-13">No thumbnail</small></div>`}
                    </div>
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <h6 class="kv-detail-section-title">Basic Information</h6>
                            <table class="table table-sm kv-detail-table">
                                <tr><td class="kv-detail-label">KV ID:</td><td><span class="kv-card-id">${data.unique_code || '\u2014'}</span></td></tr>
                                <tr><td class="kv-detail-label">Status:</td><td>${statusBadge}</td></tr>
                                <tr><td class="kv-detail-label">Title:</td><td class="fw-semibold">${data.name || '\u2014'}</td></tr>
                                <tr><td class="kv-detail-label">Asset Type:</td><td>${data.asset_type?.name ?? '\u2014'}</td></tr>
                            </table>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6 class="kv-detail-section-title">File Information</h6>
                            <table class="table table-sm kv-detail-table">
                                <tr><td class="kv-detail-label">KV Type:</td><td>${typeBadge}</td></tr>
                                <tr><td class="kv-detail-label">Resolution:</td><td>${resText}</td></tr>
                                <tr><td class="kv-detail-label">Sample File:</td><td>${sample
                                    ? `<a href="${sample}" target="_blank" rel="noopener"><i class="ri-external-link-line me-1"></i>Open file</a>`
                                    : '\u2014'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 pt-3 border-top justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="${data.id}">
                            <i class="ri-edit-box-line me-1"></i>Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="${data.id}" data-name="${(data.name || '').replace(/"/g, '&quot;')}">
                            <i class="ri-delete-bin-line me-1"></i>Delete
                        </button>
                    </div>
                `);
            })
            .fail(function () {
                showToast('Failed to load details.', 'danger');
                hideKvDetail();
            });
    }

    function hideKvDetail() {
        $('#kvDetailView').addClass('d-none');
        $('#kvDetailContent').html('');
        $('#kvGridHeader, #kvGridSearch').removeClass('d-none');
        setViewMode(currentView);
    }

    // Card click (not on action buttons) → inline detail
    $(document).on('click', '.kv-card', function (e) {
        if (!$(e.target).closest('.btn-edit, .btn-delete').length) {
            const id = $(this).find('.kv-card-expand').data('id');
            if (id) showKvDetail(id);
        }
    });

    // Expand button click → inline detail (stop card click from also firing)
    $(document).on('click', '.kv-card-expand', function (e) {
        e.stopPropagation();
        showKvDetail($(this).data('id'));
    });

    // Back to grid button
    $(document).on('click', '#btn-back-to-grid', hideKvDetail);

    // ── EVENT: MAIN FORM SUBMIT ───────────────────────────────
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

        $('#btn-save').prop('disabled', true);
        $('#btn-spinner').removeClass('d-none');

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
                        const msg = messages[0];
                        if (field === 'kv_sample_file') {
                            $('#kv_sample_file').closest('.filepond--root').addClass('is-invalid');
                        } else if (field === 'kv_thumb') {
                            $('#kv_thumb').closest('.filepond--root').addClass('is-invalid');
                        } else {
                            $('#' + field).addClass('is-invalid');
                        }
                        $('#error-' + field).text(msg);
                    });
                } else {
                    showToast(xhr.responseJSON?.message || 'Something went wrong. Please try again.', 'danger');
                }
            },
            complete: function () {
                $('#btn-save').prop('disabled', false);
                $('#btn-spinner').addClass('d-none');
            },
        });
    });

    // â”€â”€ EVENT: BRAND FORM SUBMIT
    $('#kvBrandForm').on('submit', function (e) {
        e.preventDefault();
        $('#kvBrandForm .is-invalid').removeClass('is-invalid');
        $('#kvBrandForm .invalid-feedback').text('');

        const id = String($('#kv_brand_id').val() || '').trim();
        const formData = new FormData(this);
        formData.set('status', $('#kv_brand_status').val());
        formData.delete('logo');
        const logoFile = brandLogoPond.getFile();
        if (logoFile?.file) formData.append('logo', logoFile.file);
        if (id) formData.append('_method', 'PUT');

        $('#kv-brand-btn-save').prop('disabled', true);
        $('#kv-brand-btn-spinner').removeClass('d-none');

        $.ajax({
            url: id ? `${BRAND_URL}/${id}` : BRAND_URL, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (res) {
                brandModal.hide();
                upsertBrandOption(res.data || null);
                upsertBrandPaneItem(res.data || null);
                showToast(res.message || (id ? 'Brand updated successfully.' : 'Brand created successfully.'), 'success');
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
                    showToast('Failed to save brand.', 'danger');
                }
            },
            complete: function () {
                $('#kv-brand-btn-save').prop('disabled', false);
                $('#kv-brand-btn-spinner').addClass('d-none');
            },
        });
    });

    // â”€â”€ EVENT: CATEGORY FORM SUBMIT
    $('#kvCategoryForm').on('submit', function (e) {
        e.preventDefault();
        $('#kvCategoryForm .is-invalid').removeClass('is-invalid');
        $('#kvCategoryForm .invalid-feedback').text('');

        const id = String($('#kv_category_id').val() || '').trim();
        const formData = new FormData(this);
        formData.set('status', $('#kv_category_status').val());
        if (id) formData.append('_method', 'PUT');

        $('#kv-category-btn-save').prop('disabled', true);
        $('#kv-category-btn-spinner').removeClass('d-none');

        $.ajax({
            url: id ? `${CAT_URL}/${id}` : CAT_URL, type: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (res) {
                catModal.hide();
                upsertCategoryOption(res.data || null);
                upsertCategoryPaneItem(res.data || null);
                showToast(res.message || (id ? 'Category updated successfully.' : 'Category created successfully.'), 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const map = { name: '#kv_category_name', code: '#kv_category_code', description: '#kv_category_description' };
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        if (map[field]) $(map[field]).addClass('is-invalid');
                        $(`#error-kv-category-${field}`).text(messages[0]);
                    });
                } else {
                    showToast('Failed to save category.', 'danger');
                }
            },
            complete: function () {
                $('#kv-category-btn-save').prop('disabled', false);
                $('#kv-category-btn-spinner').addClass('d-none');
            },
        });
    });

}); // end document.ready
</script>
@endpush
