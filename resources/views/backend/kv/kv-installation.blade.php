@extends('backend.master')

@section('title', 'Key Visual Installation')

@section('body')

    <!-- Main Content -->
    <div class="container px-3 px-lg-4 py-3">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active"><i class="bi bi-tools me-1"></i>Installations</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
            <div>
                <h1 class="page-title mb-1">Branding and Branding Material Installation</h1>
                <p class="page-subtitle mb-0">Streamlined 3-step workflow: Planned &rarr; Installed &rarr; Verified</p>
            </div>
            <div class="page-header-actions d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard-data me-1"></i>Export Report</button>
                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#newInstallationModal"><i class="bi bi-plus me-1"></i>New Installation</button>
            </div>
        </div>

        <!-- Stat Cards Row -->
        <div class="row g-3 mb-3 mt-1">
            <div class="col-6 col-lg">
                <div class="stat-card">
                    <div>
                        <div class="stat-label">Total Installations</div>
                        <div class="stat-value">5</div>
                    </div>
                    <div class="stat-icon ms-auto" style="background:#eef1f6;color:#2c3e6b;"><i class="bi bi-globe"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="inst-stat-card-planned">
                    <div>
                        <div class="stat-label">Planned</div>
                        <div class="stat-value">2</div>
                    </div>
                    <div class="stat-icon ms-auto" style="background:#fff3e0;color:#e65100;"><i class="bi bi-calendar-event"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="inst-stat-card-installed">
                    <div>
                        <div class="stat-label">Installed</div>
                        <div class="stat-value">2</div>
                    </div>
                    <div class="stat-icon ms-auto" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check-circle"></i></div>
                </div>
            </div>
            <div class="col-6 col-lg">
                <div class="inst-stat-card-verified">
                    <div>
                        <div class="stat-label">Verified</div>
                        <div class="stat-value">1</div>
                    </div>
                    <div class="stat-icon ms-auto" style="background:#fff3e0;color:#e67e22;"><i class="bi bi-shield-check"></i></div>
                </div>
            </div>
            <div class="col-12 col-lg">
                <div class="stat-card">
                    <div>
                        <div class="stat-label">Completion Rate</div>
                        <div class="stat-value">20%</div>
                    </div>
                    <div class="stat-icon ms-auto" style="background:#ede7f6;color:#5e35b1;"><i class="bi bi-share"></i></div>
                </div>
            </div>
        </div>

        <!-- Investment & Status Row -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-md-6">
                <div class="content-card p-3">
                    <h6 class="fw-bold mb-3" style="font-size:0.95rem;"><i class="bi bi-currency-dollar me-1 text-warning"></i>Total Investment</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="inst-invest-label">Total Budget:</span>
                        <span class="inst-invest-value fw-bold">BDT 36,850</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="inst-invest-label">Completed Value:</span>
                        <span class="inst-invest-value fw-bold text-success">BDT 16,500</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="inst-invest-label">Pending Value:</span>
                        <span class="inst-invest-value fw-bold text-warning">BDT 20,350</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="content-card p-3">
                    <h6 class="fw-bold mb-3" style="font-size:0.95rem;"><i class="bi bi-exclamation-triangle me-1 text-warning"></i>Status Overview</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="inst-status-label" style="color:#e67e22;">Planned installations:</span>
                        <span class="fw-bold" style="color:#2c3e6b;">2</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="inst-status-label" style="color:#e67e22;">Awaiting verification:</span>
                        <span class="fw-bold" style="color:#2c3e6b;">2</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="inst-status-label" style="color:#e67e22;">Completed & verified:</span>
                        <span class="fw-bold" style="color:#2c3e6b;">1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="content-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="font-size:0.95rem;"><i class="bi bi-funnel me-1"></i>Filters</h6>
                <button class="btn btn-link btn-sm text-muted text-decoration-none p-0"><i class="bi bi-chevron-down me-1"></i>More</button>
            </div>
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="inst-filter-label">Search</label>
                    <input type="text" class="form-control form-control-sm" placeholder="Search installations...">
                </div>
                <div class="col-6 col-md-4">
                    <label class="inst-filter-label">Status</label>
                    <select class="form-select form-select-sm" name="installation_status_filter">
                        <option>Select an option</option>
                        <option value="pending">Pending</option>
                        <option value="planned">Planned</option>
                        <option value="planned">Installed</option>
                        <option value="planned">Verified</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="inst-filter-label">Store</label>
                    <select class="form-select form-select-sm">
                        <option>Select an option</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->title.' ('.$store->code.'0' }}</option>
                        @endforeach
{{--                        <option>Dhaka Central Mall</option>--}}
{{--                        <option>Barisal City Center</option>--}}
{{--                        <option>Chittagong Plaza</option>--}}
{{--                        <option>Sylhet Shopping Center</option>--}}
                    </select>
                </div>
            </div>
        </div>

        <!-- Installation Table -->
        <div class="content-card">
            <div class="table-responsive">
                <table class="table inst-table mb-0">
                    <thead>
                    <tr>
{{--                        <th style="width:36px;"><input type="checkbox" class="form-check-input"></th>--}}
                        <th>Store Name <i class="bi bi-arrow-down-up" style="font-size:0.6rem;"></i></th>
                        <th>Branding Medium</th>
{{--                        <th>Branding ID</th>--}}
                        <th>KV ID</th>
                        <th>Status <i class="bi bi-arrow-down-up" style="font-size:0.6rem;"></i></th>
                        <th>Photos</th>
{{--                        <th>Last Updated</th>--}}
                        <th style="width:36px;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Row 1: Rajshahi Grand Mall -->
                    @foreach($assignedAssetkeyVisuals as $assignedAssetkeyVisual)
                        <tr>
{{--                            <td><input type="checkbox" class="form-check-input"></td>--}}
                            <td>
                                <div class="inst-store-name">{{ $assignedAssetkeyVisual?->asset?->store?->title ?? '' }}</div>
                                <div class="inst-store-meta">{{ $assignedAssetkeyVisual?->asset?->store?->address ?? '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:0.85rem;">{{ $assignedAssetkeyVisual?->asset?->assetType?->name ?? '' }}</div>
{{--                                <div class="inst-store-meta">Ponds</div>--}}
                            </td>
{{--                            <td><span class="inst-branding-id">WD-RAJ-PON-004</span></td>--}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="inst-kv-thumb"><img src="{{ asset($assignedAssetkeyVisual?->keyVisual->kv_thumb) }}" alt="{{ $assignedAssetkeyVisual?->keyVisual->name }}"></div>
                                    <div>
                                        <span class="inst-kv-id">{{ $assignedAssetkeyVisual?->keyVisual->unique_code }}</span>
                                        <span class="inst-badge-new">New</span>
                                    </div>
                                </div>
{{--                                <a href="#" class="inst-change-kv"><i class="bi bi-arrow-repeat me-1"></i>Change KV</a>--}}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="inst-status-btn inst-status-planned dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-calendar-event me-1"></i>Planned
                                    </button>
                                    <ul class="dropdown-menu inst-status-dropdown">
                                        <li><a class="dropdown-item inst-status-dd-item active" href="#" data-status="pending"><i class="bi bi-calendar-event me-1"></i>Pending <i class="bi bi-check ms-auto"></i></a></li>
                                        <li><a class="dropdown-item inst-status-dd-item " href="#" data-status="Planned"><i class="bi bi-calendar-event me-1"></i>Planned <i class="bi bi-check ms-auto"></i></a></li>
                                        <li><a class="dropdown-item inst-status-dd-item" href="#" data-status="Installed"><i class="bi bi-check-circle me-1"></i>Installed</a></li>
                                        <li>
                                            <a class="dropdown-item inst-status-dd-item " href="#">
                                                <i class="bi bi-shield-check me-1"></i>Verified
{{--                                                <i class="bi bi-shield-check me-1"></i>Upload an image to enable 'Verified'--}}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                @if(isset($assignedAssetkeyVisual->istalation_proof))
                                    @foreach(json_decode($assignedAssetkeyVisual->istalation_proof) as $file)
                                        <div class="inst-photo-thumb"><img src="{{ asset($file) }}" alt="{{ $assignedAssetkeyVisual?->keyVisual->name }} proof Photo"></div>
                                    @endforeach
                                @else
                                    <span class="inst-no-photos">No photos</span>
                                @endif
                            </td>
{{--                            <td>--}}
{{--                                <div class="inst-date">05/01/2025</div>--}}
{{--                                <div class="inst-store-meta">Store Manager</div>--}}
{{--                            </td>--}}
                            <td>
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#installationDetailModal"><i class="bi bi-eye"></i></button>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Row 5: Sylhet Shopping Center -->
{{--                    <tr>--}}
{{--                        <td><input type="checkbox" class="form-check-input"></td>--}}
{{--                        <td>--}}
{{--                            <div class="inst-store-name">Sylhet Shopping Center</div>--}}
{{--                            <div class="inst-store-meta">SYL &bull; Zindabazar, Sylhet</div>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <div class="fw-semibold" style="font-size:0.85rem;">LED Screen</div>--}}
{{--                            <div class="inst-store-meta">Samsung</div>--}}
{{--                        </td>--}}
{{--                        <td><span class="inst-branding-id">LS-SYL-SAM-003</span></td>--}}
{{--                        <td>--}}
{{--                            <div class="d-flex align-items-center gap-2">--}}
{{--                                <div class="inst-kv-thumb inst-kv-thumb-video">--}}
{{--                                    <i class="bi bi-play-circle"></i>--}}
{{--                                </div>--}}
{{--                                <div>--}}
{{--                                    <span class="inst-kv-id">SAM_LS_002</span>--}}
{{--                                    <div class="inst-store-meta">Duration: 0:45</div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <a href="#" class="inst-change-kv"><i class="bi bi-arrow-repeat me-1"></i>Change KV</a>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <div class="dropdown">--}}
{{--                                <button class="inst-status-btn inst-status-verified dropdown-toggle" data-bs-toggle="dropdown">--}}
{{--                                    <i class="bi bi-shield-check me-1"></i>Verified--}}
{{--                                </button>--}}
{{--                                <ul class="dropdown-menu inst-status-dropdown">--}}
{{--                                    <li><a class="dropdown-item inst-status-dd-item" href="#"><i class="bi bi-calendar-event me-1"></i>Planned</a></li>--}}
{{--                                    <li><a class="dropdown-item inst-status-dd-item" href="#"><i class="bi bi-check-circle me-1"></i>Installed</a></li>--}}
{{--                                    <li><a class="dropdown-item inst-status-dd-item active" href="#"><i class="bi bi-shield-check me-1"></i>Verified <i class="bi bi-check ms-auto"></i></a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <div class="d-flex gap-1">--}}
{{--                                <div class="inst-photo-thumb"><img src="https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=60&h=40&fit=crop" alt="Photo"></div>--}}
{{--                                <div class="inst-photo-thumb"><img src="https://images.unsplash.com/photo-1588006173527-b56e7fdd8596?w=60&h=40&fit=crop" alt="Photo"></div>--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <div class="inst-date">01/01/2025</div>--}}
{{--                            <div class="inst-store-meta">Store Manager</div>--}}
{{--                        </td>--}}
{{--                        <td>--}}
{{--                            <button class="btn-action" data-bs-toggle="modal" data-bs-target="#installationDetailModal"><i class="bi bi-eye"></i></button>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /container -->

@endsection

@section('modal')

    <!-- ========== NEW INSTALLATION MODAL ========== -->
    <div class="modal fade" id="newInstallationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" style="font-size:1.1rem;">New Installation</h5>
                        <small class="text-muted">Create a new installation tracking entry</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Store <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm">
                            <option>Select a store</option>
                            <option>Rajshahi Grand Mall (RAJ)</option>
                            <option>Dhaka Central Mall (DHA)</option>
                            <option>Barisal City Center (BAR)</option>
                            <option>Chittagong Plaza (CTG)</option>
                            <option>Sylhet Shopping Center (SYL)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Branding Medium <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" disabled>
                            <option>Select branding medium</option>
                        </select>
                        <small class="text-muted">Select a store first</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Branding ID <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" disabled>
                            <option>Select existing branding ID</option>
                        </select>
                        <small class="text-muted">Select a branding medium first</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">KV ID <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" disabled>
                            <option>Select key visual</option>
                        </select>
                        <small class="text-muted">Select a branding medium first</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Status</label>
                        <input type="text" class="form-control form-control-sm" value="Planned" readonly>
                        <small class="text-muted">New installations start with 'Planned' status</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold" style="font-size:0.85rem;">Image Upload <span class="text-muted fw-normal">(Optional - can be added later)</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-warning btn-sm text-white">Choose File</button>
                            <span class="text-muted" style="font-size:0.85rem;">No file chosen</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning btn-sm text-white"><i class="bi bi-plus me-1"></i>Create Installation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== INSTALLATION DETAIL MODAL ========== -->
    <div class="modal fade" id="installationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" style="font-size:1.1rem;">Installation Details</h5>
                        <small class="text-muted">Rajshahi Grand Mall - Ponds</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Detail Tabs -->
                    <ul class="nav nav-tabs-custom nav-tabs border-bottom mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#instDetailOverview" type="button">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instDetailPhotos" type="button">Photos</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instDetailTimeline" type="button">Timeline</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instDetailAudit" type="button">Audit</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="instDetailOverview">
                            <!-- Status Badge -->
                            <div class="mb-3">
                                <span class="inst-detail-status-badge"><i class="bi bi-calendar-event me-1"></i>Planned</span>
                            </div>

                            <!-- Info Grid -->
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <h6 class="inst-detail-section-title">Store Information</h6>
                                    <table class="table table-sm inst-detail-info-table">
                                        <tr><td class="inst-detail-info-label">Store Name:</td><td class="fw-semibold">Rajshahi Grand Mall</td></tr>
                                        <tr><td class="inst-detail-info-label">Store Code:</td><td class="fw-semibold">RAJ</td></tr>
                                        <tr><td class="inst-detail-info-label">Location:</td><td>Shaheb Bazar, Rajshahi</td></tr>
                                    </table>

                                    <h6 class="inst-detail-section-title mt-3">Branding Details</h6>
                                    <table class="table table-sm inst-detail-info-table">
                                        <tr><td class="inst-detail-info-label">Brand:</td><td class="fw-semibold">Ponds</td></tr>
                                        <tr><td class="inst-detail-info-label">Medium:</td><td>Window Display</td></tr>
                                        <tr><td class="inst-detail-info-label">Branding ID:</td><td><span class="inst-branding-id">WD-RAJ-PON-004</span></td></tr>
                                    </table>

                                    <h6 class="inst-detail-section-title mt-3">KV ID</h6>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span></span>
                                        <a href="#" class="inst-change-kv"><i class="bi bi-arrow-repeat me-1"></i>Change KV</a>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="inst-detail-kv-preview">
                                            <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=120&h=80&fit=crop" alt="KV">
                                        </div>
                                        <div>
                                            <span class="inst-kv-id">PON_WD_001</span>
                                            <span class="inst-badge-new ms-1">New</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h6 class="inst-detail-section-title">Schedule & Assignment</h6>
                                    <table class="table table-sm inst-detail-info-table">
                                        <tr><td class="inst-detail-info-label">Created On:</td><td>5 Jan 2025, 17:45</td></tr>
                                        <tr><td class="inst-detail-info-label">Installation Date:</td><td>20 Jan 2025, 06:00</td></tr>
                                        <tr><td class="inst-detail-info-label">Assigned To:</td><td>Store Manager</td></tr>
                                        <tr><td class="inst-detail-info-label">Team Lead:</td><td>Karim Hassan</td></tr>
                                    </table>

                                    <h6 class="inst-detail-section-title mt-3">Cost Information</h6>
                                    <table class="table table-sm inst-detail-info-table">
                                        <tr><td class="inst-detail-info-label">Total Cost:</td><td class="fw-semibold">BDT 5,200</td></tr>
                                        <tr><td class="inst-detail-info-label">Quantity:</td><td>2</td></tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Notes -->
                            <h6 class="inst-detail-section-title mt-3">Notes</h6>
                            <p class="inst-detail-notes">Regular branding installation - no replacement</p>
                        </div>

                        <!-- Photos Tab -->
                        <div class="tab-pane fade" id="instDetailPhotos">
                            <p class="text-muted">No photos uploaded yet.</p>
                        </div>

                        <!-- Timeline Tab -->
                        <div class="tab-pane fade" id="instDetailTimeline">
                            <p class="text-muted">Timeline will appear here.</p>
                        </div>

                        <!-- Audit Tab -->
                        <div class="tab-pane fade" id="instDetailAudit">
                            <p class="text-muted">Audit log will appear here.</p>
                        </div>
                    </div>
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

@endpush

@push('scripts')
@include('backend.includes.plugins.datatable')
@include('backend.includes.plugins.select2')
@include('backend.includes.plugins.sweetalert2')
<script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
{{--<script src="{{ asset('backend/build/select2-4.1.0/select2.min.js') }}"></script>--}}

<script>
    $(document).on('click', '.inst-status-dd-item', function () {
        event.preventDefault();
        var currentStatus = $(this).data('status');
    })
</script>
@endpush
