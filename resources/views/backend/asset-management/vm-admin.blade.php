@extends('backend.master')

@section('title', 'Visual Merchandising')

@php
    $hasDependencies = isset($stores, $assets) && $stores->isNotEmpty() && $assets->isNotEmpty();
    $assetDirectory = $assets->map(function ($asset) {
        return [
            'id' => $asset->id,
            'name' => $asset->name,
            'asset_code' => $asset->asset_code,
            'store_id' => $asset->store_id,
            'status' => (int) $asset->status,
            'is_common_asset' => (int) $asset->is_common_asset,
            'asset_type_name' => $asset->assetType?->name,
            'store_name' => $asset->store?->title,
            'assignment_store_ids' => $asset->assignAssetToStores
                ->pluck('store_id')
                ->filter()
                ->map(fn ($storeId) => (int) $storeId)
                ->unique()
                ->values()
                ->all(),
        ];
    })->values();
@endphp

@section('body')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-xl-10 col-lg-11 col-md-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title mb-1">Visual Merchandising Issue Management</div>
                            <p class="text-muted fs-12 mb-0">Track VM issues by store and asset, with image or video evidence on the same form.</p>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm btn-primary btn-wave"
                            id="btn-add-vm"
                            @disabled(!$hasDependencies)
                            title="{{ $hasDependencies ? 'Add visual merchandising issue' : 'Create stores and assets first' }}"
                        >
                            <i class="ri-add-line me-1"></i> Add VM Issue
                        </button>
                    </div>
                    <div class="card-body">
                        @unless($hasDependencies)
                            <div class="alert alert-warning mb-3">
                                At least one store and one asset are required before creating a visual merchandising issue.
                            </div>
                        @endunless

                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100 align-middle">
                                <thead>
                                    <tr>
                                        <th width="45">#</th>
                                        <th>Asset</th>
                                        <th>Store</th>
                                        <th>Issue</th>
                                        <th>Fix Status</th>
                                        <th>Files</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th width="110">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($visualMerchandisings as $visualMerchandising)
                                        @php
                                            $firstFile = $visualMerchandising->visualMerchandisingFiles->first();
                                            $firstFileType = strtolower((string) ($firstFile?->file_type ?? ''));
                                            $firstFileExtension = strtolower(pathinfo((string) ($firstFile?->file_path ?? ''), PATHINFO_EXTENSION));
                                            $isImagePreview = $firstFile && (
                                                \Illuminate\Support\Str::startsWith($firstFileType, 'image/')
                                                || in_array($firstFileExtension, ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'], true)
                                            );
                                            $isVideoPreview = $firstFile && (
                                                \Illuminate\Support\Str::startsWith($firstFileType, 'video/')
                                                || in_array($firstFileExtension, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)
                                            );
                                            $fixStatusClass = match ($visualMerchandising->issue_fix_status) {
                                                'reviewed' => 'info',
                                                'assigned' => 'primary',
                                                'processing' => 'warning',
                                                'solved' => 'success',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="vm-thumb">
                                                        @if($firstFile)
                                                            @if($isImagePreview)
                                                                <img src="{{ asset($firstFile->file_path) }}" alt="{{ $visualMerchandising->asset?->name ?? 'VM File' }}" class="vm-thumb-media">
                                                            @elseif($isVideoPreview)
                                                                <video class="vm-thumb-media" muted playsinline preload="metadata">
                                                                    <source src="{{ asset($firstFile->file_path) }}" @if($firstFile->file_type) type="{{ $firstFile->file_type }}" @endif>
                                                                </video>
                                                            @else
                                                                <div class="vm-thumb-placeholder"><i class="ri-attachment-2"></i></div>
                                                            @endif
                                                        @else
                                                            <div class="vm-thumb-placeholder"><i class="ri-image-line"></i></div>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="fw-semibold text-wrap">{{ $visualMerchandising->asset?->name ?? 'N/A' }}</div>
                                                        <div class="text-muted fs-12">{{ $visualMerchandising->asset?->asset_code ?? 'N/A' }}</div>
                                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                                            @if($visualMerchandising->asset?->assetType)
                                                                <span class="badge bg-light text-dark">{{ $visualMerchandising->asset->assetType->name }}</span>
                                                            @endif
                                                            @if((int) ($visualMerchandising->asset?->is_common_asset ?? 0) === 1)
                                                                <span class="badge bg-primary-transparent">Common Asset</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-wrap">{{ $visualMerchandising->store?->title ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $visualMerchandising->store?->code ?? '' }}</small>
                                            </td>
                                            <td>
                                                <div class="vm-issue-copy">{{ \Illuminate\Support\Str::limit($visualMerchandising->issue_text, 110) }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $fixStatusClass }}-transparent">{{ ucfirst($visualMerchandising->issue_fix_status) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $visualMerchandising->visualMerchandisingFiles->count() }} file{{ $visualMerchandising->visualMerchandisingFiles->count() === 1 ? '' : 's' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if((int) $visualMerchandising->status === 1)
                                                    <span class="badge bg-success-transparent">Active</span>
                                                @else
                                                    <span class="badge bg-danger-transparent">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ optional($visualMerchandising->created_at)->format('d M Y') }}</td>
                                            <td>
                                                <div class="btn-list">
                                                    <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $visualMerchandising->id }}" title="View">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $visualMerchandising->id }}" title="Edit">
                                                        <i class="ri-edit-box-line"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $visualMerchandising->id }}" data-name="{{ $visualMerchandising->asset?->name ?? 'VM Issue' }}" title="Delete">
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
    <div class="modal fade" id="vmModal" tabindex="-1" aria-labelledby="vmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold" id="vmModalLabel">Add Visual Merchandising Issue</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="vmForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="vm_id" value="">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="store_id" class="form-label">Store <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="store_id" name="store_id">
                                    <option value="">Select store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }}{{ $store->code ? ' (' . $store->code . ')' : '' }}</option>
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
                            <div class="col-md-6 d-none">
                                <label for="issue_fix_status" class="form-label">Issue Fix Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="issue_fix_status" name="issue_fix_status">
                                    @foreach($issueFixStatuses as $issueFixStatus)
                                        <option value="{{ $issueFixStatus }}">{{ ucfirst($issueFixStatus) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-issue_fix_status"></div>
                            </div>
                            <div class="col-md-6 d-none">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="error-status"></div>
                            </div>
                            <div class="col-12">
                                <label for="issue_text" class="form-label">Issue Details <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="issue_text" name="issue_text" rows="5" placeholder="Describe the visual merchandising issue, what is wrong, and any context the reviewer should know."></textarea>
                                <div class="invalid-feedback" id="error-issue_text"></div>
                            </div>
                            <div class="col-12">
                                <label for="vm_files" class="form-label">Upload Evidence</label>
                                <div class="vm-upload-zone" id="vmUploadZone" tabindex="0" role="button" aria-controls="vm_files" aria-label="Upload visual merchandising evidence">
                                    <input type="file" class="d-none" id="vm_files" name="vm_files[]" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm" multiple>
                                    <div class="vm-upload-zone__inner">
                                        <i class="ri-upload-cloud-2-line vm-upload-zone__icon"></i>
                                        <div class="fw-semibold">Drag and drop images or videos here</div>
                                        <div class="text-muted fs-12">or click to browse from your device</div>
                                        <div class="text-muted fs-11 mt-1">Images up to 5 MB each. Videos up to 10 MB each.</div>
                                    </div>
                                </div>
                                <div class="form-text" id="vm-upload-summary">No files selected yet.</div>
                                <div class="invalid-feedback d-block" id="error-vm_files"></div>
                            </div>
                            <div class="col-12">
                                <div id="existing-files-block" class="d-none">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold fs-13">Existing Files</div>
                                        <small class="text-muted">Removing here deletes the file from this issue when you save.</small>
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Visual Merchandising Issue Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered mb-4">
                        <tr><th width="28%">Store</th><td id="view-store"></td></tr>
                        <tr><th>Asset</th><td id="view-asset"></td></tr>
                        <tr><th>Issue Fix Status</th><td id="view-issue-fix-status"></td></tr>
                        <tr><th>Status</th><td id="view-status"></td></tr>
                        <tr><th>Issue Details</th><td id="view-issue-text"></td></tr>
                        <tr><th>Created</th><td id="view-created-at"></td></tr>
                        <tr><th>Updated</th><td id="view-updated-at"></td></tr>
                    </table>
                    <div>
                        <div class="fw-semibold mb-2">Files</div>
                        <div id="view-files-preview" class="vm-gallery"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-4 pb-2">
                    <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
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
        .vm-thumb { width: 72px; height: 72px; border-radius: .9rem; overflow: hidden; background: rgb(var(--light-rgb)); border: 1px solid var(--default-border); flex-shrink: 0; }
        .vm-thumb-media { width: 100%; height: 100%; object-fit: cover; display: block; background: #000; }
        .vm-thumb-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.35rem; }
        .vm-issue-copy { white-space: normal; line-height: 1.45; min-width: 220px; }
        .vm-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
        .vm-media-card { position: relative; border: 1px solid var(--default-border); border-radius: 1rem; overflow: hidden; background: #fff; }
        .vm-media-frame { width: 100%; height: 140px; background: rgb(var(--light-rgb)); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .vm-media-frame img, .vm-media-frame video { width: 100%; height: 100%; object-fit: cover; display: block; background: #000; }
        .vm-media-file { color: var(--text-muted); text-align: center; padding: 0 12px; }
        .vm-media-file i { font-size: 2rem; display: block; margin-bottom: 4px; }
        .vm-media-body { padding: 10px 12px 12px; }
        .vm-media-name { font-size: .78rem; font-weight: 600; line-height: 1.35; word-break: break-word; }
        .vm-media-meta { font-size: .72rem; color: var(--text-muted); margin-top: 4px; }
        .vm-media-remove { position: absolute; top: 10px; right: 10px; z-index: 2; }
        .vm-empty-state { min-height: 120px; border: 1px dashed var(--default-border); border-radius: 1rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; text-align: center; padding: 16px; background: rgba(var(--light-rgb), .5); }
        .select2-container--bootstrap-5 .select2-selection.is-invalid { border-color: #dc3545 !important; }
        .vm-upload-zone { border: 1.5px dashed var(--default-border); border-radius: 1rem; background: rgba(var(--light-rgb), .45); cursor: pointer; transition: border-color .2s ease, background .2s ease, transform .2s ease, box-shadow .2s ease; }
        .vm-upload-zone:hover, .vm-upload-zone:focus { border-color: rgba(var(--primary-rgb), .55); background: rgba(var(--primary-rgb), .04); box-shadow: 0 0 0 .2rem rgba(var(--primary-rgb), .08); outline: none; }
        .vm-upload-zone.drag-over { border-color: rgba(var(--primary-rgb), .95); background: rgba(var(--primary-rgb), .08); transform: translateY(-1px); }
        .vm-upload-zone.is-invalid { border-color: #dc3545; background: rgba(220, 53, 69, .04); }
        .vm-upload-zone__inner { min-height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 6px; padding: 22px; }
        .vm-upload-zone__icon { font-size: 2.15rem; color: rgb(var(--primary-rgb)); }
    </style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        $(function () {
            const issueEditor = CKEDITOR.replace( 'issue_text' , {
                versionCheck: false
            });

            const vmModal = new bootstrap.Modal(document.getElementById('vmModal'));
            const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const vmUploadZone = document.getElementById('vmUploadZone');
            const vmFilesInput = document.getElementById('vm_files');

            const assetDirectory = @json($assetDirectory);
            const apiUrl = (id = '') => base_url + 'visual-merchandising' + (id ? '/' + id : '');
            const maxImageFileSize = 5 * 1024 * 1024;
            const maxVideoFileSize = 10 * 1024 * 1024;

            let selectedFiles = [];
            let currentExistingFiles = [];
            let removedExistingFileIds = new Set();

            function showToast(message, type = 'success') {
                const $toast = $(`
                    <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:99999" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `).appendTo('body');

                setTimeout(() => $toast.remove(), 3500);
            }

            function setBtnLoading($btn, loading, loadingText = 'Save') {
                $btn.prop('disabled', loading);
                $btn.find('.btn-text').text(loading ? loadingText : $btn.data('default-text'));
                $btn.find('.spinner-border').toggleClass('d-none', !loading);
            }

            function formatDate(value) {
                if (!value) return 'N/A';

                const parsedDate = new Date(value);
                if (Number.isNaN(parsedDate.getTime())) return value;

                return parsedDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                });
            }

            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function nl2br(value) {
                return escapeHtml(value).replace(/\n/g, '<br>');
            }

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

                const $field = $('#' + fieldId);
                $field.addClass('is-invalid');

                 if (fieldId === 'issue_text') {
                    $(issueEditor.container.$).addClass('is-invalid');
                }

                if ($field.hasClass('select2-hidden-accessible')) {
                    $field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                }

                $('#error-' + fieldId).text(message);
            }

            function normalizeErrorField(field) {
                return field === 'remove_file_ids' || field.startsWith('vm_files') ? 'vm_files' : field;
            }

            function issueFixStatusBadge(status) {
                const normalizedStatus = String(status || 'pending').toLowerCase();
                const classes = {
                    pending: 'secondary',
                    reviewed: 'info',
                    assigned: 'primary',
                    processing: 'warning',
                    solved: 'success',
                };
                const label = normalizedStatus.charAt(0).toUpperCase() + normalizedStatus.slice(1);
                return `<span class="badge bg-${classes[normalizedStatus] || 'secondary'}-transparent">${escapeHtml(label)}</span>`;
            }

            function statusBadge(status) {
                return Number(status) === 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>';
            }

            function assetIsAvailableForStore(asset, storeId) {
                const normalizedStoreId = Number(storeId || 0);
                if (!(normalizedStoreId > 0)) return false;

                const assignedStoreIds = Array.isArray(asset.assignment_store_ids)
                    ? asset.assignment_store_ids.map(Number)
                    : [];

                return Number(asset.store_id || 0) === normalizedStoreId
                    || Number(asset.is_common_asset || 0) === 1
                    || assignedStoreIds.includes(normalizedStoreId);
            }

            function buildAssetLabel(asset) {
                const details = [
                    asset.asset_code || 'N/A',
                    asset.asset_type_name || '',
                    Number(asset.is_common_asset || 0) === 1 ? 'Common' : '',
                ].filter(Boolean);

                return `${asset.name}${details.length ? ' (' + details.join(' | ') + ')' : ''}`;
            }

            function populateAssetOptions(selectedId = '') {
                const storeId = $('#store_id').val();
                let availableAssets = assetDirectory.filter(asset => assetIsAvailableForStore(asset, storeId));
                const normalizedSelectedId = Number(selectedId || 0);

                if (normalizedSelectedId > 0 && !availableAssets.some(asset => Number(asset.id) === normalizedSelectedId)) {
                    const selectedAsset = assetDirectory.find(asset => Number(asset.id) === normalizedSelectedId);
                    if (selectedAsset) availableAssets = [selectedAsset, ...availableAssets];
                }

                let options = '<option value="">' + (storeId ? 'Select asset' : 'Select store first') + '</option>';

                availableAssets.forEach(function (asset) {
                    options += `<option value="${asset.id}">${escapeHtml(buildAssetLabel(asset))}</option>`;
                });

                $('#asset_id').html(options).val(normalizedSelectedId > 0 ? String(normalizedSelectedId) : '').trigger('change');
            }

            function resetForm() {
                $('#vmForm')[0].reset();
                $('#vm_id').val('');
                $('#vmModalLabel').text('Add Visual Merchandising Issue');
                $('#btn-save').data('default-text', 'Save');
                $('#btn-save .btn-text').text('Save');
                $('#status').val('1');
                $('#issue_fix_status').val('pending');
                $('#store_id').val('').trigger('change');
                $('#asset_id').html('<option value="">Select store first</option>').trigger('change');

                selectedFiles = [];
                currentExistingFiles = [];
                removedExistingFileIds = new Set();
                issueEditor.setData('');

                syncFileInput();
                renderExistingFilePreviews();
                renderSelectedFilePreviews();
                clearErrors();
            }

            function openFormModal(mode) {
                resetForm();
                const isEdit = mode === 'edit';
                $('#vmModalLabel').text(isEdit ? 'Edit Visual Merchandising Issue' : 'Add Visual Merchandising Issue');
                $('#btn-save').data('default-text', isEdit ? 'Update' : 'Save');
                $('#btn-save .btn-text').text(isEdit ? 'Update' : 'Save');
            }

            function syncFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(function (file) {
                    dataTransfer.items.add(file);
                });
                vmFilesInput.files = dataTransfer.files;
            }

            function filePreviewType(fileType, fileName = '') {
                const normalizedType = String(fileType || '').toLowerCase();
                const normalizedName = String(fileName || '').toLowerCase();

                if (normalizedType.startsWith('image/') || /\.(jpeg|jpg|png|gif|svg|webp)$/i.test(normalizedName)) return 'image';
                if (normalizedType.startsWith('video/') || /\.(mp4|mov|avi|mkv|webm)$/i.test(normalizedName)) return 'video';
                return 'file';
            }

            function validateUploadFile(file) {
                const previewType = filePreviewType(file.type, file.name);

                if (previewType === 'file') {
                    return { valid: false, message: 'Only image or video files are allowed (jpg, png, webp, gif, svg, mp4, mov, avi, mkv, webm).' };
                }

                const maxBytes = previewType === 'image' ? maxImageFileSize : maxVideoFileSize;
                if ((file.size || 0) > maxBytes) {
                    return {
                        valid: false,
                        message: previewType === 'image' ? 'Image files must not exceed 5 MB.' : 'Video files must not exceed 10 MB.',
                    };
                }

                return { valid: true };
            }

            function fileMetaLabel(fileType, fileName = '', fileSize = null) {
                const previewType = filePreviewType(fileType, fileName);
                const sizeLabel = typeof fileSize === 'number' ? `${(fileSize / 1024 / 1024).toFixed(fileSize >= 1024 * 1024 ? 1 : 2)} MB` : '';
                const typeLabel = previewType === 'image' ? 'Image' : (previewType === 'video' ? 'Video' : 'File');
                return [typeLabel, sizeLabel].filter(Boolean).join(' | ');
            }

            function emptyGalleryMarkup(message) {
                return `<div class="vm-empty-state">${escapeHtml(message)}</div>`;
            }

            function updateUploadSummary() {
                if (!selectedFiles.length) {
                    $('#vm-upload-summary').text('No files selected yet.');
                    return;
                }

                const totalBytes = selectedFiles.reduce(function (total, file) {
                    return total + (file.size || 0);
                }, 0);
                const totalMb = (totalBytes / 1024 / 1024).toFixed(totalBytes >= 1024 * 1024 ? 1 : 2);
                $('#vm-upload-summary').text(`${selectedFiles.length} file${selectedFiles.length === 1 ? '' : 's'} selected, total ${totalMb} MB.`);
            }

            function mediaCardMarkup(file, options = {}) {
                const allowRemove = options.allowRemove === true;
                const removeHandler = options.removeHandler || '';
                const previewType = file.preview_type || filePreviewType(file.file_type, file.file_name || file.name);
                const fileName = file.file_name || file.name || 'Uploaded file';
                const fileUrl = file.file_url || file.url || '';
                const metaLabel = fileMetaLabel(file.file_type, fileName, file.size || null);

                let previewMarkup = `
                    <div class="vm-media-file">
                        <i class="ri-attachment-2"></i>
                        <div>Preview unavailable</div>
                    </div>
                `;

                if (previewType === 'image' && fileUrl) {
                    previewMarkup = `<img src="${fileUrl}" alt="${escapeHtml(fileName)}">`;
                } else if (previewType === 'video' && fileUrl) {
                    previewMarkup = `<video controls muted playsinline preload="metadata"><source src="${fileUrl}" ${file.file_type ? `type="${escapeHtml(file.file_type)}"` : ''}></video>`;
                }

                return `
                    <div class="vm-media-card">
                        ${allowRemove ? `<button type="button" class="btn btn-sm btn-danger-light vm-media-remove" ${removeHandler}>Remove</button>` : ''}
                        <div class="vm-media-frame">${previewMarkup}</div>
                        <div class="vm-media-body">
                            <div class="vm-media-name">${escapeHtml(fileName)}</div>
                            <div class="vm-media-meta">${escapeHtml(metaLabel || 'Uploaded file')}</div>
                            ${fileUrl ? `<a href="${fileUrl}" target="_blank" rel="noopener" class="fs-12">Open file</a>` : ''}
                        </div>
                    </div>
                `;
            }

            function renderExistingFilePreviews() {
                const visibleFiles = currentExistingFiles.filter(file => !removedExistingFileIds.has(Number(file.id)));
                $('#existing-files-block').toggleClass('d-none', visibleFiles.length === 0);
                $('#existing-files-preview').html(
                    visibleFiles.length
                        ? visibleFiles.map(file => mediaCardMarkup(file, {
                            allowRemove: true,
                            removeHandler: `data-existing-id="${file.id}"`,
                        })).join('')
                        : ''
                );
            }

            function renderSelectedFilePreviews() {
                $('#new-files-block').toggleClass('d-none', selectedFiles.length === 0);
                updateUploadSummary();

                if (!selectedFiles.length) {
                    $('#new-files-preview').empty();
                    return;
                }

                const markup = selectedFiles.map(function (file, index) {
                    return mediaCardMarkup({
                        name: file.name,
                        file_name: file.name,
                        file_type: file.type,
                        file_url: URL.createObjectURL(file),
                        size: file.size,
                    }, {
                        allowRemove: true,
                        removeHandler: `data-new-index="${index}"`,
                    });
                }).join('');

                $('#new-files-preview').html(markup);
            }

            function loadEditData(data) {
                $('#vm_id').val(data.id);
                $('#store_id').val(String(data.store_id || '')).trigger('change');
                populateAssetOptions(data.asset_id || '');
                $('#issue_fix_status').val(data.issue_fix_status || 'pending');
                $('#status').val(Number(data.status) === 1 ? '1' : '0');
                $('#issue_text').val(data.issue_text || '');
                issueEditor.setData(data.issue_text || '');

                currentExistingFiles = Array.isArray(data.visual_merchandising_files) ? data.visual_merchandising_files : [];
                removedExistingFileIds = new Set();

                renderExistingFilePreviews();
                renderSelectedFilePreviews();
            }

            function renderViewFiles(files) {
                if (!files.length) {
                    $('#view-files-preview').html(emptyGalleryMarkup('No image or video files uploaded for this issue.'));
                    return;
                }

                $('#view-files-preview').html(files.map(file => mediaCardMarkup(file)).join(''));
            }

            $('#btn-save').data('default-text', 'Save');

            $('#store_id').on('change', function () {
                populateAssetOptions();
            });

            function handleIncomingFiles(fileList) {
                $('#error-vm_files').text('');
                $('#vm_files').removeClass('is-invalid');
                $('#vmUploadZone').removeClass('is-invalid');

                const incomingFiles = Array.from(fileList || []);
                const validFiles = [];
                const errors = [];

                incomingFiles.forEach(function (file) {
                    const validation = validateUploadFile(file);
                    if (validation.valid) {
                        validFiles.push(file);
                    } else if (!errors.includes(validation.message)) {
                        errors.push(validation.message);
                    }
                });

                if (validFiles.length) {
                    selectedFiles = selectedFiles.concat(validFiles);
                }

                syncFileInput();
                renderSelectedFilePreviews();

                if (errors.length) {
                    markFieldInvalid('vm_files', errors[0]);
                }
            }

            $('#vm_files').on('change', function (event) {
                handleIncomingFiles(event.target.files || []);
            });

            vmUploadZone.addEventListener('click', function () {
                vmFilesInput.click();
            });

            vmUploadZone.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    vmFilesInput.click();
                }
            });

            vmUploadZone.addEventListener('dragover', function (event) {
                event.preventDefault();
                vmUploadZone.classList.add('drag-over');
            });

            vmUploadZone.addEventListener('dragleave', function (event) {
                if (event.relatedTarget && vmUploadZone.contains(event.relatedTarget)) {
                    return;
                }

                vmUploadZone.classList.remove('drag-over');
            });

            vmUploadZone.addEventListener('drop', function (event) {
                event.preventDefault();
                vmUploadZone.classList.remove('drag-over');
                handleIncomingFiles(event.dataTransfer?.files || []);
            });

            $('#btn-add-vm').on('click', function () {
                openFormModal('add');
                vmModal.show();
            });

            $(document).on('click', '[data-existing-id]', function () {
                removedExistingFileIds.add(Number($(this).data('existing-id')));
                renderExistingFilePreviews();
            });

            $(document).on('click', '[data-new-index]', function () {
                const fileIndex = Number($(this).data('new-index'));
                if (Number.isNaN(fileIndex)) return;

                selectedFiles.splice(fileIndex, 1);
                syncFileInput();
                renderSelectedFilePreviews();
            });

            $(document).on('click', '.btn-edit', function () {
                openFormModal('edit');

                $.get(apiUrl($(this).data('id')) + '/edit')
                    .done(function (data) {
                        loadEditData(data);
                        $('#vmModalLabel').text('Edit Visual Merchandising Issue');
                        $('#btn-save').data('default-text', 'Update');
                        $('#btn-save .btn-text').text('Update');
                        vmModal.show();
                    })
                    .fail(function () {
                        showToast('Failed to load visual merchandising issue data.', 'danger');
                    });
            });

            $(document).on('click', '.btn-view', function () {
                $.get(apiUrl($(this).data('id')))
                    .done(function (data) {
                        const storeLabel = data.store
                            ? `${data.store.title}${data.store.code ? ` (${data.store.code})` : ''}`
                            : 'N/A';
                        const assetLabel = data.asset
                            ? `${data.asset.name}${data.asset.asset_code ? ` (${data.asset.asset_code})` : ''}${data.asset.asset_type?.name ? ` - ${data.asset.asset_type.name}` : ''}`
                            : 'N/A';

                        $('#view-store').text(storeLabel);
                        $('#view-asset').text(assetLabel);
                        $('#view-issue-fix-status').html(issueFixStatusBadge(data.issue_fix_status));
                        $('#view-status').html(statusBadge(data.status));
                        $('#view-issue-text').html(nl2br(data.issue_text || 'N/A'));
                        $('#view-created-at').text(formatDate(data.created_at));
                        $('#view-updated-at').text(formatDate(data.updated_at));

                        renderViewFiles(Array.isArray(data.visual_merchandising_files) ? data.visual_merchandising_files : []);
                        viewModal.show();
                    })
                    .fail(function () {
                        showToast('Failed to load issue details.', 'danger');
                    });
            });

            $(document).on('click', '.btn-delete', function () {
                $('#delete-vm-id').val($(this).data('id'));
                $('#delete-vm-name').text($(this).data('name'));
                deleteModal.show();
            });

            $('#btn-confirm-delete').on('click', function () {
                const vmId = $('#delete-vm-id').val();
                const $btn = $(this);
                const defaultText = $btn.find('.btn-text').text();

                $btn.prop('disabled', true);
                $btn.find('.btn-text').text('Deleting...');
                $btn.find('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: apiUrl(vmId),
                    type: 'DELETE',
                    success: function (response) {
                        deleteModal.hide();
                        showToast(response.message || 'Deleted successfully.', 'success');
                        setTimeout(() => location.reload(), 700);
                    },
                    error: function () {
                        showToast('Failed to delete the visual merchandising issue.', 'danger');
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                        $btn.find('.btn-text').text(defaultText);
                        $btn.find('.spinner-border').addClass('d-none');
                    }
                });
            });

            $('#vmForm').on('submit', function (event) {
                event.preventDefault();
                clearErrors();
                issueEditor.updateElement();
                syncFileInput();

                const vmId = $('#vm_id').val();
                const formData = new FormData(this);

                removedExistingFileIds.forEach(function (fileId) {
                    formData.append('remove_file_ids[]', fileId);
                });

                if (vmId) {
                    formData.append('_method', 'PUT');
                }

                setBtnLoading($('#btn-save'), true, vmId ? 'Updating...' : 'Saving...');

                $.ajax({
                    url: apiUrl(vmId || ''),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        vmModal.hide();
                        showToast(response.message || 'Saved successfully.', 'success');
                        setTimeout(() => location.reload(), 700);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            $.each(xhr.responseJSON.errors, function (field, messages) {
                                markFieldInvalid(normalizeErrorField(field), messages[0]);
                            });
                            return;
                        }

                        showToast(xhr.responseJSON?.message || 'Something went wrong. Please try again.', 'danger');
                    },
                    complete: function () {
                        setBtnLoading($('#btn-save'), false);
                    }
                });
            });

            $('#vmModal').on('hidden.bs.modal', function () {
                resetForm();
            });
        });
    </script>
@endpush
