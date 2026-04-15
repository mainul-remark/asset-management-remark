@extends('backend.master')

@section('title', 'VM Issues')

@php
    $assetDirectory = $assets->map(function ($asset) {
        return [
            'id'                  => $asset->id,
            'name'                => $asset->name,
            'asset_code'          => $asset->asset_code,
            'store_id'            => $asset->store_id,
            'status'              => (int) $asset->status,
            'is_common_asset'     => (int) $asset->is_common_asset,
            'asset_type_name'     => $asset->assetType?->name,
            'store_name'          => $asset->store?->title,
            'assignment_store_ids' => $asset->assignAssetToStores
                ->pluck('store_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all(),
        ];
    })->values();
@endphp

@section('body')
<div class="container px-3 px-lg-4 py-3">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="bi bi-house me-1"></i>Home</a></li>
            <li class="breadcrumb-item active"><i class="bi bi-tools me-1"></i>VM Issues</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
        <div>
            <h1 class="page-title mb-1">Damaged Asset Tracker</h1>
            <p class="text-muted fs-13 mb-0">Track and manage your reported Assets issues.</p>
        </div>
        <div class="page-header-actions d-flex gap-2 flex-wrap">
            <a href="{{ route('vm.vm-issues.export') }}" id="btn-export" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Report
            </a>
            <button class="btn btn-warning btn-sm text-white" id="btn-add-vm">
                <i class="bi bi-plus me-1"></i>New VM Issue
            </button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-3 mt-1">
        <div class="col-6 col-lg">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Total Issues</div>
                    <div class="stat-value" id="stat-total">0</div>
                </div>
                <div class="stat-icon ms-auto" style="background:#eef1f6;color:#2c3e6b;"><i class="bi bi-globe"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="inst-stat-card-planned">
                <div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-value" id="stat-pending">0</div>
                </div>
                <div class="stat-icon ms-auto" style="background:#fff3e0;color:#e65100;"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="inst-stat-card-installed">
                <div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value" id="stat-inprogress">0</div>
                </div>
                <div class="stat-icon ms-auto" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-arrow-repeat"></i></div>
            </div>
        </div>
        <div class="col-6 col-lg">
            <div class="inst-stat-card-verified">
                <div>
                    <div class="stat-label">Solved</div>
                    <div class="stat-value" id="stat-solved">0</div>
                </div>
                <div class="stat-icon ms-auto" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
        <div class="col-12 col-lg">
            <div class="stat-card">
                <div>
                    <div class="stat-label">Completion Rate</div>
                    <div class="stat-value" id="stat-rate">0%</div>
                </div>
                <div class="stat-icon ms-auto" style="background:#ede7f6;color:#5e35b1;"><i class="bi bi-share"></i></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="font-size:0.95rem;"><i class="bi bi-funnel me-1"></i>Filters</h6>
        </div>
        <div class="row g-2">
            <div class="col-12 col-md-4">
                <label class="inst-filter-label">Search</label>
                <input type="text" class="form-control form-control-sm" id="filter-search" placeholder="Search by asset or store...">
            </div>
            <div class="col-6 col-md-4">
                <label class="inst-filter-label">Fix Status</label>
                <select class="form-select form-select-sm" id="filter-status">
                    <option value="">All statuses</option>
                    @foreach($issueFixStatuses as $s)
                        <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-4">
                <label class="inst-filter-label">Store</label>
                <select class="form-select form-select-sm select-ele" id="filter-store">
                    <option value="">All stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="content-card">
        <div class="table-responsive">
            <table class="table inst-table mb-0" id="vm-issues-table">
                <thead>
                <tr>
{{--                    <th style="width:36px;"><input type="checkbox" class="form-check-input" id="select-all"></th>--}}
                    <th>Store</th>
                    <th>Asset</th>
                    <th>Issue</th>
                    <th>Fix Status</th>
                    <th>Files</th>
                    <th>Date</th>
                    <th style="width:100px;">Actions</th>
                </tr>
                </thead>
                <tbody id="vm-issues-tbody"></tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('modal')

    {{-- Create / Edit Modal --}}
    <div class="modal fade" id="vmModal">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold" id="vmModalLabel">Add VM Issue</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="vmForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    @csrf
                    <input type="hidden" id="vm_id" value="">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="store_id" name="store_id">
                                    <option value="">Select store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }}{{ $store->code ? ' ('.$store->code.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-store_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="asset_id" name="asset_id">
                                    <option value="">Select store first</option>
                                </select>
                                <div class="form-text">Asset options are filtered by the selected store.</div>
                                <div class="invalid-feedback" id="error-asset_id"></div>
                            </div>
                            <input type="hidden" id="issue_fix_status" name="issue_fix_status" value="pending">
                            <input type="hidden" id="status" name="status" value="1">
                            <div class="col-12">
                                <label for="issue_text" class="form-label">Issue Details <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="issue_text" name="issue_text" rows="5" placeholder="Describe the visual merchandising issue in detail."></textarea>
                                <div class="invalid-feedback" id="error-issue_text"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Upload Evidence</label>
                                <div class="vm-upload-zone" id="vmUploadZone" tabindex="0" role="button">
                                    <input type="file" class="d-none" id="vm_files" name="vm_files[]"
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm"
                                        multiple>
                                    <div class="vm-upload-zone__inner">
                                        <i class="bi bi-cloud-upload vm-upload-zone__icon"></i>
                                        <div class="fw-semibold">Drag and drop images or videos here</div>
                                        <div class="text-muted fs-12">or click to browse from your device</div>
                                        <div class="text-muted fs-11 mt-1">Images up to 5 MB · Videos up to 10 MB</div>
                                    </div>
                                </div>
                                <div class="form-text" id="vm-upload-summary">No files selected yet.</div>
                                <div class="invalid-feedback d-block" id="error-vm_files"></div>
                            </div>
                            <div class="col-12">
                                <div id="existing-files-block" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold fs-13">Existing Files</div>
                                        <small class="text-muted">Removing here deletes the file when you save.</small>
                                    </div>
                                    <div id="existing-files-preview" class="vm-gallery"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div id="new-files-block" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold fs-13">New Uploads</div>
                                        <small class="text-muted">These files will be added when you save.</small>
                                    </div>
                                    <div id="new-files-preview" class="vm-gallery"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-white" id="btn-save">
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">VM Issue Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-4">
                        <tr><th width="28%">Store</th><td id="view-store"></td></tr>
                        <tr><th>Asset</th><td id="view-asset"></td></tr>
                        <tr><th>Fix Status</th><td id="view-issue-fix-status"></td></tr>
                        <tr><th>Status</th><td id="view-status"></td></tr>
                        <tr><th>Issue Details</th><td id="view-issue-text"></td></tr>
                        <tr><th>Created</th><td id="view-created-at"></td></tr>
                        <tr><th>Updated</th><td id="view-updated-at"></td></tr>
                    </table>
                    <div class="fw-semibold mb-2">Files</div>
                    <div id="view-files-preview" class="vm-gallery"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-4 pb-2">
                    <div class="mb-3"><i class="bi bi-trash text-danger" style="font-size:3rem;"></i></div>
                    <h6>Delete VM Issue</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-vm-name"></strong>?</p>
                    <input type="hidden" id="delete-vm-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">
                        <span class="btn-text">Yes, Delete</span>
                        <span class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .vm-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
        .vm-media-card { position: relative; border: 1px solid var(--default-border); border-radius: 1rem; overflow: hidden; background: #fff; }
        .vm-media-frame { width: 100%; height: 140px; background: rgb(var(--light-rgb)); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .vm-media-frame img, .vm-media-frame video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .vm-media-file { color: var(--text-muted); text-align: center; padding: 0 12px; }
        .vm-media-file i { font-size: 2rem; display: block; margin-bottom: 4px; }
        .vm-media-body { padding: 10px 12px 12px; }
        .vm-media-name { font-size: .78rem; font-weight: 600; line-height: 1.35; word-break: break-word; }
        .vm-media-meta { font-size: .72rem; color: var(--text-muted); margin-top: 4px; }
        .vm-media-remove { position: absolute; top: 10px; right: 10px; z-index: 2; }
        .vm-empty-state { min-height: 120px; border: 1px dashed var(--default-border); border-radius: 1rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; text-align: center; padding: 16px; }
        .vm-upload-zone { border: 1.5px dashed var(--default-border); border-radius: 1rem; background: rgba(var(--light-rgb),.45); cursor: pointer; transition: border-color .2s, background .2s; }
        .vm-upload-zone:hover, .vm-upload-zone:focus { border-color: rgba(var(--primary-rgb),.55); background: rgba(var(--primary-rgb),.04); outline: none; }
        .vm-upload-zone.drag-over { border-color: rgba(var(--primary-rgb),.95); background: rgba(var(--primary-rgb),.08); }
        .vm-upload-zone.is-invalid { border-color: #dc3545; }
        .vm-upload-zone__inner { min-height: 140px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 6px; padding: 20px; }
        .vm-upload-zone__icon { font-size: 2rem; color: rgb(var(--primary-rgb)); }
        .vm-issue-copy { white-space: normal; line-height: 1.45; max-width: 260px; }
        .inst-photo-thumb { width: 56px; height: 40px; border-radius: .4rem; overflow: hidden; display: inline-block; }
        .inst-photo-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .inst-no-photos { font-size: .78rem; color: var(--text-muted); }
        .select2-container--bootstrap-5 .select2-selection.is-invalid { border-color: #dc3545 !important; }
        .btn-action { background: none; border: 1px solid var(--default-border); border-radius: .4rem; padding: 4px 7px; font-size: .85rem; color: var(--text-muted); transition: background .15s, color .15s; cursor: pointer; }
        .btn-action:hover { background: rgb(var(--light-rgb)); color: var(--default-text-color); }
        .btn-action.text-danger:hover { background: rgba(220,53,69,.1); color: #dc3545; }
        /* DataTables - pagination & info */
        #vm-issues-table_wrapper .dataTables_info {
            font-size: .83rem;
            color: var(--text-muted);
            line-height: 30px;
        }
        #vm-issues-table_wrapper .pagination {
            margin: 0;
            gap: 3px;
        }
        #vm-issues-table_wrapper .pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: .4rem !important;
            border: 1px solid var(--default-border);
            background: transparent;
            color: var(--default-text-color);
            font-size: .82rem;
            line-height: 1;
            box-shadow: none;
        }
        #vm-issues-table_wrapper .pagination .page-item .page-link:hover {
            background: rgb(var(--light-rgb));
            border-color: var(--default-border);
            color: var(--default-text-color);
        }
        #vm-issues-table_wrapper .pagination .page-item.active .page-link {
            background: rgb(var(--primary-rgb));
            border-color: rgb(var(--primary-rgb));
            color: #fff;
        }
        #vm-issues-table_wrapper .pagination .page-item.disabled .page-link {
            opacity: .4;
            background: transparent;
            border-color: var(--default-border);
            color: var(--default-text-color);
        }
        #vm-issues-table_wrapper .pagination .page-item .page-link i {
            font-size: 1rem;
            line-height: 1;
        }
        #vm-issues-table_wrapper .dataTables_processing {
            font-size: .83rem;
            color: var(--text-muted);
            padding: 12px;
        }
        #vm-issues-table_wrapper .vm-dt-bottom {
            border-top: 1px solid var(--default-border);
        }
        /*custom css*/
        #filter-store + .select2-container .select2-selection--single {
            height: 31px !important;
            min-height: 31px !important;
            display: flex !important;
            align-items: center !important;
        }
        #filter-store + .select2-container .select2-selection__rendered {
            line-height: normal !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            flex: 1;
        }
        #filter-store + .select2-container .select2-selection__arrow {
            height: 31px !important;
            top: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    <script src="{{ asset('/') }}backend/build/cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/') }}backend/build/cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
    $(function () {
        const issueEditor = CKEDITOR.replace('issue_text', { versionCheck: false });

        const vmModal     = new bootstrap.Modal(document.getElementById('vmModal'));
        const viewModal   = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const vmUploadZone  = document.getElementById('vmUploadZone');
        const vmFilesInput  = document.getElementById('vm_files');

        const assetDirectory    = @json($assetDirectory);
        const apiUrl = (id = '') => base_url + 'visual-merchandising' + (id ? '/' + id : '');
        const maxImageFileSize  = 5  * 1024 * 1024;
        const maxVideoFileSize  = 10 * 1024 * 1024;

        let selectedFiles          = [];
        let currentExistingFiles   = [];
        let removedExistingFileIds = new Set();

        /* -------- Toasts -------- */
        function showToast(message, type = 'success') {
            const $t = $(`
                <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:99999" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`).appendTo('body');
            setTimeout(() => $t.remove(), 3500);
        }

        /* -------- Button loading -------- */
        function setBtnLoading($btn, loading, loadingText = 'Save') {
            $btn.prop('disabled', loading);
            $btn.find('.btn-text').text(loading ? loadingText : $btn.data('default-text'));
            $btn.find('.spinner-border').toggleClass('d-none', !loading);
        }

        /* -------- Helpers -------- */
        function formatDate(v) {
            if (!v) return 'N/A';
            const d = new Date(v);
            return isNaN(d) ? v : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
        function escapeHtml(v) { return $('<div>').text(v ?? '').html(); }
        function stripTags(v) { return $('<div>').html(v ?? '').text(); }
        function nl2br(v) { return escapeHtml(v).replace(/\n/g, '<br>'); }
        function truncate(v, n = 80) { const t = String(v ?? ''); return t.length <= n ? t : t.slice(0, n - 1).trimEnd() + '…'; }

        function filePreviewType(fileType, fileName = '') {
            const t = String(fileType || '').toLowerCase();
            const n = String(fileName || '').toLowerCase();
            if (t.startsWith('image/') || /\.(jpeg|jpg|png|gif|svg|webp)$/i.test(n)) return 'image';
            if (t.startsWith('video/') || /\.(mp4|mov|avi|mkv|webm)$/i.test(n)) return 'video';
            return 'file';
        }

        function fixStatusBadge(status) {
            const s = String(status || 'pending').toLowerCase();
            const cls = { pending: 'secondary', reviewed: 'info', assigned: 'primary', processing: 'warning', solved: 'success' };
            return `<span class="badge bg-${cls[s] || 'secondary'}-transparent">${escapeHtml(s.charAt(0).toUpperCase()+s.slice(1))}</span>`;
        }

        function statusBadge(v) {
            return Number(v) === 1
                ? '<span class="badge bg-success-transparent">Active</span>'
                : '<span class="badge bg-danger-transparent">Inactive</span>';
        }

        /* -------- Stats -------- */
        function refreshStats(data) {
            const total      = data.length;
            const pending    = data.filter(r => r.issue_fix_status === 'pending').length;
            const inProgress = data.filter(r => ['reviewed','assigned','processing'].includes(r.issue_fix_status)).length;
            const solved     = data.filter(r => r.issue_fix_status === 'solved').length;
            const rate       = total > 0 ? Math.round(solved / total * 100) : 0;
            $('#stat-total').text(total);
            $('#stat-pending').text(pending);
            $('#stat-inprogress').text(inProgress);
            $('#stat-solved').text(solved);
            $('#stat-rate').text(rate + '%');
        }

        /* -------- Validation -------- */
        function clearErrors() {
            $('#vmForm .is-invalid').removeClass('is-invalid');
            $('#vmForm .invalid-feedback').text('');
            $('#vmUploadZone').removeClass('is-invalid');
            $(issueEditor.container.$).removeClass('is-invalid');
            $('#vmForm .select2-hidden-accessible').each(function () {
                $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            });
        }

        function markFieldInvalid(fieldId, message) {
            if (fieldId === 'vm_files') {
                $('#vm_files').addClass('is-invalid');
                $('#vmUploadZone').addClass('is-invalid');
                $('#error-vm_files').text(message);
                return;
            }
            const $f = $('#' + fieldId);
            $f.addClass('is-invalid');
            if (fieldId === 'issue_text') $(issueEditor.container.$).addClass('is-invalid');
            if ($f.hasClass('select2-hidden-accessible')) $f.next('.select2-container').find('.select2-selection').addClass('is-invalid');
            $('#error-' + fieldId).text(message);
        }

        function normalizeErrorField(field) {
            return field === 'remove_file_ids' || field.startsWith('vm_files') ? 'vm_files' : field;
        }

        /* -------- Asset filter -------- */
        function assetIsAvailableForStore(asset, storeId) {
            const sid = Number(storeId || 0);
            if (!sid) return false;
            const assigned = Array.isArray(asset.assignment_store_ids) ? asset.assignment_store_ids.map(Number) : [];
            return Number(asset.store_id || 0) === sid
                || Number(asset.is_common_asset || 0) === 1
                || assigned.includes(sid);
        }

        function buildAssetLabel(a) {
            const parts = [a.asset_code || 'N/A', a.asset_type_name || '', Number(a.is_common_asset || 0) === 1 ? 'Common' : ''].filter(Boolean);
            return `${a.name}${parts.length ? ' (' + parts.join(' | ') + ')' : ''}`;
        }

        function populateAssetOptions(selectedId = '') {
            const storeId = $('#store_id').val();
            let avail = assetDirectory.filter(a => assetIsAvailableForStore(a, storeId));
            const selId = Number(selectedId || 0);
            if (selId > 0 && !avail.some(a => Number(a.id) === selId)) {
                const found = assetDirectory.find(a => Number(a.id) === selId);
                if (found) avail = [found, ...avail];
            }
            let opts = '<option value="">' + (storeId ? 'Select asset' : 'Select store first') + '</option>';
            avail.forEach(a => { opts += `<option value="${a.id}">${escapeHtml(buildAssetLabel(a))}</option>`; });
            $('#asset_id').html(opts).val(selId > 0 ? String(selId) : '').trigger('change');
        }

        $('#store_id').on('change', function () { populateAssetOptions(); });

        /* -------- Form reset -------- */
        function resetForm() {
            $('#vmForm')[0].reset();
            $('#vm_id').val('');
            $('#vmModalLabel').text('Add VM Issue');
            $('#btn-save').data('default-text', 'Save');
            $('#btn-save .btn-text').text('Save');
            $('#status').val('1');
            $('#issue_fix_status').val('pending');
            $('#store_id').val('').trigger('change');
            $('#asset_id').html('<option value="">Select store first</option>').trigger('change');
            selectedFiles = []; currentExistingFiles = []; removedExistingFileIds = new Set();
            issueEditor.setData('');
            syncFileInput(); renderExistingFilePreviews(); renderSelectedFilePreviews(); clearErrors();
        }

        function openFormModal(mode) {
            resetForm();
            const isEdit = mode === 'edit';
            $('#vmModalLabel').text(isEdit ? 'Edit VM Issue' : 'Add VM Issue');
            $('#btn-save').data('default-text', isEdit ? 'Update' : 'Save');
            $('#btn-save .btn-text').text(isEdit ? 'Update' : 'Save');
        }

        /* -------- File handling -------- */
        function syncFileInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            vmFilesInput.files = dt.files;
        }

        function validateUploadFile(file) {
            const pt = filePreviewType(file.type, file.name);
            if (pt === 'file') return { valid: false, message: 'Only image or video files are allowed.' };
            const max = pt === 'image' ? maxImageFileSize : maxVideoFileSize;
            if ((file.size || 0) > max) return { valid: false, message: pt === 'image' ? 'Image files must not exceed 5 MB.' : 'Video files must not exceed 10 MB.' };
            return { valid: true };
        }

        function fileMetaLabel(fileType, fileName = '', fileSize = null) {
            const pt = filePreviewType(fileType, fileName);
            const sz = typeof fileSize === 'number' ? `${(fileSize/1024/1024).toFixed(fileSize >= 1024*1024 ? 1 : 2)} MB` : '';
            return [pt === 'image' ? 'Image' : (pt === 'video' ? 'Video' : 'File'), sz].filter(Boolean).join(' | ');
        }

        function mediaCardMarkup(file, options = {}) {
            const allowRemove = options.allowRemove === true;
            const removeHandler = options.removeHandler || '';
            const pt = file.preview_type || filePreviewType(file.file_type, file.file_name || file.name);
            const fileName = file.file_name || file.name || 'Uploaded file';
            const fileUrl = file.file_url || file.url || '';
            const meta = fileMetaLabel(file.file_type, fileName, file.size || null);

            let preview = `<div class="vm-media-file"><i class="bi bi-paperclip"></i><div>Preview unavailable</div></div>`;
            if (pt === 'image' && fileUrl) preview = `<img src="${fileUrl}" alt="${escapeHtml(fileName)}">`;
            else if (pt === 'video' && fileUrl) preview = `<video controls muted playsinline preload="metadata"><source src="${fileUrl}" ${file.file_type ? `type="${escapeHtml(file.file_type)}"` : ''}></video>`;

            return `
                <div class="vm-media-card">
                    ${allowRemove ? `<button type="button" class="btn btn-sm btn-danger vm-media-remove" ${removeHandler}><i class="bi bi-trash"></i></button>` : ''}
                    <div class="vm-media-frame">${preview}</div>
                    <div class="vm-media-body">
                        <div class="vm-media-name">${escapeHtml(fileName)}</div>
                        <div class="vm-media-meta">${escapeHtml(meta || 'Uploaded file')}</div>
                        ${fileUrl ? `<a href="${fileUrl}" target="_blank" rel="noopener" class="fs-12">Open file</a>` : ''}
                    </div>
                </div>`;
        }

        function renderExistingFilePreviews() {
            const visible = currentExistingFiles.filter(f => !removedExistingFileIds.has(Number(f.id)));
            $('#existing-files-block').toggleClass('d-none', !visible.length);
            $('#existing-files-preview').html(visible.length
                ? visible.map(f => mediaCardMarkup(f, { allowRemove: true, removeHandler: `data-existing-id="${f.id}"` })).join('')
                : '');
        }

        function updateUploadSummary() {
            if (!selectedFiles.length) { $('#vm-upload-summary').text('No files selected yet.'); return; }
            const totalMb = (selectedFiles.reduce((s, f) => s + (f.size || 0), 0) / 1024 / 1024).toFixed(2);
            $('#vm-upload-summary').text(`${selectedFiles.length} file${selectedFiles.length === 1 ? '' : 's'} selected, total ${totalMb} MB.`);
        }

        function renderSelectedFilePreviews() {
            $('#new-files-block').toggleClass('d-none', !selectedFiles.length);
            updateUploadSummary();
            if (!selectedFiles.length) { $('#new-files-preview').empty(); return; }
            $('#new-files-preview').html(selectedFiles.map((f, i) => mediaCardMarkup({
                name: f.name, file_name: f.name, file_type: f.type, file_url: URL.createObjectURL(f), size: f.size,
            }, { allowRemove: true, removeHandler: `data-new-index="${i}"` })).join(''));
        }

        function handleIncomingFiles(fileList) {
            $('#error-vm_files').text(''); $('#vm_files').removeClass('is-invalid'); $('#vmUploadZone').removeClass('is-invalid');
            const errors = [];
            Array.from(fileList || []).forEach(f => {
                const v = validateUploadFile(f);
                if (v.valid) selectedFiles.push(f);
                else if (!errors.includes(v.message)) errors.push(v.message);
            });
            syncFileInput(); renderSelectedFilePreviews();
            if (errors.length) markFieldInvalid('vm_files', errors[0]);
        }

        $('#vm_files').on('change', function (e) { handleIncomingFiles(e.target.files); });
        vmUploadZone.addEventListener('click', () => vmFilesInput.click());
        vmUploadZone.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); vmFilesInput.click(); }});
        vmUploadZone.addEventListener('dragover', e => { e.preventDefault(); vmUploadZone.classList.add('drag-over'); });
        vmUploadZone.addEventListener('dragleave', e => { if (!e.relatedTarget || !vmUploadZone.contains(e.relatedTarget)) vmUploadZone.classList.remove('drag-over'); });
        vmUploadZone.addEventListener('drop', e => { e.preventDefault(); vmUploadZone.classList.remove('drag-over'); handleIncomingFiles(e.dataTransfer?.files); });

        $(document).on('click', '[data-existing-id]', function () { removedExistingFileIds.add(Number($(this).data('existing-id'))); renderExistingFilePreviews(); });
        $(document).on('click', '[data-new-index]', function () { selectedFiles.splice(Number($(this).data('new-index')), 1); syncFileInput(); renderSelectedFilePreviews(); });

        /* -------- Load edit data -------- */
        function loadEditData(data) {
            $('#vm_id').val(data.id);
            $('#store_id').val(String(data.store_id || '')).trigger('change');
            populateAssetOptions(data.asset_id || '');
            $('#issue_fix_status').val(data.issue_fix_status || 'pending');
            $('#status').val(Number(data.status) === 1 ? '1' : '0');
            issueEditor.setData(data.issue_text || '');
            currentExistingFiles = Array.isArray(data.visual_merchandising_files) ? data.visual_merchandising_files : [];
            removedExistingFileIds = new Set();
            renderExistingFilePreviews(); renderSelectedFilePreviews();
        }

        /* -------- DataTable init -------- */
        const vmTable = $('#vm-issues-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: '{{ route('vm.vm-issues.datatable') }}',
                data: function (d) {
                    d.fix_status = $('#filter-status').val();
                    d.store_id   = $('#filter-store').val();
                    d.search_text = $('#filter-search').val();
                }
            },
            columns: [
                { data: 'store_name',      name: 'store_name', render: function(data, type, row) {
                    return `<div class="inst-store-name">${data}</div><div class="inst-store-meta">${row.store_code || ''}</div>`;
                }},
                { data: 'asset_name',      name: 'asset_name', render: function(data, type, row) {
                    return `<div class="fw-semibold" style="font-size:.85rem;">${data}</div><div class="inst-store-meta">${row.asset_type || ''}${row.asset_code ? ' · '+row.asset_code : ''}</div>`;
                }},
                { data: 'issue_preview',   name: 'issue_preview', render: function(data) {
                    return `<div class="vm-issue-copy">${data}</div>`;
                }},
                { data: 'fix_status_badge',name: 'fix_status_badge', orderable: false },
                { data: 'file_preview',    name: 'file_preview',    orderable: false },
                { data: 'created_at',      name: 'created_at', render: function(data) {
                    return `<div class="inst-date">${data ? new Date(data).toLocaleDateString('en-GB', {day:'2-digit',month:'short',year:'numeric'}) : ''}</div>`;
                }},
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ],
            order: [[5, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: 'rt<"vm-dt-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-2"ip>',
            pagingType: 'simple_numbers',
            language: {
                emptyTable: 'No VM issues reported yet.',
                processing: 'Loading...',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries found',
                paginate: {
                    previous: '<i class="ri-arrow-left-s-line"></i>',
                    next: '<i class="ri-arrow-right-s-line"></i>'
                }
            },
            drawCallback: function(settings) {
                const api = this.api();
                const data = api.data().toArray();
                refreshStats(data);
            }
        });

        function reloadTable() { vmTable.ajax.reload(null, false); }

        /* -------- Sync export URL with active filters -------- */
        const exportBase = '{{ route('vm.vm-issues.export') }}';
        function syncExportUrl() {
            const params = new URLSearchParams();
            const fixStatus = $('#filter-status').val();
            const storeId   = $('#filter-store').val();
            if (fixStatus) params.set('fix_status', fixStatus);
            if (storeId)   params.set('store_id',   storeId);
            const query = params.toString();
            $('#btn-export').attr('href', exportBase + (query ? '?' + query : ''));
        }
        syncExportUrl();
        $('#filter-status, #filter-store').on('change', syncExportUrl);

        /* -------- Filters → reload table -------- */
        let filterTimer;
        $('#filter-search').on('input', function () {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(reloadTable, 400);
        });
        $('#filter-status, #filter-store').on('change', reloadTable);

        /* -------- Open Add modal -------- */
        $('#btn-add-vm').on('click', function () { openFormModal('add'); vmModal.show(); });

        /* -------- Edit button -------- */
        $(document).on('click', '.btn-edit-vm', function () {
            openFormModal('edit');
            $.get(apiUrl($(this).data('id')) + '/edit')
                .done(function (data) {
                    loadEditData(data);
                    $('#vmModalLabel').text('Edit VM Issue');
                    $('#btn-save').data('default-text', 'Update');
                    $('#btn-save .btn-text').text('Update');
                    vmModal.show();
                })
                .fail(function () { showToast('Failed to load VM issue data.', 'danger'); });
        });

        /* -------- View button -------- */
        $(document).on('click', '.btn-view-vm', function () {
            $.get(apiUrl($(this).data('id')))
                .done(function (data) {
                    const storeLabel = data.store ? `${data.store.title}${data.store.code ? ` (${data.store.code})` : ''}` : 'N/A';
                    const assetLabel = data.asset ? `${data.asset.name}${data.asset.asset_code ? ` (${data.asset.asset_code})` : ''}${data.asset.asset_type?.name ? ` - ${data.asset.asset_type.name}` : ''}` : 'N/A';
                    $('#view-store').text(storeLabel);
                    $('#view-asset').text(assetLabel);
                    $('#view-issue-fix-status').html(fixStatusBadge(data.issue_fix_status));
                    $('#view-status').html(statusBadge(data.status));
                    $('#view-issue-text').html(data.issue_text || 'N/A');
                    $('#view-created-at').text(formatDate(data.created_at));
                    $('#view-updated-at').text(formatDate(data.updated_at));
                    const files = Array.isArray(data.visual_merchandising_files) ? data.visual_merchandising_files : [];
                    $('#view-files-preview').html(files.length
                        ? files.map(f => mediaCardMarkup(f)).join('')
                        : `<div class="vm-empty-state">No files uploaded for this issue.</div>`);
                    viewModal.show();
                })
                .fail(function () { showToast('Failed to load issue details.', 'danger'); });
        });

        /* -------- Delete button -------- */
        $(document).on('click', '.btn-delete-vm', function () {
            $('#delete-vm-id').val($(this).data('id'));
            $('#delete-vm-name').text($(this).data('name'));
            deleteModal.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const vmId = $('#delete-vm-id').val();
            const $btn = $(this);
            $btn.prop('disabled', true);
            $btn.find('.btn-text').text('Deleting…');
            $btn.find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: apiUrl(vmId), type: 'DELETE',
                success: function (res) {
                    reloadTable();
                    deleteModal.hide();
                    showToast(res.message || 'Deleted successfully.', 'success');
                },
                error: function () { showToast('Failed to delete the VM issue.', 'danger'); },
                complete: function () {
                    $btn.prop('disabled', false);
                    $btn.find('.btn-text').text('Yes, Delete');
                    $btn.find('.spinner-border').addClass('d-none');
                }
            });
        });

        /* -------- Form submit -------- */
        $('#vmForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();
            issueEditor.updateElement();
            syncFileInput();

            const vmId = $('#vm_id').val();
            const formData = new FormData(this);
            removedExistingFileIds.forEach(id => formData.append('remove_file_ids[]', id));
            if (vmId) formData.append('_method', 'PUT');

            setBtnLoading($('#btn-save'), true, vmId ? 'Updating…' : 'Saving…');

            $.ajax({
                url: apiUrl(vmId || ''), type: 'POST', data: formData, processData: false, contentType: false,
                success: function (res) {
                    reloadTable();
                    vmModal.hide();
                    showToast(res.message || 'Saved successfully.', 'success');
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        $.each(xhr.responseJSON.errors, (field, msgs) => markFieldInvalid(normalizeErrorField(field), msgs[0]));
                        return;
                    }
                    showToast(xhr.responseJSON?.message || 'Something went wrong.', 'danger');
                },
                complete: function () { setBtnLoading($('#btn-save'), false); }
            });
        });

        $('#vmModal').on('hidden.bs.modal', resetForm);

        $('#btn-save').data('default-text', 'Save');

        $(document).on('click', '.change-vm-status', function () {
            sendAjaxRequest(`vm/change-vm-issue-status/${$(this).data('id')}/${$(this).data('fix-status')}`, 'POST', {}).then(function (response) {
                showToast(response.message, response.success ? 'success' : 'danger');
                if (response.success) reloadTable();
            })
        })
    });
    </script>
@endpush
