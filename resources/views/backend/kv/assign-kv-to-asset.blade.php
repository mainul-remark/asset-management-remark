@extends('backend.master')

@section('title', 'Assign KV to Asset')

@section('body')
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-xl-10 col-lg-11 mx-auto">
                <div class="card custom-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-filter-3-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-14">Assign Filters</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label for="filter-division" class="form-label fs-12 mb-1">Division</label>
                                <select id="filter-division" class="form-select form-select-sm select-ele">
                                    <option value="">All Divisions</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-district" class="form-label fs-12 mb-1">District</label>
                                <select id="filter-district" class="form-select form-select-sm select-ele">
                                    <option value="">All Districts</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-store" class="form-label fs-12 mb-1">Store</label>
                                <select id="filter-store" class="form-select form-select-sm select-ele">
                                    <option value="">All Stores</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-asset-type" class="form-label fs-12 mb-1">Asset Category</label>
                                <select id="filter-asset-type" class="form-select form-select-sm select-ele">
                                    <option value="">All Categories</option>
                                    @foreach($assetTypes as $assetType)
                                        <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-asset" class="form-label fs-12 mb-1">Asset</label>
                                <select id="filter-asset" class="form-select form-select-sm select-ele"></select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-key-visual" class="form-label fs-12 mb-1">Key Visual</label>
                                <select id="filter-key-visual" class="form-select form-select-sm select-ele"></select>
                            </div>
                            <div class="col-md-2">
                                <label for="filter-key-visual-file" class="form-label fs-12 mb-1">KV File</label>
                                <select id="filter-key-visual-file" class="form-select form-select-sm select-ele"></select>
                            </div>
                            <div class="col-md-2">
                                <label for="filter-instalation-status" class="form-label fs-12 mb-1">Installation</label>
                                <select id="filter-instalation-status" class="form-select form-select-sm">
                                    <option value="">All Statuses</option>
                                    @foreach($instalationStatuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filter-perfect-size" class="form-label fs-12 mb-1">Perfect Size</label>
                                <select id="filter-perfect-size" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="button" class="btn btn-sm btn-primary btn-wave flex-fill" id="btn-filter">
                                    <i class="ri-search-line me-1"></i> Search
                                </button>
                                <button type="button" class="btn btn-sm btn-light btn-wave" id="btn-reset" title="Reset Filters">
                                    <i class="ri-refresh-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-10 col-lg-11 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="card-title mb-0">KV Assignments</div>
                            <span class="badge bg-primary-transparent" id="result-count"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-assignment">
                            <i class="ri-add-line me-1"></i> Add Assignment
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="assignment-table" class="table table-bordered align-middle w-100 mb-0">
                                <thead>
                                <tr>
                                    <th width="50">SL</th>
                                    <th>Asset</th>
                                    <th>Store</th>
                                    <th>Key Visual</th>
                                    <th>KV File</th>
                                    <th>Perfect Fit</th>
                                    <th>Assigned</th>
                                    <th>Installation</th>
                                    <th width="90">Actions</th>
                                </tr>
                                </thead>
                                <tbody id="assignment-tbody"></tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="text-center py-4 text-muted d-none">
                            <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                            <span class="fs-13">No KV assignments found for the current filters.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold" id="assignmentModalLabel">Add KV Assignment</h5>
                        <p class="text-muted fs-12 mb-0">Link a key visual file to a store asset</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignmentForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    <input type="hidden" id="assignment_id">
                    <div class="modal-body pt-3">
                        <div class="section-card mb-3">
                            <div class="section-card-header">
                                <i class="ri-store-2-line me-1"></i> Store Location
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="modal-division" class="form-label">Division</label>
                                        <select id="modal-division" class="form-select select-ele">
                                            <option value="">All Divisions</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modal-district" class="form-label">District</label>
                                        <select id="modal-district" class="form-select select-ele">
                                            <option value="">All Districts</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modal-store" class="form-label">Store <span class="text-danger">*</span></label>
                                        <select id="modal-store" class="form-select select-ele">
                                            <option value="">All Stores</option>
                                        </select>
                                        <div class="form-text">Select a store to load its assigned assets.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-card mb-3">
                            <div class="section-card-header">
                                <i class="ri-image-line me-1"></i> Asset
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label for="modal-asset" class="form-label">Asset <span class="text-danger">*</span></label>
                                        <select id="modal-asset" class="form-select select-ele" name="asset_id"></select>
                                        <div class="form-text d-none" id="asset-load-feedback"></div>
                                        <div class="invalid-feedback" id="error-asset_id"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-card mb-3">
                            <div class="section-card-header">
                                <i class="ri-layout-masonry-line me-1"></i> Key Visual
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="modal-asset-type" class="form-label">Asset Category</label>
                                        <select id="modal-asset-type" class="form-select select-ele">
                                            <option value="">All Categories</option>
                                            @foreach($assetTypes as $assetType)
                                                <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modal-brand" class="form-label">Brand</label>
                                        <select id="modal-brand" class="form-select select-ele">
                                            <option value="">All Brands</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}{{ $brand->code ? ' (' . $brand->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="modal-category" class="form-label">Category</label>
                                        <select id="modal-category" class="form-select select-ele">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}{{ $category->code ? ' (' . $category->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-key-visual" class="form-label">Key Visual <span class="text-danger">*</span></label>
                                        <select id="modal-key-visual" class="form-select select-ele" name="key_visual_id"></select>
                                        <div class="invalid-feedback" id="error-key_visual_id"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-key-visual-file" class="form-label">Key Visual File <span class="text-danger">*</span></label>
                                        <select id="modal-key-visual-file" class="form-select select-ele" name="key_visual_files_id"></select>
                                        <div class="invalid-feedback" id="error-key_visual_files_id"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-assignment">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Save Assignment</span>
                            <span class="spinner-border spinner-border-sm d-none" id="assignment-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <span class="avatar avatar-lg bg-danger-transparent rounded-circle">
                            <i class="ri-delete-bin-line text-danger fs-24"></i>
                        </span>
                    </div>
                    <h6 class="fw-semibold mb-1">Delete Assignment?</h6>
                    <p class="text-muted fs-13 mb-0" id="delete-message"></p>
                    <input type="hidden" id="delete-assignment-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">
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
.btn-list {
    display: flex;
    gap: 4px;
}
.assignment-table-cell .primary-line {
    font-weight: 600;
    color: var(--default-text-color);
}
.assignment-table-cell .secondary-line {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 2px;
}
.assignment-table-cell .secondary-line + .secondary-line {
    margin-top: 4px;
}
.assignment-fit-badge {
    min-width: 72px;
}
.section-card {
    border: 1px solid var(--default-border, #e9ebec);
    border-radius: 8px;
    overflow: hidden;
}
.section-card-header {
    background: var(--light, #f8f9fa);
    padding: 8px 14px;
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-muted, #6c757d);
    border-bottom: 1px solid var(--default-border, #e9ebec);
}
.section-card-body {
    padding: 16px 14px;
}
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const assignmentUrl = base_url + 'kv/assign-kv-to-asset';
        const filterUrl = assignmentUrl + '/filter';
        const storeAssetsUrl = (storeId) => `${assignmentUrl}/stores/${storeId}/assets`;

        const districts = @json($districts);
        const stores = @json($stores);
        const assets = @json($assets);
        const keyVisuals = @json($keyVisuals);
        const keyVisualFiles = @json($keyVisualFiles);
        const currentUser = @json($currentUser);
        let modalAssets = [];

        function showToast(message, type) {
            $(`<div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`).appendTo('body').delay(3000).queue(function () {
                $(this).remove();
            });
        }

        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function formatDate(value) {
            if (!value) {
                return '-';
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatFileMeta(file) {
            const parts = [];
            const size = file.key_visual_size || {};

            if (file.file_type) {
                parts.push(file.file_type);
            }

            if (size.name) {
                parts.push(size.name);
            } else if (size.width && size.height) {
                parts.push(`${size.width} x ${size.height} ${size.unit_name || ''}`.trim());
            }

            return parts.join(' | ') || '-';
        }

        function statusBadge(status) {
            const variantMap = {
                pending: 'warning',
                planned: 'info',
                installed: 'success',
                verified: 'primary'
            };
            const variant = variantMap[status] || 'secondary';

            return `<span class="badge bg-${variant}-transparent text-capitalize">${escapeHtml(status || 'pending')}</span>`;
        }

        function perfectFitBadge(value) {
            return String(value) === '1'
                ? '<span class="badge bg-success-transparent assignment-fit-badge">Perfect</span>'
                : '<span class="badge bg-light text-dark assignment-fit-badge">Adjusted</span>';
        }

        function setSaveLoading(loading) {
            $('#btn-save-assignment').prop('disabled', loading);
            $('#assignment-spinner').toggleClass('d-none', !loading);
        }

        function setDeleteLoading(loading) {
            $('#btn-confirm-delete').prop('disabled', loading);
            $('#btn-confirm-delete .spinner-border').toggleClass('d-none', !loading);
        }

        function clearErrors() {
            $('#assignmentForm .is-invalid').removeClass('is-invalid');
            $('#assignmentForm .invalid-feedback').text('');
        }

        function setAssetLoadFeedback(message = '', tone = 'danger') {
            const $feedback = $('#asset-load-feedback');
            $('#modal-asset').toggleClass('is-invalid', !!message && tone === 'danger');
            $feedback
                .text(message)
                .toggleClass('d-none', !message)
                .toggleClass('text-danger', !!message && tone === 'danger')
                .toggleClass('text-muted', !!message && tone === 'muted');
        }

        function populateDistrictOptions($select, divisionId = '', selectedId = '', placeholder = 'All Districts') {
            const filteredDistricts = districts.filter(function (district) {
                return !divisionId || String(district.division_id) === String(divisionId);
            }).sort(function (left, right) {
                return (left.name || '').localeCompare(right.name || '');
            });

            let options = `<option value="">${placeholder}</option>`;
            filteredDistricts.forEach(function (district) {
                options += `<option value="${district.id}">${escapeHtml(district.name)}</option>`;
            });

            $select.html(options);
            if (selectedId && filteredDistricts.some(district => String(district.id) === String(selectedId))) {
                $select.val(String(selectedId));
            } else {
                $select.val('');
            }
            $select.trigger('change.select2');
        }

        function populateStoreOptions($select, divisionId = '', districtId = '', selectedId = '', placeholder = 'All Stores') {
            const filteredStores = stores.filter(function (store) {
                const divisionMatches = !divisionId || String(store.division_id) === String(divisionId);
                const districtMatches = !districtId || String(store.district_id) === String(districtId);
                return divisionMatches && districtMatches;
            }).sort(function (left, right) {
                return (left.title || '').localeCompare(right.title || '');
            });

            let options = `<option value="">${placeholder}</option>`;
            filteredStores.forEach(function (store) {
                const codeSuffix = store.code ? ` (${escapeHtml(store.code)})` : '';
                options += `<option value="${store.id}">${escapeHtml(store.title)}${codeSuffix}</option>`;
            });

            $select.html(options);
            if (selectedId && filteredStores.some(store => String(store.id) === String(selectedId))) {
                $select.val(String(selectedId));
            } else {
                $select.val('');
            }
            $select.trigger('change.select2');
        }

        function populateAssetOptions($select, assetTypeId = '', storeId = '', selectedId = '', placeholder = 'Select Store First', divisionId = '', districtId = '', requireStore = true) {
            if (requireStore && !storeId) {
                $select.html(`<option value="">${placeholder}</option>`)
                    .val('')
                    .trigger('change.select2');
                return;
            }

            const filteredAssets = assets.filter(function (asset) {
                const typeMatches = !assetTypeId || String(asset.asset_type_id) === String(assetTypeId);
                const divisionMatches = !divisionId || String(asset.store?.division_id || '') === String(divisionId);
                const districtMatches = !districtId || String(asset.store?.district_id || '') === String(districtId);
                const storeMatches = !storeId || String(asset.store_id) === String(storeId);
                return typeMatches && divisionMatches && districtMatches && storeMatches;
            }).sort(function (left, right) {
                return (left.name || '').localeCompare(right.name || '');
            });

            let options = `<option value="">${placeholder}</option>`;
            filteredAssets.forEach(function (asset) {
                const extraParts = [asset.asset_type?.name, asset.store?.title].filter(Boolean).join(' | ');
                options += `<option value="${asset.id}">${escapeHtml(asset.name)} (${escapeHtml(asset.asset_code || '')})${extraParts ? ` | ${escapeHtml(extraParts)}` : ''}</option>`;
            });

            $select.html(options);
            if (selectedId && filteredAssets.some(asset => String(asset.id) === String(selectedId))) {
                $select.val(String(selectedId));
            } else {
                $select.val('');
            }
            $select.trigger('change.select2');
        }

        function renderModalAssetOptions(selectedId = '') {
            const storeId = $('#modal-store').val();
            const placeholder = !storeId
                ? 'Select Store First'
                : (modalAssets.length ? 'Select Asset' : 'No Assets Available');

            let options = `<option value="">${placeholder}</option>`;
            modalAssets.forEach(function (asset) {
                const extraParts = [asset.asset_type?.name, asset.store?.title].filter(Boolean).join(' | ');
                options += `<option value="${asset.id}">${escapeHtml(asset.name)} (${escapeHtml(asset.asset_code || '')})${extraParts ? ` | ${escapeHtml(extraParts)}` : ''}</option>`;
            });

            $('#modal-asset').html(options);
            if (selectedId && modalAssets.some(asset => String(asset.id) === String(selectedId))) {
                $('#modal-asset').val(String(selectedId));
            } else {
                $('#modal-asset').val('');
            }
            $('#modal-asset')
                .prop('disabled', !storeId || !modalAssets.length)
                .trigger('change.select2');
        }

        function loadModalAssets(selectedId = '') {
            const storeId = $('#modal-store').val();

            modalAssets = [];
            setAssetLoadFeedback('');
            $('#error-asset_id').text('');

            if (!storeId) {
                renderModalAssetOptions('');
                return;
            }

            $('#modal-asset')
                .prop('disabled', true)
                .html('<option value="">Loading assets...</option>')
                .val('')
                .trigger('change.select2');

            $.get(storeAssetsUrl(storeId))
                .done(function (response) {
                    modalAssets = Array.isArray(response.data) ? response.data : [];
                    renderModalAssetOptions(selectedId);

                    if (!modalAssets.length) {
                        setAssetLoadFeedback(response.message || 'No KV-ready assets are assigned to the selected store.', 'danger');
                    }
                })
                .fail(function (xhr) {
                    modalAssets = [];
                    renderModalAssetOptions('');
                    setAssetLoadFeedback(xhr.responseJSON?.message || 'Failed to load assets for the selected store.', 'danger');
                    showToast(xhr.responseJSON?.message || 'Failed to load assets for the selected store.', 'danger');
                });
        }

        function populateKeyVisualOptions($select, filters = {}, requireSelection = false) {
            const assetTypeId = filters.assetTypeId || '';
            const brandId = filters.brandId || '';
            const categoryId = filters.categoryId || '';
            const selectedId = filters.selectedId || '';
            const placeholder = filters.placeholder || 'All Key Visuals';

            const filteredKeyVisuals = keyVisuals.filter(function (keyVisual) {
                const assetTypeMatches = !assetTypeId || String(keyVisual.asset_type_id) === String(assetTypeId);
                const brandMatches = !brandId || (Array.isArray(keyVisual.brands) && keyVisual.brands.some(brand => String(brand.id) === String(brandId)));
                const categoryMatches = !categoryId || (Array.isArray(keyVisual.categories) && keyVisual.categories.some(category => String(category.id) === String(categoryId)));

                return assetTypeMatches && brandMatches && categoryMatches;
            }).sort(function (left, right) {
                return (left.name || '').localeCompare(right.name || '');
            });

            let options = `<option value="">${placeholder}</option>`;
            filteredKeyVisuals.forEach(function (keyVisual) {
                options += `<option value="${keyVisual.id}">${escapeHtml(keyVisual.name)}${keyVisual.unique_code ? ` (${escapeHtml(keyVisual.unique_code)})` : ''}</option>`;
            });

            $select.html(options);
            if (selectedId && filteredKeyVisuals.some(keyVisual => String(keyVisual.id) === String(selectedId))) {
                $select.val(String(selectedId));
            } else {
                $select.val('');
            }

            if (requireSelection) {
                $select.prop('disabled', !filteredKeyVisuals.length);
            }

            $select.trigger('change.select2');
        }

        function populateKeyVisualFileOptions($select, keyVisualId = '', selectedId = '', placeholder = 'All KV Files', requireKeyVisual = false) {
            if (requireKeyVisual && !keyVisualId) {
                $select.html(`<option value="">${placeholder}</option>`)
                    .prop('disabled', true)
                    .val('')
                    .trigger('change.select2');
                return;
            }

            const filteredFiles = keyVisualFiles.filter(function (file) {
                return !keyVisualId || String(file.key_visual_id) === String(keyVisualId);
            }).sort(function (left, right) {
                return (left.name || '').localeCompare(right.name || '');
            });

            let options = `<option value="">${placeholder}</option>`;
            filteredFiles.forEach(function (file) {
                const label = file.name || `File #${file.id}`;
                const meta = formatFileMeta(file);
                options += `<option value="${file.id}">${escapeHtml(label)}${meta !== '-' ? ` | ${escapeHtml(meta)}` : ''}</option>`;
            });

            $select.html(options);
            if (selectedId && filteredFiles.some(file => String(file.id) === String(selectedId))) {
                $select.val(String(selectedId));
            } else {
                $select.val('');
            }
            $select.prop('disabled', requireKeyVisual && !filteredFiles.length);
            $select.trigger('change.select2');
        }

        function refreshFilterAssets() {
            populateAssetOptions(
                $('#filter-asset'),
                $('#filter-asset-type').val(),
                $('#filter-store').val(),
                $('#filter-asset').val(),
                'Select Store First',
                $('#filter-division').val(),
                $('#filter-district').val(),
                true
            );
        }

        function refreshFilterKeyVisuals() {
            populateKeyVisualOptions($('#filter-key-visual'), {
                assetTypeId: $('#filter-asset-type').val(),
                selectedId: $('#filter-key-visual').val(),
                placeholder: 'All Key Visuals'
            });
            populateKeyVisualFileOptions($('#filter-key-visual-file'), $('#filter-key-visual').val(), $('#filter-key-visual-file').val(), 'All KV Files', false);
        }

        function refreshModalKeyVisuals(selectedKeyVisualId = '', selectedFileId = '') {
            populateKeyVisualOptions($('#modal-key-visual'), {
                assetTypeId: $('#modal-asset-type').val(),
                brandId: $('#modal-brand').val(),
                categoryId: $('#modal-category').val(),
                selectedId: selectedKeyVisualId,
                placeholder: 'Select Key Visual'
            }, true);
            populateKeyVisualFileOptions($('#modal-key-visual-file'), $('#modal-key-visual').val(), selectedFileId, 'Select KV File', true);
        }

        function resetModalForm() {
            $('#assignmentForm')[0].reset();
            $('#assignment_id').val('');
            $('#modal-division').val('').trigger('change.select2');
            populateDistrictOptions($('#modal-district'), '', '', 'All Districts');
            populateStoreOptions($('#modal-store'), '', '', '', 'All Stores');
            $('#modal-asset-type').val('').trigger('change.select2');
            $('#modal-brand').val('').trigger('change.select2');
            $('#modal-category').val('').trigger('change.select2');
            modalAssets = [];
            renderModalAssetOptions('');
            setAssetLoadFeedback('');
            refreshModalKeyVisuals();
            clearErrors();
        }

        function renderTable(data) {
            const $tbody = $('#assignment-tbody');
            $tbody.empty();

            if (!data.length) {
                $('#assignment-table').hide();
                $('#empty-state').removeClass('d-none').show();
                $('#result-count').text('');
                return;
            }

            $('#assignment-table').show();
            $('#empty-state').hide();

            data.forEach(function (item, index) {
                const asset = item.asset || {};
                const store = asset.store || {};
                const keyVisual = item.key_visual || {};
                const file = item.key_visual_file || {};
                const proofText = item.instalation_proof || '';
                const proof = proofText
                    ? `<div class="secondary-line" title="${escapeHtml(proofText)}">${escapeHtml(proofText).substring(0, 80)}${proofText.length > 80 ? '...' : ''}</div>`
                    : '';

                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${escapeHtml(asset.name || '-')}</div>
                                <div class="secondary-line">${escapeHtml(asset.asset_code || '-')}</div>
                                <div class="secondary-line">${escapeHtml(asset.asset_type?.name || '-')}</div>
                            </div>
                        </td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${escapeHtml(store.title || 'No Store')}</div>
                                <div class="secondary-line">${escapeHtml(store.code || '')}</div>
                                <div class="secondary-line">${escapeHtml([store.division?.name, store.district?.name].filter(Boolean).join(', ') || '-')}</div>
                            </div>
                        </td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${escapeHtml(keyVisual.name || '-')}</div>
                                <div class="secondary-line">${escapeHtml(keyVisual.unique_code || '-')}</div>
                                <div class="secondary-line">${escapeHtml(keyVisual.asset_type?.name || '-')}</div>
                            </div>
                        </td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${escapeHtml(file.name || '-')}</div>
                                <div class="secondary-line">${escapeHtml(formatFileMeta(file))}</div>
                            </div>
                        </td>
                        <td>${perfectFitBadge(item.has_perfect_size_kv)}</td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${escapeHtml(formatDate(item.assigned_date))}</div>
                                <div class="secondary-line">${escapeHtml(item.assigned_by_user?.name || '-')}</div>
                            </div>
                        </td>
                        <td>
                            <div class="assignment-table-cell">
                                <div class="primary-line">${statusBadge(item.instalation_status)}</div>
                                <div class="secondary-line">${escapeHtml(formatDate(item.instalation_date))}</div>
                                <div class="secondary-line">${escapeHtml(item.installed_by_user?.name || '-')}</div>
                                ${proof}
                            </div>
                        </td>
                        <td>
                            <div class="btn-list">
                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="${item.id}" title="Edit"><i class="ri-edit-box-line"></i></button>
                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="${item.id}" data-asset="${escapeHtml(asset.name || '')}" data-key-visual="${escapeHtml(keyVisual.name || '')}" title="Delete"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </td>
                    </tr>
                `;

                $tbody.append(row);
            });

            $('#result-count').text(`${data.length} assignment(s)`);
        }

        function loadData() {
            const params = {};
            const fields = {
                division_id: '#filter-division',
                district_id: '#filter-district',
                store_id: '#filter-store',
                asset_type_id: '#filter-asset-type',
                asset_id: '#filter-asset',
                key_visual_id: '#filter-key-visual',
                key_visual_files_id: '#filter-key-visual-file',
                instalation_status: '#filter-instalation-status',
                has_perfect_size_kv: '#filter-perfect-size'
            };

            Object.entries(fields).forEach(function ([key, selector]) {
                const value = $(selector).val();
                if (value !== '' && value !== null) {
                    params[key] = value;
                }
            });

            const $button = $('#btn-filter');
            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...');

            $.get(filterUrl, params)
                .done(function (data) {
                    renderTable(data);
                })
                .fail(function () {
                    $('#assignment-tbody').empty();
                    $('#assignment-table').hide();
                    $('#empty-state').removeClass('d-none').show();
                    $('#result-count').text('');
                    showToast('Failed to load KV assignments.', 'danger');
                })
                .always(function () {
                    $button.prop('disabled', false).html('<i class="ri-search-line me-1"></i> Search');
                });
        }

        function applyValidationErrors(errors) {
            const fieldMap = {
                asset_id: '#modal-asset',
                key_visual_id: '#modal-key-visual',
                key_visual_files_id: '#modal-key-visual-file'
            };

            Object.entries(errors || {}).forEach(function ([field, messages]) {
                const selector = fieldMap[field];
                if (selector) {
                    $(selector).addClass('is-invalid');
                }
                $('#error-' + field).text(messages[0] || '');
            });
        }

        $('#filter-division').on('change', function () {
            populateDistrictOptions($('#filter-district'), $(this).val(), '', 'All Districts');
            populateStoreOptions($('#filter-store'), $(this).val(), '', '', 'All Stores');
            refreshFilterAssets();
        });

        $('#filter-district').on('change', function () {
            populateStoreOptions($('#filter-store'), $('#filter-division').val(), $(this).val(), '', 'All Stores');
            refreshFilterAssets();
        });

        $('#filter-store').on('change', refreshFilterAssets);

        $('#filter-asset-type').on('change', function () {
            refreshFilterAssets();
            refreshFilterKeyVisuals();
        });

        $('#filter-key-visual').on('change', function () {
            populateKeyVisualFileOptions($('#filter-key-visual-file'), $(this).val(), $('#filter-key-visual-file').val(), 'All KV Files', false);
        });

        $('#modal-division').on('change', function () {
            populateDistrictOptions($('#modal-district'), $(this).val(), '', 'All Districts');
            populateStoreOptions($('#modal-store'), $(this).val(), '', '', 'All Stores');
            loadModalAssets();
        });

        $('#modal-district').on('change', function () {
            populateStoreOptions($('#modal-store'), $('#modal-division').val(), $(this).val(), '', 'All Stores');
            loadModalAssets();
        });

        $('#modal-store').on('change', function () {
            loadModalAssets();
        });

        $('#modal-asset-type, #modal-brand, #modal-category').on('change', function () {
            refreshModalKeyVisuals();
        });

        $('#modal-asset').on('change', function () {
            const selectedAsset = modalAssets.find(asset => String(asset.id) === String($(this).val()));
            if (!selectedAsset) {
                return;
            }

            if (String($('#modal-asset-type').val() || '') !== String(selectedAsset.asset_type_id)) {
                $('#modal-asset-type').val(String(selectedAsset.asset_type_id)).trigger('change.select2');
            }

            refreshModalKeyVisuals();
        });

        $('#modal-key-visual').on('change', function () {
            populateKeyVisualFileOptions($('#modal-key-visual-file'), $(this).val(), $('#modal-key-visual-file').val(), 'Select KV File', true);
        });

        $('#btn-filter').on('click', loadData);

        $('#btn-reset').on('click', function () {
            $('#filter-division').val('').trigger('change').trigger('change.select2');
            $('#filter-asset-type').val('').trigger('change').trigger('change.select2');
            $('#filter-key-visual').val('').trigger('change').trigger('change.select2');
            $('#filter-key-visual-file').val('').trigger('change').trigger('change.select2');
            $('#filter-instalation-status').val('');
            $('#filter-perfect-size').val('');
            populateAssetOptions($('#filter-asset'), '', '', '', 'Select Store First', '', '', true);
            refreshFilterKeyVisuals();
            loadData();
        });

        $('#btn-add-assignment').on('click', function () {
            resetModalForm();
            $('#assignmentModalLabel').text('Add KV Assignment');
            $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Save Assignment');
            assignmentModal.show();
        });

        $(document).on('click', '.btn-edit', function () {
            const id = $(this).data('id');
            resetModalForm();
            $('#assignmentModalLabel').text('Edit KV Assignment');
            $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Update Assignment');

            $.get(`${assignmentUrl}/${id}/edit`, function (data) {
                $('#assignment_id').val(data.id);

                const store = data.asset?.store;
                if (store?.division_id) {
                    $('#modal-division').val(String(store.division_id)).trigger('change.select2');
                    populateDistrictOptions($('#modal-district'), store.division_id, store.district_id, 'All Districts');
                    populateStoreOptions($('#modal-store'), store.division_id, store.district_id, store.id, 'All Stores');
                }

                const assetTypeId = data.asset?.asset_type_id || data.key_visual?.asset_type_id || '';
                const brandId = data.key_visual?.brands?.[0]?.id || '';
                const categoryId = data.key_visual?.categories?.[0]?.id || '';

                $('#modal-asset-type').val(assetTypeId ? String(assetTypeId) : '').trigger('change.select2');
                $('#modal-brand').val(brandId ? String(brandId) : '').trigger('change.select2');
                $('#modal-category').val(categoryId ? String(categoryId) : '').trigger('change.select2');
                refreshModalKeyVisuals(data.key_visual_id, data.key_visual_files_id);

                loadModalAssets(data.asset_id);
                assignmentModal.show();
            }).fail(function () {
                showToast('Failed to load KV assignment data.', 'danger');
            });
        });

        $('#assignmentForm').on('submit', function (event) {
            event.preventDefault();
            clearErrors();

            const id = $('#assignment_id').val();
            const payload = {
                asset_id: $('#modal-asset').val(),
                key_visual_id: $('#modal-key-visual').val(),
                key_visual_files_id: $('#modal-key-visual-file').val()
            };

            setSaveLoading(true);

            $.ajax({
                url: id ? `${assignmentUrl}/${id}` : assignmentUrl,
                type: id ? 'PUT' : 'POST',
                data: payload,
                success: function (response) {
                    if (response.success === false) {
                        showToast(response.message || 'Something went wrong.', 'danger');
                        return;
                    }

                    assignmentModal.hide();
                    loadData();
                    showToast(response.message, 'success');
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        applyValidationErrors(xhr.responseJSON.errors);
                    } else {
                        showToast(xhr.responseJSON?.message || 'Failed to save KV assignment.', 'danger');
                    }
                },
                complete: function () {
                    setSaveLoading(false);
                }
            });
        });

        $(document).on('click', '.btn-delete', function () {
            $('#delete-assignment-id').val($(this).data('id'));
            $('#delete-message').html(`Remove <strong>${$(this).data('key-visual') || 'this key visual'}</strong> from <strong>${$(this).data('asset') || 'this asset'}</strong>?`);
            deleteModal.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-assignment-id').val();
            if (!id) {
                return;
            }

            setDeleteLoading(true);

            $.ajax({
                url: `${assignmentUrl}/${id}`,
                type: 'DELETE',
                success: function (response) {
                    if (response.success === false) {
                        showToast(response.message || 'Failed to delete KV assignment.', 'danger');
                        return;
                    }

                    deleteModal.hide();
                    loadData();
                    showToast(response.message, 'success');
                },
                error: function (xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to delete KV assignment.', 'danger');
                },
                complete: function () {
                    setDeleteLoading(false);
                }
            });
        });

        populateDistrictOptions($('#filter-district'), '', '', 'All Districts');
        populateStoreOptions($('#filter-store'), '', '', '', 'All Stores');
        populateAssetOptions($('#filter-asset'), '', '', '', 'Select Store First', '', '', true);
        refreshFilterKeyVisuals();
        resetModalForm();
        loadData();
    });
    </script>
@endpush
