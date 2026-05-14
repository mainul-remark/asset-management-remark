@extends('backend.master')

@section('title', 'Key Visual Installation')

@section('body')

    <!-- Main Content -->
    <div class="container px-3 px-lg-4 py-3">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active"><i class="bi bi-tools me-1"></i>Installations</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
            <div>
                <h1 class="page-title mb-1">Branding and Branding Material Installation</h1>
                <p class="page-subtitle mb-0">Streamlined 3-step workflow: Planned &rarr; Installed &rarr; Verified</p>
            </div>
{{--            <div class="page-header-actions d-flex gap-2 flex-wrap">--}}
{{--                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-clipboard-data me-1"></i>Export Report</button>--}}
{{--                <button class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#newInstallationModal"><i class="bi bi-plus me-1"></i>New Installation</button>--}}
{{--            </div>--}}
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
{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-12 col-md-6">--}}
{{--                <div class="content-card p-3">--}}
{{--                    <h6 class="fw-bold mb-3" style="font-size:0.95rem;"><i class="bi bi-currency-dollar me-1 text-warning"></i>Total Investment</h6>--}}
{{--                    <div class="d-flex justify-content-between mb-2">--}}
{{--                        <span class="inst-invest-label">Total Budget:</span>--}}
{{--                        <span class="inst-invest-value fw-bold">BDT 36,850</span>--}}
{{--                    </div>--}}
{{--                    <div class="d-flex justify-content-between mb-2">--}}
{{--                        <span class="inst-invest-label">Completed Value:</span>--}}
{{--                        <span class="inst-invest-value fw-bold text-success">BDT 16,500</span>--}}
{{--                    </div>--}}
{{--                    <div class="d-flex justify-content-between">--}}
{{--                        <span class="inst-invest-label">Pending Value:</span>--}}
{{--                        <span class="inst-invest-value fw-bold text-warning">BDT 20,350</span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-12 col-md-6">--}}
{{--                <div class="content-card p-3">--}}
{{--                    <h6 class="fw-bold mb-3" style="font-size:0.95rem;"><i class="bi bi-exclamation-triangle me-1 text-warning"></i>Status Overview</h6>--}}
{{--                    <div class="d-flex justify-content-between mb-2">--}}
{{--                        <span class="inst-status-label" style="color:#e67e22;">Planned installations:</span>--}}
{{--                        <span class="fw-bold" style="color:#2c3e6b;">2</span>--}}
{{--                    </div>--}}
{{--                    <div class="d-flex justify-content-between mb-2">--}}
{{--                        <span class="inst-status-label" style="color:#e67e22;">Awaiting verification:</span>--}}
{{--                        <span class="fw-bold" style="color:#2c3e6b;">2</span>--}}
{{--                    </div>--}}
{{--                    <div class="d-flex justify-content-between">--}}
{{--                        <span class="inst-status-label" style="color:#e67e22;">Completed & verified:</span>--}}
{{--                        <span class="fw-bold" style="color:#2c3e6b;">1</span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- Filters Card -->
        <div class="content-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="font-size:0.95rem;"><i class="bi bi-funnel me-1"></i>Filters</h6>
                <button class="btn btn-link btn-sm text-muted text-decoration-none p-0"><i class="bi bi-chevron-down me-1"></i>More</button>
            </div>
            <div class="row g-2">
                <div class="col-12 col-md-4">
                    <label class="inst-filter-label">Search</label>
                    <input type="text" id="filter-search" class="form-control form-control-sm" placeholder="Search installations...">
                </div>
                <div class="col-6 col-md-4">
                    <label class="inst-filter-label">Status</label>
                    <select class="form-select form-select-sm" id="filter-status" name="installation_status_filter">
                        <option value="">Select an option</option>
                        <option value="pending">Pending</option>
                        <option value="planned">Planned</option>
                        <option value="installed">Installed</option>
                        <option value="verified">Verified</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="inst-filter-label">Store</label>
                    <select class="form-select form-select-sm select-ele" id="filter-store">
                        <option value="">Select an option</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->title.' ('.$store->code.')' }}</option>
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
            <div class="table-responsive py-2 px-3">
                <table class="table inst-table mb-0" id="installation-table">
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
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div><!-- /container -->

@endsection

@section('modal')

    <!-- ========== INSTALLATION PROOF UPLOAD MODAL ========== -->
    @if($permissions['canUploadProof'])
    <div class="modal fade" id="installationProofModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Files</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form id="installationProofForm" action="{{ route('key-visuals.update-asset-assigned-kv-data', ['for' => 'proof']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="asset_assign_kv_id" id="assetAssignKvId">
                            <div class="mt-2">
                                <label for="installationFiles" class="mb-2">Installation Files</label>
                                <input type="file" id="installationFiles" name="instalation_proof[]" class="filepond-installation-proof" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple />
                            </div>
                            <div class="mt-3 text-end">
                                <input type="submit" id="installationProofSubmitBtn" class="btn btn-sm btn-success" value="Upload Installation Files">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
    @if($permissions['canView'])
    <div class="modal fade" id="installationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" style="font-size:1.1rem;">Installation Details</h5>
                        <small class="text-muted" id="inst-detail-subtitle">—</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    <!-- Loading state -->
                    <div id="inst-detail-loading" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                        <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">Loading details...</p>
                    </div>

                    <!-- Detail content (hidden until loaded) -->
                    <div id="inst-detail-content" style="display:none;">
                        <!-- Detail Tabs -->
                        <ul class="nav nav-tabs-custom nav-tabs border-bottom mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#instDetailOverview" type="button">Overview</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#instDetailPhotos" type="button">Photos <span class="badge bg-secondary ms-1" id="inst-detail-photo-count" style="font-size:0.7rem;">0</span></button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="instDetailOverview">
                                <!-- Status Badge -->
                                <div class="mb-3">
                                    <span class="inst-detail-status-badge" id="inst-detail-status-badge"><i class="bi bi-calendar-event me-1"></i>—</span>
                                </div>

                                <!-- Info Grid -->
                                <div class="row g-4">
                                    <div class="col-12 col-md-6">
                                        <h6 class="inst-detail-section-title">Store Information</h6>
                                        <table class="table table-sm inst-detail-info-table">
                                            <tr><td class="inst-detail-info-label">Store Name:</td><td class="fw-semibold" id="inst-detail-store-name">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Store Code:</td><td class="fw-semibold" id="inst-detail-store-code">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Location:</td><td id="inst-detail-store-location">—</td></tr>
                                        </table>

                                        <h6 class="inst-detail-section-title mt-3">Branding Details</h6>
                                        <table class="table table-sm inst-detail-info-table">
                                            <tr><td class="inst-detail-info-label">Medium:</td><td class="fw-semibold" id="inst-detail-medium">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Branding ID:</td><td><span class="inst-branding-id" id="inst-detail-branding-id">—</span></td></tr>
                                        </table>

                                        <h6 class="inst-detail-section-title mt-3">KV Details</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="inst-detail-kv-preview" id="inst-detail-kv-preview-wrap">
                                                <i class="bi bi-image text-muted" style="font-size:1.5rem;"></i>
                                            </div>
                                            <div>
                                                <div class="inst-kv-id" id="inst-detail-kv-code">—</div>
                                                <div class="inst-badge-new mt-1" id="inst-detail-kv-file-code">—</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <h6 class="inst-detail-section-title">Schedule & Assignment</h6>
                                        <table class="table table-sm inst-detail-info-table">
                                            <tr><td class="inst-detail-info-label">Created On:</td><td id="inst-detail-created-on">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Installation Date:</td><td id="inst-detail-installation-date">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Assigned By:</td><td id="inst-detail-assigned-by">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Installed By:</td><td id="inst-detail-installed-by">—</td></tr>
                                            <tr><td class="inst-detail-info-label">Slot No:</td><td id="inst-detail-slot-number">—</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Photos Tab -->
                            <div class="tab-pane fade" id="instDetailPhotos">
                                <div id="inst-detail-photos-container" class="d-flex flex-wrap gap-2">
                                    <p class="text-muted">No photos uploaded yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/select2-4.1.0/select2-bootstrap-5-theme.min.css') }}">
<style>
    .filepond--root {
        margin-bottom: 0;
    }
    .inst-status-installed {
        background: rgba(40, 167, 69, 0.3) !important;
        color: rgba(40, 167, 69, .8) !important;
        border-color: rgba(40, 167, 69, 1) !important;;
    }
</style>

@endpush

@push('scripts')
@include('backend.includes.plugins.datatable')
@include('backend.includes.plugins.select2')
@include('backend.includes.plugins.sweetalert2')
@include('backend.includes.plugins.toastr')
@if($permissions['canUploadProof'])
<script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
@endif
{{--<script src="{{ asset('backend/build/select2-4.1.0/select2.min.js') }}"></script>--}}

<script>
    const kvInstallationPermissions = @json($permissions);

    @if($permissions['canUploadProof'])
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize
    );

    const installationProofPond = FilePond.create(document.querySelector('.filepond-installation-proof'), {
        allowMultiple: true,
        instantUpload: false,
        allowProcess: false,
        allowRevert: false,
        maxFiles: 10,
        credits: false,
        acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
        maxFileSize: '5MB',
        labelMaxFileSizeExceeded: 'Image is too large',
        labelMaxFileSize: 'Maximum image size is 5 MB',
        labelIdle: '<i class="ri-upload-cloud-2-line" style="font-size:1.45rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop installation photos or <span class="filepond--label-action">browse</span></span>',
        imagePreviewHeight: 150,
    });

    document.getElementById('installationProofModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('installationProofForm').reset();
        installationProofPond.removeFiles();
        $('#assetAssignKvId').val('');
        $('#installationProofSubmitBtn').prop('disabled', false).val('Upload Installation Files');
    });
    @endif

    const installationTable = $('#installation-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        searchDelay: 400,
        ajax: {
            url: '{{ route('key-visuals.kv-installation.datatable') }}',
            data: function (d) {
                d.search_text = $('#filter-search').val();
                d.installation_status_filter = $('#filter-status').val();
                d.store_id = $('#filter-store').val();
            }
        },
        columns: [
            { data: 'store_name', name: 'stores.title' },
            { data: 'branding_medium', name: 'asset_types.name', orderable: false },
            { data: 'kv_id', name: 'key_visuals.unique_code', orderable: false },
            { data: 'status', name: 'assign_kv_to_assets.instalation_status', orderable: false },
            { data: 'photos', name: 'photos', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"i>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            processing: '<div class="py-4">Loading installations...</div>',
            emptyTable: 'No installations found.',
            zeroRecords: 'No matching installations found.',
            info: 'Showing _START_ to _END_ of _TOTAL_ installations',
            infoEmpty: 'No installations found',
            paginate: {
                previous: "<i class='ri-arrow-left-s-line'></i>",
                next: "<i class='ri-arrow-right-s-line'></i>"
            }
        }
    });

    function reloadInstallationTable(resetPaging = false) {
        installationTable.ajax.reload(null, resetPaging);
    }

    let installationFilterTimer;
    $('#filter-search').on('input', function () {
        clearTimeout(installationFilterTimer);
        installationFilterTimer = setTimeout(function () {
            reloadInstallationTable(true);
        }, 350);
    });

    $('#filter-status, #filter-store').on('change', function () {
        reloadInstallationTable(true);
    });

    @if($permissions['canChangeStatus'])
    $(document).on('click', '.inst-status-dd-item', function (e) {
        e.preventDefault();
        var currentElement = $(this);
        var currentStatus = $(this).data('status');
        var assetAssignedKvId = $(this).data('asset-assigned-kv-id');
        $(this).closest('.inst-status-dropdown').find('.inst-status-dd-item').removeClass('active'); // remove active class
        $(this).addClass('active').css({color: "white"});
        $.ajax({
            url: "{{ route('key-visuals.update-asset-assigned-kv-data', ['for' => 'status']) }}",
            type: 'POST',
            data: {
                status: currentStatus,
                assigned_asset_kv_id: assetAssignedKvId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (!response.success)
                {
                    toastr.error(response.message);
                } else {
                    toastr.success(response.message);
                    reloadInstallationTable(false);
                }
            },
            error: function () {
                toastr.error('Unable to update installation status.');
            }
        });
    })
    @endif
    @if($permissions['canView'])
    const kvInstallationDetailUrl = '{{ route('key-visuals.kv-installation.detail', ['id' => '__ID__']) }}';

    $('#installationDetailModal').on('show.bs.modal', function (event) {
        const id = $(event.relatedTarget).data('kv-installation');
        const url = kvInstallationDetailUrl.replace('__ID__', id);

        $('#inst-detail-loading').show();
        $('#inst-detail-content').hide();
        $('#inst-detail-subtitle').text('—');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    toastr.error(response.message || 'Failed to load details.');
                    $('#installationDetailModal').modal('hide');
                    return;
                }

                const d = response.data;
                const statusIcon = { installed: 'bi-check-circle', verified: 'bi-shield-check' };
                const icon = statusIcon[d.status] || 'bi-calendar-event';
                const statusLabel = d.status ? d.status.charAt(0).toUpperCase() + d.status.slice(1) : '—';

                $('#inst-detail-subtitle').text((d.store_title || '—') + (d.key_visual_name ? ' — ' + d.key_visual_name : ''));
                $('#inst-detail-status-badge').html('<i class="bi ' + icon + ' me-1"></i>' + statusLabel);
                $('#inst-detail-store-name').text(d.store_title || '—');
                $('#inst-detail-store-code').text(d.store_code || '—');
                $('#inst-detail-store-location').text(d.store_address || '—');
                $('#inst-detail-medium').text(d.asset_type_name || '—');
                $('#inst-detail-branding-id').text(d.asset_code || '—');
                $('#inst-detail-kv-code').text(d.key_visual_code ? 'KV: ' + d.key_visual_code : '—');
                $('#inst-detail-kv-file-code').text(d.kv_file_code ? 'File: ' + d.kv_file_code : '—');

                if (d.kv_thumb) {
                    $('#inst-detail-kv-preview-wrap').html('<img src="' + d.kv_thumb + '" alt="KV" style="max-width:100px;max-height:70px;object-fit:cover;border-radius:4px;">');
                } else {
                    $('#inst-detail-kv-preview-wrap').html('<i class="bi bi-image text-muted" style="font-size:1.5rem;"></i>');
                }

                $('#inst-detail-created-on').text(d.created_at || '—');
                $('#inst-detail-installation-date').text(d.instalation_date || '—');
                $('#inst-detail-assigned-by').text(d.assigned_by_name || '—');
                $('#inst-detail-installed-by').text(d.installed_by_name || '—');
                $('#inst-detail-slot-number').text(d.slot_number || '—');

                const photos = d.proof_files || [];
                $('#inst-detail-photo-count').text(photos.length);
                if (photos.length) {
                    $('#inst-detail-photos-container').html(
                        photos.map(function (src) {
                            return '<a href="' + src + '" target="_blank"><img src="' + src + '" style="width:110px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;" alt="proof"></a>';
                        }).join('')
                    );
                } else {
                    $('#inst-detail-photos-container').html('<p class="text-muted mb-0">No photos uploaded yet.</p>');
                }

                $('#inst-detail-loading').hide();
                $('#inst-detail-content').show();
            },
            error: function () {
                toastr.error('Unable to load installation details.');
                $('#installationDetailModal').modal('hide');
            }
        });
    });

    $('#installationDetailModal').on('hidden.bs.modal', function () {
        $('#inst-detail-loading').show();
        $('#inst-detail-content').hide();
        // reset first tab to active
        $('#installationDetailModal .nav-link').first().tab('show');
    });
    @endif

    @if($permissions['canUploadProof'])
    $(document).on('click', '.upload-proof-image', function () {
        $('#assetAssignKvId').val($(this).data('asset-assign-kv-id'));
        $('#installationProofModal').modal('show');
    })
    $(document).on('submit', '#installationProofForm', function (e) {
        e.preventDefault();

        const files = installationProofPond.getFiles();
        if (!files.length) {
            toastr.error('Please select at least one installation photo.');
            return;
        }

        const formData = new FormData(this);
        formData.delete('instalation_proof[]');
        files.forEach(function (fileItem) {
            formData.append('instalation_proof[]', fileItem.file);
        });

        const $submitButton = $('#installationProofSubmitBtn');
        $submitButton.prop('disabled', true).val('Uploading...');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (!response.success) {
                    toastr.error(response.message || 'Unable to upload installation proof.');
                    $submitButton.prop('disabled', false).val('Upload Installation Files');
                    return;
                }

                toastr.success(response.message || 'Installation proof uploaded successfully.');
                $('#installationProofModal').modal('hide');
                reloadInstallationTable(false);
            },
            error: function () {
                toastr.error('Something went wrong while uploading installation proof.');
                $submitButton.prop('disabled', false).val('Upload Installation Files');
            }
        });
    })
    @endif
</script>
@endpush
