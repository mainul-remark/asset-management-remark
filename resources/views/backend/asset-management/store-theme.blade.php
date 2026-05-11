@extends('backend.master')

@section('title', 'Stores List')

@section('body')

    @php
        $totalStores = $stores->count();
        $withLayouts = $stores->filter(fn($s) => $s->storeLayouts && $s->storeLayouts->count() > 0)->count();
        $avgRentPerSqft = $stores->where('total_area_sqft', '>', 0)->avg(fn($s) => $s->monthly_rent / $s->total_area_sqft);
        $totalLocations = $stores->whereNotNull('division_id')->pluck('division_id')->unique()->count();
        $activeStores = $stores->where('status', 1)->count();
        $storeLocatorData = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'title' => $store->title,
                'code' => $store->code,
                'address' => $store->address,
                'division' => optional($store->division)->name,
                'status' => (int) $store->status,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
            ];
        })->values();
    @endphp

    <!-- Main Content -->
    <div class="container px-3 px-lg-4 py-3">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active"><i class="bi bi-shop me-1"></i>Store Management</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
            <div>
                <h1 class="page-title mb-1">Store Management</h1>
                <p class="page-subtitle mb-0">Manage store information, layouts, and calculate branding costs across all locations</p>
            </div>
            <div class="page-header-actions d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#storeLocator"><i class="bi bi-map me-1"></i>View Stores</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="exportStoreData"><i class="bi bi-download me-1"></i>Export Data</button>
                <button class="btn btn-primary btn-sm" id="btn-add-store"><i class="bi bi-plus me-1"></i>Add Store</button>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs-custom nav-tabs border-bottom mb-3" id="storeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="stores-tab" data-bs-toggle="tab" data-bs-target="#storesPane" type="button" role="tab">
                    <i class="bi bi-shop me-1"></i>Stores
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="layouts-tab" data-bs-toggle="tab" data-bs-target="#layoutsPane" type="button" role="tab">
                    <i class="bi bi-grid-1x2 me-1"></i>Layouts
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="storeTabContent">

            <!-- ========== STORES TAB ========== -->
            <div class="tab-pane fade show active" id="storesPane" role="tabpanel">

                <!-- Stat Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#eef1f6;color:#2c3e6b;"><i class="bi bi-shop"></i></div>
                            <div>
                                <div class="stat-value">{{ $totalStores }}</div>
                                <div class="stat-label">Total Stores</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check-circle"></i></div>
                            <div>
                                <div class="stat-value">{{ $activeStores }}</div>
                                <div class="stat-label">Active Stores</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-graph-up"></i></div>
                            <div>
                                <div class="stat-value">{{ $avgRentPerSqft ? number_format($avgRentPerSqft, 0) . '৳' : '—' }}</div>
                                <div class="stat-label">Avg Rent/Sq Ft</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:#ede7f6;color:#5e35b1;"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <div class="stat-value">{{ $totalLocations }}</div>
                                <div class="stat-label">Locations</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Store Directory Card -->
                <div class="content-card">
                    <div class="card-header-area">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h5 class="mb-1">Store Directory</h5>
                                <p class="mb-0">Manage store information and calculate branding costs</p>
                            </div>
                            <span class="text-muted store-count-label" style="font-size:0.85rem;">{{ $totalStores }} of {{ $totalStores }} stores</span>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="px-3 pb-3">
                        <div class="row g-2 filter-row">
                            <div class="col-12 col-sm-6 col-md-3">
                                <input type="text" class="form-control form-control-sm" id="filter-search" placeholder="Search stores...">
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-select form-select-sm" id="filter-division">
                                    <option value="">Filter by location</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->name }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <select class="form-select form-select-sm" id="filter-size">
                                    <option value="">Filter by size</option>
                                    <option value="small">&lt; 500 sq ft</option>
                                    <option value="medium">500 - 1000 sq ft</option>
                                    <option value="large">&gt; 1000 sq ft</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <select class="form-select form-select-sm" id="filter-rent">
                                    <option value="">Filter by rent</option>
                                    <option value="low">&lt; 50,000৳</option>
                                    <option value="mid">50,000 - 80,000৳</option>
                                    <option value="high">&gt; 80,000৳</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table store-table mb-0" id="stores-table">
                            <thead>
                            <tr>
                                <th>Store Name <i class="bi bi-arrow-down-up" style="font-size:0.65rem;"></i></th>
                                <th>Location <i class="bi bi-arrow-down-up" style="font-size:0.65rem;"></i></th>
                                <th>Store Type</th>
                                <th>Size (sq ft) <i class="bi bi-arrow-down-up" style="font-size:0.65rem;"></i></th>
                                <th>Monthly Rent <i class="bi bi-arrow-down-up" style="font-size:0.65rem;"></i></th>
                                <th>Rent/sq ft <i class="bi bi-arrow-down-up" style="font-size:0.65rem;"></i></th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $storeTypeLabels = [
                                    'join_venture' => 'Join Venture',
                                    'partner'      => 'Partner',
                                    'zone'         => 'Zone',
                                    'zone_jv'      => 'JV + Zone',
                                ];
                            @endphp
                            @foreach($stores as $store)
                                @php
                                    $rentPerSqft = ($store->total_area_sqft > 0 && $store->monthly_rent > 0)
                                        ? number_format($store->monthly_rent / $store->total_area_sqft, 2)
                                        : '—';
                                @endphp
                                <tr data-size="{{ $store->total_area_sqft }}" data-rent="{{ $store->monthly_rent }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="store-icon"><i class="bi bi-shop"></i></span>
                                            <div>
                                                <div class="store-name">{{ $store->title }}</div>
                                                <div class="store-id">ID: {{ $store->code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-geo-alt text-muted me-1" style="font-size:0.75rem;"></i>
                                        {{ $store->division?->name ?? '—' }}
                                    </td>
                                    <td>{{ $storeTypeLabels[$store->store_type] ?? ($store->store_type ? ucwords(str_replace('_', ' ', $store->store_type)) : '—') }}</td>
                                    <td>{{ $store->total_area_sqft ? number_format($store->total_area_sqft) : '—' }}</td>
                                    <td>{{ $store->monthly_rent ? number_format($store->monthly_rent) . '৳' : '—' }}</td>
                                    <td class="text-warning">{{ $rentPerSqft !== '—' ? $rentPerSqft . '৳' : '—' }}</td>
                                    <td>
                                        @if($store->status == 1)
                                            <span class="badge badge-current rounded-pill"><i class="bi bi-check-circle me-1"></i>Active</span>
                                        @else
                                            <span class="badge badge-pending rounded-pill"><i class="bi bi-clock me-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="btn-action open-assign-asset-to-store-mdoal" data-id="{{ $store->id }}" title="Assigned Assets"><i class="bi bi-cassette"></i></a>
{{--                                        <a href="{{ route('assets.assign-assets') }}" class="btn-action open-assign-asset-to-store-mdoal" data-id="{{ $store->id }}" title="View"><i class="bi bi-check"></i></a>--}}
                                        <button class="btn-action btn-view" data-id="{{ $store->id }}" title="View"><i class="bi bi-eye"></i></button>
                                        <button class="btn-action btn-edit" data-id="{{ $store->id }}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                        <button class="btn-action text-danger btn-delete" data-id="{{ $store->id }}" data-name="{{ $store->title }}" title="Delete"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========== LAYOUTS TAB ========== -->
            <div class="tab-pane fade" id="layoutsPane" role="tabpanel">

                <!-- Search & Filter Bar -->
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <input type="text" class="form-control form-control-sm" id="layout-search" placeholder="Search stores..." style="max-width:280px;">
                    <select class="form-select form-select-sm" id="layout-division-filter" style="max-width:200px;">
                        <option value="">All Locations</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->name }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    <span class="ms-auto text-muted" style="font-size:0.85rem;" id="layout-store-count"></span>
                </div>

                <div class="row g-3">
                    <!-- Left Sidebar - Store List -->
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="content-card layout-sidebar">
                            <div class="p-3 border-bottom">
                                <h6 class="fw-bold mb-1">Store Layouts</h6>
                                <small class="text-muted">Select a store to view and manage its layouts</small>
                            </div>
                            <div class="store-list" id="layout-store-list" style="max-height:520px; overflow-y:auto;">
                                {{-- Loaded via AJAX --}}
                            </div>
                            <div class="text-center py-2 d-none" id="layout-load-spinner">
                                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                <small class="text-muted ms-1">Loading...</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Content - Layout Detail -->
                    <div class="col-12 col-md-7 col-lg-8">
                        <div class="content-card p-3 p-lg-4">
                            <!-- Header -->
                            <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                                <div class="layout-detail-header">
                                    <h5 class="mb-1" id="layout-store-name">No Store Selected</h5>
                                    <p class="text-muted mb-0" style="font-size:0.85rem;" id="layout-store-address">Select a store from the sidebar</p>
                                </div>
                                <button class="btn btn-primary btn-sm <!--text-white--> mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#newVersionModal">
                                    <i class="bi bi-upload me-1"></i>Upload New Version
                                </button>
                            </div>

                            <!-- Layout Tabs -->
                            <ul class="nav nav-tabs-custom nav-tabs border-bottom mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#currentLayoutPane" type="button">
                                        <i class="bi bi-eye me-1"></i>Current Layout
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#versionHistoryPane" type="button">
                                        <i class="bi bi-clock-history me-1"></i>Version History
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Current Layout -->
                                <div class="tab-pane fade show active" id="currentLayoutPane">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">Current Layout</h6>
                                        <a href="#" target="_blank" class="btn btn-outline-secondary btn-sm d-none" id="layout-download-btn"><i class="bi bi-download me-1"></i>Download</a>
                                    </div>

                                    <!-- Preview placeholder / PDF embed -->
                                    <div class="layout-preview-placeholder" id="layout-placeholder">
                                        <i class="bi bi-image d-block mb-2"></i>
                                        <div class="fw-semibold" style="font-size:0.9rem;">Layout Preview</div>
                                        <small>Select a store to view its layout</small>
                                    </div>
                                    <iframe id="layout-pdf-viewer" class="d-none" style="width:100%; height:600px; border:1px solid #e9ecef; border-radius:6px;"></iframe>
                                </div>

                                <!-- Version History -->
                                <div class="tab-pane fade" id="versionHistoryPane">
                                    <div id="version-history-list">
                                        <p class="text-muted">Select a store to view version history.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /tab-content -->
    </div><!-- /container -->

@endsection

@section('modal')
{{--    assign assets to store management--}}
    <div class="modal fade" id="assignAssetToStoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="assignAssetToStoreModalTitle">Store Assets</h5>
                        <small class="text-muted" id="assignAssetToStoreModalSubtitle">Assigned assets for the selected store.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="text-muted small" id="assignAssetToStoreSummary">Choose a store to view assigned assets.</div>
                        <span class="badge bg-primary-transparent" id="assignAssetToStoreCount">0 assigned assets</span>
                    </div>

                    <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold mb-1" for="assignAssetFilterCategory">Category</label>
                                <select class="form-select form-select-sm" id="assignAssetFilterCategory">
                                    <option value="">All Categories</option>
                                    @foreach($assetTypes as $assetType)
                                        <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold mb-1" for="assignAssetFilterFrom">Assigned From</label>
                                <input type="date" class="form-control form-control-sm" id="assignAssetFilterFrom">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold mb-1" for="assignAssetFilterTo">Assigned To</label>
                                <input type="date" class="form-control form-control-sm" id="assignAssetFilterTo">
                            </div>
                            <div class="col-12 col-md-2 d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm w-100" id="assignAssetFilterSubmit">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="assignAssetFilterReset" title="Reset filters">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="assignAssetToStoreLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted">Loading assigned assets...</div>
                    </div>

                    <div id="assignAssetToStoreEmpty" class="text-center py-5 d-none">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                        <div class="fw-semibold">No assigned assets found</div>
                        <small class="text-muted" id="assignAssetToStoreEmptyText">This store does not have any assigned asset records yet.</small>
                    </div>

                    <div class="table-responsive d-none" id="assignAssetToStoreTableWrap">
                        <table class="table table-bordered align-middle text-nowrap w-100 mb-0" id="assignedStoreAssetsTable">
                            <thead>
                            <tr>
                                <th width="60">SL</th>
                                <th>Asset</th>
                                <th>Asset Code</th>
                                <th>Category</th>
                                <th>Assign Date</th>
                                <th>Assigned By</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="modal fade" id="storeModal" >
        <div class="modal-dialog modal-dialog-centered modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon"><i class="bi bi-shop"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="storeModalLabel" style="font-size:1.05rem;">Add Store</h5>
                            <small class="text-muted" id="storeModalSubtitle">Create a new store entry</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="storeForm" enctype="multipart/form-data" >
                    <div class="modal-body">
                        <input type="hidden" id="store_id" value="">

                        <!-- Basic Information -->
                        <div class="form-section-title">Basic Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Store Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" placeholder="Enter store name">
                                <div class="invalid-feedback" id="error-title"></div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm text-uppercase" id="code" name="code" placeholder="Auto" maxlength="3" readonly>
                                <div class="invalid-feedback" id="error-code"></div>
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Store SEP Code</label>
                                <input type="text" class="form-control form-control-sm" id="store_code" name="store_code" placeholder="e.g. S001">
                                <div class="invalid-feedback" id="error-store_code"></div>
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="error-status"></div>
                            </div>
                        </div>

                        <!-- Store Dimensions & Rent -->
                        <div class="form-section-title">Store Dimensions & Rent</div>
                        <div class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Store Size (sq ft) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control form-control-sm" required id="total_area_sqft" name="total_area_sqft" placeholder="0.00">
                                <div class="invalid-feedback" id="error-total_area_sqft"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Monthly Rent (৳) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control form-control-sm" required id="monthly_rent" name="monthly_rent" placeholder="0.00">
                                <div class="invalid-feedback" id="error-monthly_rent"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Rent per Sq Ft (৳)</label>
                                <input type="number" readonly step="0.01" class="form-control form-control-sm" id="per_sqr_feet_rent" name="per_sqr_feet_rent" placeholder="0.00">
                                <div class="invalid-feedback" id="error-per_sqr_feet_rent"></div>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Opened Date</label>
                                <input type="date" class="form-control form-control-sm" id="opened_date" name="opened_date">
                                <div class="invalid-feedback" id="error-opened_date"></div>
                            </div>
                        </div>
                        <div class="calculated-rent mb-3" id="calculated-rent-display" style="display:none;">
                            <i class="bi bi-info-circle me-1"></i> Calculated Rent per Sq Ft: <strong id="calc-rent-value">—</strong>
                        </div>

                        <!-- Location -->
                        <div class="form-section-title">Location Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Division</label>
                                <select class="form-select form-select-sm" id="division_id" name="division_id">
                                    <option value="">— Select Division —</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-division_id"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">District</label>
                                <select class="form-select form-select-sm" id="district_id" name="district_id" disabled>
                                    <option value="">— Select District —</option>
                                </select>
                                <div class="invalid-feedback" id="error-district_id"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Thana</label>
                                <select class="form-select form-select-sm" id="thana_id" name="thana_id" disabled>
                                    <option value="">— Select Thana —</option>
                                </select>
                                <div class="invalid-feedback" id="error-thana_id"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Area/Locality</label>
                                <input type="text" class="form-control form-control-sm" id="area" name="area" placeholder="Area or locality name">
                                <div class="invalid-feedback" id="error-area"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Postal Code</label>
                                <input type="text" class="form-control form-control-sm" id="postal_code" name="postal_code" placeholder="Postal Code">
                                <div class="invalid-feedback" id="error-postal_code"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Store Type</label>
{{--                                <input type="text" class="form-control form-control-sm" id="store_type" name="store_type" placeholder="Postal Code">--}}
                                <select class="form-select form-select-sm" id="store_type" name="store_type">
                                    <option value="join_venture">Join Venture</option>
                                    <option value="partner">Partner</option>
                                    <option value="zone">Zone</option>
                                    <option value="zone_jv">Join Venture + Zone</option>
                                </select>
                                <div class="invalid-feedback" id="error-store_type"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Full Address</label>
                                <textarea class="form-control form-control-sm" id="address" name="address" rows="2" placeholder="Full address"></textarea>
                                <div class="invalid-feedback" id="error-address"></div>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Latitude</label>
                                <input type="text" class="form-control form-control-sm" id="latitude" name="latitude" placeholder="e.g. 23.8103">
                                <div class="invalid-feedback" id="error-latitude"></div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Longitude</label>
                                <input type="text" class="form-control form-control-sm" id="longitude" name="longitude" placeholder="e.g. 90.4125">
                                <div class="invalid-feedback" id="error-longitude"></div>
                            </div>
                        </div>

                        <!-- Store Layout -->
                        <div class="form-section-title">Store Layout</div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">Upload Layout (PDF)</label>
                            <input type="file" class="filepond-pdf" id="store_layout_pdf" name="store_layout_pdf" accept="application/pdf">
                            <div class="invalid-feedback d-block" id="error-store_layout_pdf" style="display:none !important;"></div>
                            <small class="text-muted d-block mt-1">Upload a new layout file. Max 10MB.</small>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-section-title">Contact Information</div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Contact Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required id="contact_persion" name="contact_person" placeholder="Contact person name">
                                <div class="invalid-feedback" id="error-contact_persion"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" required minlength="11" maxlength="11" id="shop_official_mobile" name="shop_official_mobile" placeholder="Mobile number">
                                <div class="invalid-feedback" id="error-shop_official_mobile"></div>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm" required id="shop_official_email" name="shop_official_email" placeholder="Email address">
                                <div class="invalid-feedback" id="error-shop_official_email"></div>
                            </div>
                        </div>

                        <!-- Store Manager -->
{{--                        <div class="form-section-title">Management</div>--}}
{{--                        <div class="row g-3">--}}
{{--                            <div class="col-12 col-md-6">--}}
{{--                                <label class="form-label fw-semibold" style="font-size:0.85rem;">Store Manager</label>--}}
{{--                                <select class="form-select form-select-sm" id="store_manager_id" name="store_manager_id">--}}
{{--                                    <option value="">— Select Manager —</option>--}}
{{--                                    @foreach($users as $user)--}}
{{--                                        <option value="{{ $user->id }}">{{ $user->name }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                                <div class="invalid-feedback" id="error-store_manager_id"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-save">
                            <span class="btn-text"><i class="bi bi-save me-1"></i>Save Store</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon"><i class="bi bi-shop"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" style="font-size:1.05rem;">Store Details</h5>
                            <small class="text-muted">View complete store information</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm">
                                <tr><th width="40%">Title</th><td id="view-title"></td></tr>
                                <tr><th>Code</th><td id="view-code"></td></tr>
                                <tr><th>Store Code</th><td id="view-store-code"></td></tr>
                                <tr><th>Area (sqft)</th><td id="view-area-sqft"></td></tr>
                                <tr><th>Monthly Rent</th><td id="view-rent"></td></tr>
                                <tr><th>Per Sqft Rent</th><td id="view-per-sqft-rent"></td></tr>
                                <tr><th>Opened Date</th><td id="view-opened"></td></tr>
                                <tr><th>Manager</th><td id="view-manager"></td></tr>
                                <tr><th>Status</th><td id="view-status"></td></tr>
                                <tr><th>Store Type</th><td id="view-store-type"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm">
                                <tr><th width="40%">Address</th><td id="view-address"></td></tr>
                                <tr><th>Area</th><td id="view-area"></td></tr>
                                <tr><th>Thana</th><td id="view-thana"></td></tr>
                                <tr><th>District</th><td id="view-district"></td></tr>
                                <tr><th>Division</th><td id="view-division"></td></tr>
                                <tr><th>Postal Code</th><td id="view-postal"></td></tr>
                                <tr><th>Coordinates</th><td id="view-coords"></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-sm">
                                <tr><th width="20%">Contact Person</th><td id="view-contact"></td></tr>
                                <tr><th>Mobile</th><td id="view-mobile"></td></tr>
                                <tr><th>Email</th><td id="view-email"></td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Layout History --}}
                    <div id="view-layouts-section" class="mt-3" style="display:none;">
                        <h6 class="fw-semibold text-muted mb-2"><i class="bi bi-grid-1x2 me-1"></i> Layout History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Changed At</th>
                                        <th>PDF</th>
                                        <th>Active</th>
                                    </tr>
                                </thead>
                                <tbody id="view-layouts-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3"><i class="bi bi-trash text-danger" style="font-size: 3rem;"></i></div>
                    <h6>Delete Store</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-store-name"></strong>?</p>
                    <input type="hidden" id="delete-store-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Upload New Layout Version Modal --}}
    <div class="modal fade" id="newVersionModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" style="font-size:1.05rem;">Upload New Version</h5>
                        <small class="text-muted" id="newVersionStoreName">Select a store first</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="newVersionForm" enctype="multipart/form-data">
                    <input type="hidden" id="new_version_store_id" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">Store Layout File <span class="text-danger">*</span></label>
                            <div class="upload-drop-zone" id="layoutDropZone">
                                <input type="file" class="d-none" id="new_version_file" name="store_layout_pdf" accept="application/pdf">
                                <div class="text-center py-3" id="layoutDropContent">
                                    <i class="bi bi-cloud-arrow-up d-block mb-1" style="font-size:1.6rem; color:#6c757d;"></i>
                                    <div style="font-size:0.85rem;">Click to upload or drag and drop</div>
                                    <small class="text-muted">PDF (Max 10MB)</small>
                                </div>
                                <div class="d-none px-3 py-2" id="layoutFileSelected">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.2rem;"></i>
                                            <span class="text-truncate" style="font-size:0.85rem; max-width:280px;" id="layoutFileName"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm p-0 text-muted" id="layoutFileRemove"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback d-block" id="error-new_version_file" style="display:none !important;"></div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">Changelog <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea class="form-control form-control-sm" id="new_version_changelog" name="changelog" rows="3" placeholder="Describe what changed in this version..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btn-upload-layout">
                            <span class="btn-text"><i class="bi bi-cloud-arrow-up me-1"></i>Upload Layout</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-upload-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Store Locator Map --}}
    <div class="modal fade" id="storeLocator" tabindex="-1" aria-labelledby="storeLocatorLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="modal-title-icon"><i class="bi bi-geo-alt-fill"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="storeLocatorLabel" style="font-size:1.05rem;">Store Locator</h5>
                            <small class="text-muted">All stores from latitude/longitude coordinates</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="locator-stat">
                                <span class="locator-stat-label">Total Stores</span>
                                <span class="locator-stat-value" id="locator-total-stores">{{ $stores->count() }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="locator-stat">
                                <span class="locator-stat-label">Mapped</span>
                                <span class="locator-stat-value text-success" id="locator-mapped-stores">0</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="locator-stat">
                                <span class="locator-stat-label">Missing Coords</span>
                                <span class="locator-stat-value text-warning" id="locator-missing-stores">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-4">
                            <div class="locator-list-wrap">
                                <div class="locator-list-head d-flex align-items-center justify-content-between">
                                    <span class="fw-semibold" style="font-size:0.9rem;">Mapped Stores</span>
                                    <span class="badge bg-light text-dark" id="locator-list-count">0</span>
                                </div>
                                <div id="storeLocatorList" class="list-group list-group-flush locator-list-body">
                                    <div class="text-muted text-center py-4" style="font-size:0.85rem;">
                                        Open map to load store points.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8">
                            <div id="storeLocatorMap" class="store-locator-map" aria-label="Store location map"></div>
                            <div class="alert alert-warning py-2 px-3 mt-2 mb-0 d-none" id="storeLocatorEmpty" style="font-size:0.85rem;">
                                <i class="bi bi-exclamation-triangle me-1"></i>No valid latitude/longitude found for stores.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <small class="text-muted me-auto">Click a store from list to focus marker on map.</small>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/leaflet/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<style>
    .filepond--root { margin-bottom: 0; }
    .upload-drop-zone {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color .2s, background .2s;
    }
    .upload-drop-zone:hover,
    .upload-drop-zone.dragover {
        border-color: var(--primary-color, #0162E8);
        background: rgba(var(--primary-rgb, 1,98,232), 0.04);
    }
    .calculated-rent {
        background: #fff8e1;
        border: 1px solid #ffe082;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.85rem;
        color: #f57c00;
    }
    .store-locator-map {
        width: 100%;
        min-height: 430px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f8fafc;
    }
    .locator-stat {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 10px 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .locator-stat-label {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.2;
    }
    .locator-stat-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }
    .locator-list-wrap {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
        min-height: 430px;
        background: #fff;
    }
    .locator-list-head {
        padding: 10px 12px;
        border-bottom: 1px solid #eef2f7;
        background: #f8fafc;
    }
    .locator-list-body {
        max-height: 378px;
        overflow-y: auto;
    }
    .locator-store-item {
        border: 0;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        padding: 10px 12px;
    }
    .locator-store-item:last-child {
        border-bottom: 0;
    }
    .locator-store-item .store-name {
        font-size: 0.86rem;
        font-weight: 600;
        color: #0f172a;
    }
    .locator-store-item .store-meta {
        font-size: 0.78rem;
        color: #64748b;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
    .store-popup-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 3px;
    }
    .store-popup-meta {
        font-size: 0.78rem;
        color: #475569;
    }
    @media (max-width: 991.98px) {
        .store-locator-map {
            min-height: 320px;
        }
        .locator-list-wrap {
            min-height: auto;
        }
        .locator-list-body {
            max-height: 220px;
        }
    }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.toastr')
    @include('backend.includes.plugins.datatable')
    <script src="{{ asset('backend/build/assets/libs/leaflet/leaflet.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script>
    $(document).ready(function () {
        const exportStoresUrl = @json(route('stores.export'));
        const storeModal = new bootstrap.Modal(document.getElementById('storeModal'));
        const viewModalEl = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));
        const storeLocatorModal = document.getElementById('storeLocator');

        const storeLocatorData = @json($storeLocatorData);
        const defaultMapCenter = [23.685, 90.3563];
        let locatorMap = null;
        let locatorLayer = null;
        let locatorMarkersById = {};

        function escapeHtml(value) {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function normalizeStoreCoordinates() {
            return storeLocatorData
                .map(function (store) {
                    const lat = parseFloat(store.latitude);
                    const lng = parseFloat(store.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
                    if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return null;

                    return {
                        id: store.id,
                        title: store.title,
                        code: store.code,
                        address: store.address,
                        division: store.division,
                        status: store.status,
                        latitude: lat,
                        longitude: lng
                    };
                })
                .filter(Boolean);
        }

        function storePopupHtml(store) {
            const statusBadge = store.status === 1
                ? '<span class="badge bg-success-transparent">Active</span>'
                : '<span class="badge bg-light text-muted">Inactive</span>';

            return `
                <div style="min-width:180px;">
                    <div class="store-popup-title">${escapeHtml(store.title || 'Store')}</div>
                    <div class="store-popup-meta mb-1">${escapeHtml(store.code || '')}</div>
                    <div class="store-popup-meta mb-2">${escapeHtml(store.division || store.address || 'Location unavailable')}</div>
                    <div class="store-popup-meta mb-2">${store.latitude.toFixed(6)}, ${store.longitude.toFixed(6)}</div>
                    ${statusBadge}
                </div>
            `;
        }

        function renderStoreLocatorList(validStores) {
            const $list = $('#storeLocatorList');
            $('#locator-list-count').text(validStores.length);

            if (!validStores.length) {
                $list.html('<div class="text-muted text-center py-4" style="font-size:0.85rem;">No mapped stores available.</div>');
                return;
            }

            let html = '';
            validStores.forEach(function (store) {
                html += `
                    <button type="button" class="list-group-item list-group-item-action locator-store-item" data-store-id="${store.id}">
                        <div class="store-name">${escapeHtml(store.title)}</div>
                        <div class="store-meta">${escapeHtml(store.division || store.address || 'N/A')}</div>
                        <div class="store-meta">${store.latitude.toFixed(5)}, ${store.longitude.toFixed(5)}</div>
                    </button>
                `;
            });
            $list.html(html);
        }

        function updateStoreLocatorStats(mappedCount) {
            const total = storeLocatorData.length;
            const missing = total - mappedCount;
            $('#locator-total-stores').text(total);
            $('#locator-mapped-stores').text(mappedCount);
            $('#locator-missing-stores').text(missing < 0 ? 0 : missing);
        }

        function initializeStoreLocatorMap(validStores) {
            if (!locatorMap) {
                locatorMap = L.map('storeLocatorMap', {
                    center: defaultMapCenter,
                    zoom: 7,
                    zoomControl: true,
                    attributionControl: true
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(locatorMap);

                locatorLayer = L.layerGroup().addTo(locatorMap);
            }

            locatorMarkersById = {};
            locatorLayer.clearLayers();

            if (!validStores.length) {
                $('#storeLocatorEmpty').removeClass('d-none');
                locatorMap.setView(defaultMapCenter, 7);
                setTimeout(function () { locatorMap.invalidateSize(); }, 150);
                return;
            }

            $('#storeLocatorEmpty').addClass('d-none');

            const bounds = [];
            validStores.forEach(function (store) {
                const marker = L.marker([store.latitude, store.longitude], {
                    title: store.title
                }).bindPopup(storePopupHtml(store));

                marker.addTo(locatorLayer);
                locatorMarkersById[String(store.id)] = marker;
                bounds.push([store.latitude, store.longitude]);
            });

            if (bounds.length === 1) {
                locatorMap.setView(bounds[0], 14);
            } else {
                locatorMap.fitBounds(bounds, { padding: [24, 24] });
            }

            setTimeout(function () { locatorMap.invalidateSize(); }, 150);
        }

        if (storeLocatorModal) {
            storeLocatorModal.addEventListener('shown.bs.modal', function () {
                if (typeof L === 'undefined') {
                    $('#storeLocatorEmpty')
                        .removeClass('d-none')
                        .html('<i class="bi bi-exclamation-triangle me-1"></i>Leaflet failed to load. Please check map assets.');
                    return;
                }

                const validStores = normalizeStoreCoordinates();
                updateStoreLocatorStats(validStores.length);
                renderStoreLocatorList(validStores);
                initializeStoreLocatorMap(validStores);
            });
        }

        $(document).on('click', '.locator-store-item', function () {
            const storeId = String($(this).data('store-id'));
            const marker = locatorMarkersById[storeId];
            if (!locatorMap || !marker) return;

            const position = marker.getLatLng();
            locatorMap.setView(position, Math.max(locatorMap.getZoom(), 15), { animate: true });
            marker.openPopup();
        });

        // --- Initialize DataTable ---
        let storesTable = $('#stores-table').DataTable({
            dom: '<"d-none"f>rt<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i>p>',
            pageLength: 15,
            lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
            order: [[0, 'asc']],
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ stores",
                infoEmpty: "No stores found",
                infoFiltered: "(filtered from _MAX_ total stores)",
                zeroRecords: "No matching stores found",
                paginate: {
                    previous: "<i class='bi bi-chevron-left'></i>",
                    next: "<i class='bi bi-chevron-right'></i>"
                }
            },
            columnDefs: [
                { orderable: false, targets: [6, 7] },
                { searchable: false, targets: [7] }
            ]
        });

        // Update store count label
        function updateStoreCount() {
            const info = storesTable.page.info();
            $('.store-count-label').text(info.recordsDisplay + ' of ' + info.recordsTotal + ' stores');
        }
        storesTable.on('draw', updateStoreCount);
        updateStoreCount();

        function reloadStoresTable() {
            const typeLabels = { join_venture: 'Join Venture', partner: 'Partner', zone: 'Zone', zone_jv: 'JV + Zone' };
            $.get(base_url + 'stores/json-list', function (stores) {
                storesTable.destroy();
                let html = '';
                stores.forEach(function (s) {
                    const rentPerSqft = (s.total_area_sqft > 0 && s.monthly_rent > 0)
                        ? (s.monthly_rent / s.total_area_sqft).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                        : null;
                    const statusBadge = s.status == 1
                        ? '<span class="badge badge-current rounded-pill"><i class="bi bi-check-circle me-1"></i>Active</span>'
                        : '<span class="badge badge-pending rounded-pill"><i class="bi bi-clock me-1"></i>Inactive</span>';
                    html += `<tr data-size="${s.total_area_sqft || ''}" data-rent="${s.monthly_rent || ''}">
                        <td><div class="d-flex align-items-center gap-2"><span class="store-icon"><i class="bi bi-shop"></i></span><div><div class="store-name">${escapeHtml(s.title)}</div><div class="store-id">ID: ${escapeHtml(s.code)}</div></div></div></td>
                        <td><i class="bi bi-geo-alt text-muted me-1" style="font-size:0.75rem;"></i>${s.division ? escapeHtml(s.division.name) : '—'}</td>
                        <td>${typeLabels[s.store_type] || (s.store_type || '—')}</td>
                        <td>${s.total_area_sqft ? parseFloat(s.total_area_sqft).toLocaleString('en-US', {maximumFractionDigits:0}) : '—'}</td>
                        <td>${s.monthly_rent ? parseFloat(s.monthly_rent).toLocaleString('en-US', {maximumFractionDigits:0}) + '৳' : '—'}</td>
                        <td class="text-warning">${rentPerSqft ? rentPerSqft + '৳' : '—'}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="javascript:void(0)" class="btn-action open-assign-asset-to-store-mdoal" data-id="${s.id}" title="Assigned Assets"><i class="bi bi-cassette"></i></a>
                            <button class="btn-action btn-view" data-id="${s.id}" title="View"><i class="bi bi-eye"></i></button>
                            <button class="btn-action btn-edit" data-id="${s.id}" title="Edit"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn-action text-danger btn-delete" data-id="${s.id}" data-name="${escapeHtml(s.title)}" title="Delete"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
                $('#stores-table tbody').html(html);
                storesTable = $('#stores-table').DataTable({
                    dom: '<"d-none"f>rt<"d-flex justify-content-between align-items-center mt-3"<"text-muted"i>p>',
                    pageLength: 15,
                    lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
                    order: [[0, 'asc']],
                    language: {
                        info: "Showing _START_ to _END_ of _TOTAL_ stores",
                        infoEmpty: "No stores found",
                        infoFiltered: "(filtered from _MAX_ total stores)",
                        zeroRecords: "No matching stores found",
                        paginate: { previous: "<i class='bi bi-chevron-left'></i>", next: "<i class='bi bi-chevron-right'></i>" }
                    },
                    columnDefs: [
                        { orderable: false, targets: [6, 7] },
                        { searchable: false, targets: [7] }
                    ]
                });
                storesTable.on('draw', updateStoreCount);
                updateStoreCount();
            });
        }

        // --- Custom Filters with DataTable ---
        // Search filter
        $('#filter-search').on('input', function () {
            storesTable.search($(this).val()).draw();
        });

        $('#exportStoreData').on('click', function () {
            const $button = $(this);
            const originalHtml = $button.html();

            $button.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-1"></i>Exporting...');

            $.ajax({
                url: exportStoresUrl,
                type: 'POST',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (blob, _status, xhr) {
                    const disposition = xhr.getResponseHeader('Content-Disposition') || '';
                    const match = disposition.match(/filename="?([^"]+)"?/i);
                    const filename = match && match[1] ? match[1] : 'stores.xlsx';
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');

                    link.href = downloadUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    window.URL.revokeObjectURL(downloadUrl);
                },
                error: function () {
                    toastr.error('Store export failed. Please try again.');
                },
                complete: function () {
                    $button.prop('disabled', false).html(originalHtml);
                }
            });
        });

        // Custom filter function for division, size, and rent
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'stores-table') return true;

            const $row = $(storesTable.row(dataIndex).node());
            const division = $('#filter-division').val().toLowerCase();
            const sizeFilter = $('#filter-size').val();
            const rentFilter = $('#filter-rent').val();

            const location = data[1].toLowerCase(); // Location column
            const size = parseFloat($row.data('size')) || 0;
            const rent = parseFloat($row.data('rent')) || 0;

            // Division filter
            if (division && !location.includes(division)) {
                return false;
            }

            // Size filter
            if (sizeFilter) {
                if (sizeFilter === 'small' && size >= 500) return false;
                if (sizeFilter === 'medium' && (size < 500 || size > 1000)) return false;
                if (sizeFilter === 'large' && size <= 1000) return false;
            }

            // Rent filter
            if (rentFilter) {
                if (rentFilter === 'low' && rent >= 50000) return false;
                if (rentFilter === 'mid' && (rent < 50000 || rent > 80000)) return false;
                if (rentFilter === 'high' && rent <= 80000) return false;
            }

            return true;
        });

        // Trigger redraw on filter change
        $('#filter-division, #filter-size, #filter-rent').on('change', function () {
            storesTable.draw();
        });

        // --- FilePond for Layout PDF ---
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        const pdfPond = FilePond.create(document.querySelector('.filepond-pdf'), {
            labelIdle: '<i class="bi bi-file-earmark-pdf" style="font-size:1.2rem;"></i><br>Drag & Drop layout PDF or <span class="filepond--label-action">Browse</span>',
            acceptedFileTypes: ['application/pdf'],
            maxFileSize: '10MB',
            credits: false,
        });

        // --- Auto-generate code from title ---
        $('#title').on('input', function () {
            if (!$('#store_id').val()) {
                const name = $(this).val().trim();
                if (name.length >= 2) {
                    let code = '';
                    const words = name.split(/\s+/);
                    if (words.length >= 3) {
                        code = words.map(w => w[0]).join('').substring(0, 3);
                    } else if (words.length === 2) {
                        code = words[0].substring(0, 2) + words[1][0];
                    } else {
                        const consonants = name.replace(/[aeiou]/gi, '');
                        if (consonants.length >= 2) {
                            code = name[0] + consonants[1] + (consonants[2] || name[name.length - 1]);
                        } else {
                            code = name.substring(0, 3);
                        }
                    }
                    $('#code').val(code.toUpperCase().substring(0, 3));
                }
            }
        });

        // --- Calculate Rent per Sq Ft ---
        $('#total_area_sqft, #monthly_rent').on('input', function () {
            const area = parseFloat($('#total_area_sqft').val()) || 0;
            const rent = parseFloat($('#monthly_rent').val()) || 0;
            if (area > 0 && rent > 0) {
                const perSqft = (rent / area).toFixed(2);
                $('#calc-rent-value').text(perSqft + '৳');
                $('#per_sqr_feet_rent').val(perSqft);
                $('#calculated-rent-display').show();
            } else {
                $('#calculated-rent-display').hide();
            }
        });

        // --- Cascading Dropdowns ---
        $('#division_id').on('change', function () {
            const divisionId = $(this).val();
            $('#district_id').html('<option value="">— Select District —</option>').prop('disabled', true);
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);

            if (divisionId) {
                $.get(base_url + 'get-districts/' + divisionId, function (districts) {
                    let options = '<option value="">— Select District —</option>';
                    districts.forEach(function (d) {
                        options += `<option value="${d.id}">${d.name}</option>`;
                    });
                    $('#district_id').html(options).prop('disabled', false);
                });
            }
        });

        $('#district_id').on('change', function () {
            const districtId = $(this).val();
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);

            if (districtId) {
                $.get(base_url + 'get-thanas/' + districtId, function (thanas) {
                    let options = '<option value="">— Select Thana —</option>';
                    thanas.forEach(function (t) {
                        options += `<option value="${t.id}">${t.name}</option>`;
                    });
                    $('#thana_id').html(options).prop('disabled', false);
                });
            }
        });

        // --- Open Add Modal ---
        $('#btn-add-store').on('click', function () {
            resetForm();
            $('#storeModalLabel').text('Add Store');
            $('#storeModalSubtitle').text('Create a new store entry');
            $('#btn-save .btn-text').html('<i class="bi bi-save me-1"></i>Save Store');
            storeModal.show();
        });

        // --- Open Edit Modal ---
        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#storeModalLabel').text('Edit Store');
            $('#storeModalSubtitle').text('Update store information');
            $('#btn-save .btn-text').html('<i class="bi bi-save me-1"></i>Update Store');
            $.get(base_url + 'stores/' + id + '/edit', function (data) {
                $('#store_id').val(data.id);
                $('#title').val(data.title);
                $('#code').val(data.code);
                $('#store_code').val(data.store_code);
                $('#total_area_sqft').val(data.total_area_sqft);
                $('#monthly_rent').val(data.monthly_rent);
                $('#per_sqr_feet_rent').val(data.per_sqr_feet_rent);
                $('#opened_date').val(data.opened_date);
                // $('#store_manager_id').val(data.store_manager_id || '');
                $('#address').val(data.address);
                $('#area').val(data.area);
                $('#postal_code').val(data.postal_code);
                $('#latitude').val(data.latitude);
                $('#longitude').val(data.longitude);
                $('#contact_persion').val(data.contact_person);
                $('#shop_official_mobile').val(data.shop_official_mobile);
                $('#shop_official_email').val(data.shop_official_email);
                $('#status').val(data.status);
                $('#store_type').val(data.store_type);

                // Trigger rent calculation
                $('#total_area_sqft').trigger('input');

                // Load cascading dropdowns
                if (data.division_id) {
                    $('#division_id').val(data.division_id);
                    $.get(base_url + 'get-districts/' + data.division_id, function (districts) {
                        let options = '<option value="">— Select District —</option>';
                        districts.forEach(function (d) {
                            options += `<option value="${d.id}" ${d.id == data.district_id ? 'selected' : ''}>${d.name}</option>`;
                        });
                        $('#district_id').html(options).prop('disabled', false);

                        if (data.district_id) {
                            $.get(base_url + 'get-thanas/' + data.district_id, function (thanas) {
                                let options = '<option value="">— Select Thana —</option>';
                                thanas.forEach(function (t) {
                                    options += `<option value="${t.id}" ${t.id == data.thana_id ? 'selected' : ''}>${t.name}</option>`;
                                });
                                $('#thana_id').html(options).prop('disabled', false);
                            });
                        }
                    });
                }

                storeModal.show();
            });
        });

        // --- View Store ---
        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'stores/' + id, function (data) {
                $('#view-title').text(data.title);
                $('#view-code').text(data.code);
                $('#view-store-code').text(data.store_code || '—');
                $('#view-area-sqft').text(data.total_area_sqft ? data.total_area_sqft + ' sqft' : '—');
                $('#view-rent').text(data.monthly_rent ? '৳' + parseFloat(data.monthly_rent).toLocaleString() : '—');
                $('#view-per-sqft-rent').text(data.per_sqr_feet_rent ? '৳' + parseFloat(data.per_sqr_feet_rent).toLocaleString() : '—');
                $('#view-opened').text(data.opened_date || '—');
                $('#view-manager').text(data.store_manager ? data.store_manager.name : '—');
                $('#view-status').html(data.status == 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                const storeTypeLabels = {
                    'join_venture': 'Join Venture',
                    'partner':      'Partner',
                    'zone':         'Zone',
                    'zone_jv':      'JV + Zone',
                };
                $('#view-store-type').text(storeTypeLabels[data.store_type] || data.store_type || '—');
                $('#view-address').text(data.address || '—');
                $('#view-area').text(data.area || '—');
                $('#view-thana').text(data.thana ? data.thana.name : '—');
                $('#view-district').text(data.district ? data.district.name : '—');
                $('#view-division').text(data.division ? data.division.name : '—');
                $('#view-postal').text(data.postal_code || '—');
                $('#view-coords').text(data.latitude && data.longitude ? data.latitude + ', ' + data.longitude : '—');
                $('#view-contact').text(data.contact_person || '—');
                $('#view-mobile').text(data.shop_official_mobile || '—');
                $('#view-email').text(data.shop_official_email || '—');

                // Layout history
                if (data.store_layouts && data.store_layouts.length) {
                    $('#view-layouts-section').show();
                    let html = '';
                    data.store_layouts.forEach(function (layout, i) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + (layout.changed_at || '—') + '</td>';
                        html += '<td>' + (layout.layout_pdf ? '<a href="' + base_url + layout.layout_pdf + '" target="_blank" class="btn btn-xs btn-outline-primary">Download</a>' : '—') + '</td>';
                        html += '<td>' + (layout.is_currently_active == 1 ? '<span class="badge bg-success-transparent">Active</span>' : '<span class="text-muted">—</span>') + '</td>';
                        html += '</tr>';
                    });
                    $('#view-layouts-body').html(html);
                } else {
                    $('#view-layouts-section').hide();
                }

                viewModalEl.show();
            });
        });

        // --- Delete ---
        $(document).on('click', '.btn-delete', function () {
            $('#delete-store-id').val($(this).data('id'));
            $('#delete-store-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-store-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'stores/' + id,
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    toastr.success(res.message);
                    reloadStoresTable();
                },
                error: function () {
                    toastr.error('Failed to delete store.');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        // --- Submit Form ---
        $('#storeForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            let hasError = false;
            const areaVal = $('#total_area_sqft').val();
            if (!areaVal) {
                $('#total_area_sqft').addClass('is-invalid');
                $('#error-total_area_sqft').text('Store size (sq ft) is required.');
                hasError = true;
            } else if (parseFloat(areaVal) < 0) {
                $('#total_area_sqft').addClass('is-invalid');
                $('#error-total_area_sqft').text('Store size cannot be negative.');
                hasError = true;
            }
            const rentVal = $('#monthly_rent').val();
            if (!rentVal) {
                $('#monthly_rent').addClass('is-invalid');
                $('#error-monthly_rent').text('Monthly rent is required.');
                hasError = true;
            } else if (parseFloat(rentVal) < 0) {
                $('#monthly_rent').addClass('is-invalid');
                $('#error-monthly_rent').text('Monthly rent cannot be negative.');
                hasError = true;
            }
            if (hasError) return;

            const id = $('#store_id').val();
            const url = id ? base_url + 'stores/' + id : base_url + 'stores';
            const formData = new FormData(this);

            // Append FilePond PDF file
            const pdfFile = pdfPond.getFile();
            if (pdfFile) {
                formData.append('store_layout_pdf', pdfFile.file);
            }

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
                    storeModal.hide();
                    toastr.success(res.message);
                    reloadStoresTable();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            if (field === 'store_layout_pdf') {
                                $('#error-store_layout_pdf').text(messages[0]).css('display', 'block');
                            } else {
                                $('#' + field).addClass('is-invalid');
                                $('#error-' + field).text(messages[0]);
                            }
                        });
                    } else {
                        toastr.error('Something went wrong.');
                    }
                },
                complete: function () {
                    $('#btn-save').prop('disabled', false);
                    $('#btn-spinner').addClass('d-none');
                }
            });
        });

        // --- Helpers ---
        function resetForm() {
            $('#storeForm')[0].reset();
            $('#store_id').val('');
            pdfPond.removeFiles();
            $('#district_id').html('<option value="">— Select District —</option>').prop('disabled', true);
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);
            $('#calculated-rent-display').hide();
            clearErrors();
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').css('display', '');
        }

        // ===== LAYOUTS TAB - AJAX Infinite Scroll =====
        (function () {
            let page = 1, lastPage = 1, loading = false;
            let searchTerm = '', division = '';
            let debounceTimer = null;

            const $list = $('#layout-store-list');
            const $spinner = $('#layout-load-spinner');
            const $count = $('#layout-store-count');

            function renderItem(store) {
                const divName = store.division ? store.division.name : 'N/A';
                const badge = store.store_layouts_count > 0
                    ? '<div class="version-badge"><i class="bi bi-check-circle me-1"></i>Has Layout</div>'
                    : '<div class="no-layout-badge"><i class="bi bi-clock me-1"></i>No layout uploaded</div>';

                return `<div class="store-list-item" data-store-id="${store.id}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="store-list-name">${store.title}</div>
                            <div class="store-list-meta">${divName} &bull; ${store.code}</div>
                            ${badge}
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </div>`;
            }

            function loadStores(reset) {
                if (loading) return;
                if (!reset && page > lastPage) return;

                if (reset) {
                    page = 1;
                    $list.empty();
                }

                loading = true;
                $spinner.removeClass('d-none');

                $.get(base_url + 'stores/layout-list', { page: page, search: searchTerm, division: division }, function (res) {
                    res.data.forEach(function (store) {
                        $list.append(renderItem(store));
                    });

                    lastPage = res.last_page;
                    $count.text(res.total + ' stores found');
                    page++;

                    if (reset && res.data.length > 0) {
                        $list.find('.store-list-item').first().addClass('active').trigger('click');
                    }

                    if (reset && res.data.length === 0) {
                        $list.html('<div class="text-center text-muted py-4"><i class="bi bi-search d-block mb-2" style="font-size:1.5rem;"></i>No stores found</div>');
                    }
                }).fail(function () {
                    toastr.error('Failed to load stores.');
                }).always(function () {
                    loading = false;
                    $spinner.addClass('d-none');
                });
            }

            // Infinite scroll
            $list.on('scroll', function () {
                if (this.scrollTop + this.clientHeight >= this.scrollHeight - 50) {
                    loadStores(false);
                }
            });

            // Search with debounce
            $('#layout-search').on('input', function () {
                clearTimeout(debounceTimer);
                const val = $(this).val().trim();
                debounceTimer = setTimeout(function () {
                    searchTerm = val;
                    loadStores(true);
                }, 300);
            });

            // Division filter
            $('#layout-division-filter').on('change', function () {
                division = $(this).val();
                loadStores(true);
            });

            // Click store item - load detail + PDF preview
            $list.on('click', '.store-list-item', function () {
                $list.find('.store-list-item').removeClass('active');
                $(this).addClass('active');

                $.get(base_url + 'stores/' + $(this).data('store-id'), function (data) {
                    $('#layout-store-name').text(data.title);
                    $('#layout-store-address').text(
                        (data.division ? data.division.name : '') + ' \u2022 ' + (data.address || '')
                    );

                    // Find active layout PDF
                    var pdfPath = null;
                    if (data.store_layouts && data.store_layouts.length) {
                        var active = data.store_layouts.find(function (l) { return l.is_currently_active == 1; });
                        if (active && active.layout_pdf) {
                            pdfPath = active.layout_pdf;
                        } else if (data.store_layouts[0].layout_pdf) {
                            pdfPath = data.store_layouts[0].layout_pdf;
                        }
                    }

                    if (pdfPath) {
                        var fullUrl = base_url + pdfPath;
                        $('#layout-pdf-viewer').attr('src', fullUrl).removeClass('d-none');
                        $('#layout-placeholder').addClass('d-none');
                        $('#layout-download-btn').attr('href', fullUrl).removeClass('d-none');
                    } else {
                        $('#layout-pdf-viewer').attr('src', '').addClass('d-none');
                        $('#layout-placeholder').removeClass('d-none')
                            .html('<i class="bi bi-file-earmark-pdf d-block mb-2"></i><div class="fw-semibold" style="font-size:0.9rem;">No Layout Available</div><small>This store has no layout uploaded yet</small>');
                        $('#layout-download-btn').addClass('d-none');
                    }

                    // Render version history
                    renderVersionHistory(data.store_layouts || []);
                });
            });

            // Lazy init on first tab show
            let initialized = false;
            $('button[data-bs-target="#layoutsPane"]').on('shown.bs.tab', function () {
                if (!initialized) {
                    loadStores(true);
                    initialized = true;
                }
            });

            if ($('#layoutsPane').hasClass('show')) {
                loadStores(true);
                initialized = true;
            }

            // Render version history
            function renderVersionHistory(layouts) {
                var $container = $('#version-history-list');
                if (!layouts.length) {
                    $container.html('<p class="text-muted">No layout versions uploaded yet.</p>');
                    return;
                }

                // Sort by newest first
                layouts.sort(function (a, b) { return b.id - a.id; });

                var html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">' +
                    '<thead><tr><th style="width:60px;">#</th><th>Date</th><th>Changelog</th><th>PDF</th><th style="width:70px;">Status</th></tr></thead><tbody>';

                layouts.forEach(function (layout, i) {
                    var version = 'v' + (layouts.length - i) + '.0';
                    var badge = layout.is_currently_active == 1
                        ? '<span class="badge bg-success-transparent">Active</span>'
                        : '<span class="text-muted">—</span>';
                    var pdfLink = layout.layout_pdf
                        ? '<a href="' + base_url + layout.layout_pdf + '" target="_blank" class="btn btn-xs btn-outline-primary"><i class="bi bi-download me-1"></i>Download</a>'
                        : '—';
                    var changelog = layout.change_log || '<span class="text-muted">—</span>';

                    html += '<tr>' +
                        '<td><span class="fw-semibold">' + version + '</span></td>' +
                        '<td>' + (layout.changed_at || '—') + '</td>' +
                        '<td style="font-size:0.85rem;">' + changelog + '</td>' +
                        '<td>' + pdfLink + '</td>' +
                        '<td>' + badge + '</td>' +
                        '</tr>';
                });

                html += '</tbody></table></div>';
                $container.html(html);
            }
        })();

        // ===== Upload New Version Modal =====
        (function () {
            const $dropZone = $('#layoutDropZone');
            const $fileInput = $('#new_version_file');
            const $dropContent = $('#layoutDropContent');
            const $fileSelected = $('#layoutFileSelected');
            const $fileName = $('#layoutFileName');

            // Set store info when modal opens
            $('#newVersionModal').on('show.bs.modal', function () {
                const $active = $('#layout-store-list .store-list-item.active');
                if ($active.length) {
                    const storeId = $active.data('store-id');
                    const storeName = $active.find('.store-list-name').text();
                    const storeMeta = $active.find('.store-list-meta').text();
                    $('#new_version_store_id').val(storeId);
                    $('#newVersionStoreName').text(storeName + ' - ' + storeMeta);
                } else {
                    $('#new_version_store_id').val('');
                    $('#newVersionStoreName').text('No store selected');
                }
                // Reset form
                $fileInput.val('');
                $('#new_version_changelog').val('');
                $dropContent.removeClass('d-none');
                $fileSelected.addClass('d-none');
                $('#error-new_version_file').css('display', 'none').text('');
            });

            // Click to upload
            $dropZone.on('click', function (e) {
                if (!$(e.target).closest('#layoutFileRemove').length && e.target !== $fileInput[0]) {
                    $fileInput.trigger('click');
                }
            });

            // Prevent click on file input from bubbling back to drop zone
            $fileInput.on('click', function (e) {
                e.stopPropagation();
            });

            // File selected
            $fileInput.on('change', function () {
                if (this.files && this.files[0]) {
                    showFile(this.files[0]);
                }
            });

            // Drag & drop
            $dropZone.on('dragover', function (e) {
                e.preventDefault();
                $(this).addClass('dragover');
            }).on('dragleave drop', function (e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            }).on('drop', function (e) {
                const files = e.originalEvent.dataTransfer.files;
                if (files.length) {
                    $fileInput[0].files = files;
                    showFile(files[0]);
                }
            });

            // Remove file
            $('#layoutFileRemove').on('click', function () {
                $fileInput.val('');
                $dropContent.removeClass('d-none');
                $fileSelected.addClass('d-none');
            });

            function showFile(file) {
                if (file.type !== 'application/pdf') {
                    toastr.error('Only PDF files are allowed.');
                    $fileInput.val('');
                    return;
                }
                $fileName.text(file.name);
                $dropContent.addClass('d-none');
                $fileSelected.removeClass('d-none');
            }

            // Submit
            $('#newVersionForm').on('submit', function (e) {
                e.preventDefault();
                const storeId = $('#new_version_store_id').val();
                if (!storeId) {
                    toastr.error('Please select a store first.');
                    return;
                }
                if (!$fileInput[0].files.length) {
                    $('#error-new_version_file').text('Please select a layout file.').css('display', 'block');
                    return;
                }

                const formData = new FormData(this);

                $('#btn-upload-layout').prop('disabled', true);
                $('#btn-upload-spinner').removeClass('d-none');

                $.ajax({
                    url: base_url + 'stores/' + storeId + '/layouts',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#newVersionModal').modal('hide');
                        toastr.success(res.message || 'Layout uploaded successfully.');
                        // Refresh the active store detail
                        $('#layout-store-list .store-list-item.active').trigger('click');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.store_layout_pdf) {
                                $('#error-new_version_file').text(errors.store_layout_pdf[0]).css('display', 'block');
                            }
                        } else {
                            toastr.error('Failed to upload layout.');
                        }
                    },
                    complete: function () {
                        $('#btn-upload-layout').prop('disabled', false);
                        $('#btn-upload-spinner').addClass('d-none');
                    }
                });
            });
        })();
    });
    </script>

    <script>
        const assignAssetsFilterUrl = @json(route('assets.assign-assets.filter'));
        let assignedStoreAssetsTable = null;
        let activeAssignAssetStore = null;

        function resetAssignedStoreAssetsTable() {
            if ($.fn.DataTable.isDataTable('#assignedStoreAssetsTable')) {
                $('#assignedStoreAssetsTable').DataTable().clear().destroy();
            }

            $('#assignedStoreAssetsTable tbody').empty();
            assignedStoreAssetsTable = null;
        }

        function toggleAssignedStoreAssetsState(state) {
            $('#assignAssetToStoreLoading').toggleClass('d-none', state !== 'loading');
            $('#assignAssetToStoreEmpty').toggleClass('d-none', state !== 'empty');
            $('#assignAssetToStoreTableWrap').toggleClass('d-none', state !== 'table');
        }

        function getAssignedStoreFilterParams() {
            const params = {
                store_id: activeAssignAssetStore?.id || ''
            };

            const assetTypeId = $('#assignAssetFilterCategory').val();
            const assignedFrom = $('#assignAssetFilterFrom').val();
            const assignedTo = $('#assignAssetFilterTo').val();

            if (assetTypeId) {
                params.asset_type_id = assetTypeId;
            }

            if (assignedFrom) {
                params.assigned_from = assignedFrom;
            }

            if (assignedTo) {
                params.assigned_to = assignedTo;
            }

            return params;
        }

        function resetAssignedStoreFilters() {
            $('#assignAssetFilterCategory').val('');
            $('#assignAssetFilterFrom').val('');
            $('#assignAssetFilterTo').val('');
        }

        function setAssignedStoreFilterLoading(isLoading) {
            $('#assignAssetFilterSubmit').prop('disabled', isLoading);
            $('#assignAssetFilterReset').prop('disabled', isLoading);
        }

        function formatAssignedAssetDate(value) {
            if (!value) {
                return '-';
            }

            const parsed = new Date(value);

            if (Number.isNaN(parsed.getTime())) {
                return value;
            }

            return parsed.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function renderAssignedStoreAssetsTable(items) {
            resetAssignedStoreAssetsTable();

            const rows = items.map(function (item, index) {
                const asset = item.asset || {};
                const categoryName = asset.asset_type?.name || '-';
                const assignedBy = item.assigned_by?.name || '-';

                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="fw-semibold">${asset.name || '-'}</div>
                        </td>
                        <td>${asset.asset_code || '-'}</td>
                        <td>${categoryName}</td>
                        <td>${formatAssignedAssetDate(item.assign_date)}</td>
                        <td>${assignedBy}</td>
                    </tr>
                `;
            }).join('');

            $('#assignedStoreAssetsTable tbody').html(rows);

            assignedStoreAssetsTable = $('#assignedStoreAssetsTable').DataTable({
                dom: '<"d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"<"text-muted"i><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"lp>',
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                order: [[1, 'asc']],
                responsive: true,
                language: {
                    searchPlaceholder: 'Search assigned assets...',
                    sSearch: '',
                    info: 'Showing _START_ to _END_ of _TOTAL_ assigned assets',
                    infoEmpty: 'No assigned assets found',
                    zeroRecords: 'No matching assigned assets found',
                    lengthMenu: 'Show _MENU_ entries',
                    paginate: {
                        previous: "<i class='bi bi-chevron-left'></i>",
                        next: "<i class='bi bi-chevron-right'></i>"
                    }
                },
                columnDefs: [
                    { orderable: false, searchable: false, targets: 0 }
                ]
            });

            $('#assignAssetToStoreCount').text(`${items.length} assigned asset${items.length === 1 ? '' : 's'}`);
            toggleAssignedStoreAssetsState('table');
        }

        function loadAssignedStoreAssets() {
            if (!activeAssignAssetStore?.id) {
                return;
            }

            const assignedFrom = $('#assignAssetFilterFrom').val();
            const assignedTo = $('#assignAssetFilterTo').val();

            if (assignedFrom && assignedTo && assignedFrom > assignedTo) {
                toastr.error('Assigned from date must be before or equal to assigned to date.');
                return;
            }

            resetAssignedStoreAssetsTable();
            toggleAssignedStoreAssetsState('loading');
            $('#assignAssetToStoreSummary').text(`Loading assigned assets for ${activeAssignAssetStore.name}...`);
            $('#assignAssetToStoreCount').text('Loading...');
            setAssignedStoreFilterLoading(true);

            $.ajax({
                url: assignAssetsFilterUrl,
                type: 'GET',
                data: getAssignedStoreFilterParams(),
                success: function (response) {
                    const items = Array.isArray(response) ? response : [];

                    $('#assignAssetToStoreSummary').text(
                        items.length
                            ? `Assigned assets currently linked to ${activeAssignAssetStore.name}.`
                            : `No assigned assets matched the selected filters for ${activeAssignAssetStore.name}.`
                    );

                    if (!items.length) {
                        $('#assignAssetToStoreCount').text('0 assigned assets');
                        $('#assignAssetToStoreEmptyText').text('Try a different category or widen the assigned date range.');
                        toggleAssignedStoreAssetsState('empty');
                        return;
                    }

                    renderAssignedStoreAssetsTable(items);
                },
                error: function (xhr) {
                    const message = xhr.status === 422
                        ? xhr.responseJSON?.message || 'The selected filter values are not valid.'
                        : 'Failed to load assigned assets.';

                    $('#assignAssetToStoreSummary').text(`Failed to load assigned assets for ${activeAssignAssetStore.name}.`);
                    $('#assignAssetToStoreCount').text('0 assigned assets');
                    $('#assignAssetToStoreEmptyText').text('Please adjust the filters and try again.');
                    toggleAssignedStoreAssetsState('empty');
                    toastr.error(message);
                },
                complete: function () {
                    setAssignedStoreFilterLoading(false);
                }
            });
        }

        $('#assignAssetToStoreModal').on('hidden.bs.modal', function () {
            resetAssignedStoreAssetsTable();
            toggleAssignedStoreAssetsState('empty');
            $('#assignAssetToStoreModalTitle').text('Store Assets');
            $('#assignAssetToStoreModalSubtitle').text('Assigned assets for the selected store.');
            $('#assignAssetToStoreSummary').text('Choose a store to view assigned assets.');
            $('#assignAssetToStoreCount').text('0 assigned assets');
            $('#assignAssetToStoreEmptyText').text('This store does not have any assigned asset records yet.');
            activeAssignAssetStore = null;
            resetAssignedStoreFilters();
        });

        $('#assignAssetToStoreModal').on('shown.bs.modal', function () {
            if (assignedStoreAssetsTable) {
                assignedStoreAssetsTable.columns.adjust().responsive.recalc();
            }
        });

        $('#assignAssetFilterSubmit').on('click', function () {
            loadAssignedStoreAssets();
        });

        $('#assignAssetFilterReset').on('click', function () {
            resetAssignedStoreFilters();
            loadAssignedStoreAssets();
        });

        $(document).on('click', '.open-assign-asset-to-store-mdoal', function () {
            const storeId = $(this).data('id');
            const $row = $(this).closest('tr');
            const storeName = $row.find('.store-name').first().text().trim() || `Store #${storeId}`;
            const storeCode = $row.find('.store-id').first().text().replace('ID:', '').trim();

            activeAssignAssetStore = {
                id: storeId,
                name: storeName,
                code: storeCode
            };

            resetAssignedStoreFilters();
            $('#assignAssetToStoreModalTitle').text(storeName);
            $('#assignAssetToStoreModalSubtitle').text(storeCode ? `Store code: ${storeCode}` : `Store ID: ${storeId}`);
            $('#assignAssetToStoreEmptyText').text('This store does not have any assigned asset records yet.');
            $('#assignAssetToStoreModal').modal('show');
            loadAssignedStoreAssets();
        });
    </script>
@endpush
