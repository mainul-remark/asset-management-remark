@extends('backend.master')

@section('title', 'Assign Asset to Brand')

@section('body')
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-xl-11 col-lg-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-filter-3-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-14">Assignment Filters</span>
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
                                <label for="filter-brand" class="form-label fs-12 mb-1">Brand</label>
                                <select id="filter-brand" class="form-select form-select-sm select-ele">
                                    <option value="">All Brands</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}{{ $brand->code ? ' (' . $brand->code . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter-status" class="form-label fs-12 mb-1">Status</label>
                                <select id="filter-status" class="form-select form-select-sm">
                                    <option value="">All Statuses</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter-asset" class="form-label fs-12 mb-1">Asset</label>
                                <select id="filter-asset" class="form-select form-select-sm">
                                    <option value=""></option>
                                </select>
                                <div class="form-text">Search by asset name or code. Store and category filters narrow the results.</div>
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
            <div class="col-xl-11 col-lg-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="card-title mb-0">Brand Asset Assignments</div>
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
                                    <th width="60">SL</th>
                                    <th>Brand</th>
                                    <th>Asset</th>
                                    <th>Store</th>
                                    <th>Charge</th>
                                    <th>Assigned</th>
                                    <th>Close Date</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                                </thead>
                                <tbody id="assignment-tbody"></tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="text-center py-5 text-muted d-none">
                            <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                            <span class="fs-13">No asset-to-brand assignments found for the selected filters.</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <div class="fs-12 text-muted" id="pagination-summary">No records found.</div>
                        <nav aria-label="Assignment pagination">
                            <ul class="pagination pagination-sm mb-0" id="pagination-links"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold" id="assignmentModalLabel">Assign Asset to Brand</h5>
                        <p class="text-muted fs-12 mb-0">Create or update a brand-level asset assignment.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignmentForm">
                    <input type="hidden" id="assignment_id">
                    <div class="modal-body pt-3">

                        <div class="section-card mb-3">
                            <div class="section-card-header">
                                <i class="ri-building-2-line me-1"></i> Asset Helper Filters
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="modal-division" class="form-label">Division</label>
                                        <select id="modal-division" class="form-select select-ele">
                                            <option value="">All Divisions</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="modal-district" class="form-label">District</label>
                                        <select id="modal-district" class="form-select select-ele">
                                            <option value="">All Districts</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="modal-store" class="form-label">Store</label>
                                        <select id="modal-store" class="form-select select-ele">
                                            <option value="">All Stores</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="modal-asset-type" class="form-label">Asset Category</label>
                                        <select id="modal-asset-type" class="form-select select-ele">
                                            <option value="">All Categories</option>
                                            @foreach($assetTypes as $assetType)
                                                <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-card mb-3">
                            <div class="section-card-header">
                                <i class="ri-price-tag-3-line me-1"></i> Brand & Asset
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="modal-brand" class="form-label">Brands <span class="text-danger">*</span></label>
                                        <select id="modal-brand" name="brand_ids[]" class="form-select select-ele" multiple>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}{{ $brand->code ? ' (' . $brand->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <div class="form-text" id="modal-brand-help">Select one or more brands for this asset.</div>
                                        <div class="invalid-feedback" id="error-brand_id"></div>
                                        <div class="invalid-feedback" id="error-brand_ids"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="modal-asset" class="form-label">Asset <span class="text-danger">*</span></label>
                                        <select id="modal-asset" name="asset_id" class="form-select">
                                            <option value=""></option>
                                        </select>
                                        <div class="form-text">Search by name or asset code. Use the helper filters above to narrow the asset list.</div>
                                        <div class="invalid-feedback" id="error-asset_id"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-card">
                            <div class="section-card-header">
                                <i class="ri-file-list-3-line me-1"></i> Assignment Details
                            </div>
                            <div class="section-card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="modal-status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select id="modal-status" name="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-status"></div>
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

    <div class="modal fade" id="viewAssignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Assignment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Brand</div>
                                <div class="detail-value" id="view-brand">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Status</div>
                                <div class="detail-value" id="view-status">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Asset</div>
                                <div class="detail-value" id="view-asset">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Store</div>
                                <div class="detail-value" id="view-store">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Category</div>
                                <div class="detail-value" id="view-asset-type">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Asset Charge</div>
                                <div class="detail-value" id="view-charge">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Assigned By</div>
                                <div class="detail-value" id="view-assigned-by">-</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <div class="detail-label">Assigned On</div>
                                <div class="detail-value" id="view-created-at">-</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="detail-card">
                                <div class="detail-label">Close Date</div>
                                <div class="detail-value" id="view-close-date">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
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
.assignment-table-cell .primary-line {
    font-weight: 600;
    color: var(--default-text-color);
}

.assignment-table-cell .secondary-line {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 3px;
}

.btn-list {
    display: flex;
    gap: 4px;
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

.detail-card {
    border: 1px solid var(--default-border, #e9ebec);
    border-radius: 8px;
    padding: 14px;
    background: rgba(var(--primary-rgb), 0.02);
}

.detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin-bottom: 6px;
}

.detail-value {
    font-weight: 600;
    color: var(--default-text-color);
}

.select2-container--bootstrap-5 .select2-selection.is-invalid {
    border-color: #dc3545;
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
            const viewAssignmentModal = new bootstrap.Modal(document.getElementById('viewAssignmentModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            const routes = {
                store: @json(route('assets.assign-asset-to-brand.store')),
                filter: @json(route('assets.assign-asset-to-brand.filter')),
                assets: @json(route('assets.assign-asset-to-brand.assets')),
                show: @json(route('assets.assign-asset-to-brand.show', ['assignAssetToBrand' => '__ID__'])),
                edit: @json(route('assets.assign-asset-to-brand.edit', ['assignAssetToBrand' => '__ID__'])),
                update: @json(route('assets.assign-asset-to-brand.update', ['assignAssetToBrand' => '__ID__'])),
                destroy: @json(route('assets.assign-asset-to-brand.destroy', ['assignAssetToBrand' => '__ID__'])),
            };

            const districts = @json($districts);
            const stores = @json($stores);
            const paginationState = {
                current_page: 1,
                last_page: 1,
                total: 0,
                from: null,
                to: null,
                per_page: 15,
            };

            let currentAssignments = [];

            function endpoint(urlTemplate, id) {
                return urlTemplate.replace('__ID__', id);
            }

            function escapeHtml(value) {
                return $('<div>').text(value ?? '').html();
            }

            function showToast(message, type = 'success') {
                const toast = $(`
                    <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index: 1080;" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${escapeHtml(message)}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `);

                $('body').append(toast);
                setTimeout(function () {
                    toast.remove();
                }, 3000);
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

            function formatCurrency(value) {
                const amount = Number(value ?? 0);

                if (Number.isNaN(amount)) {
                    return '-';
                }

                return new Intl.NumberFormat('en-BD', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            }

            function statusBadge(status) {
                const isActive = Number(status) === 1;

                return `<span class="badge ${isActive ? 'bg-success-transparent text-success' : 'bg-danger-transparent text-danger'}">${isActive ? 'Active' : 'Inactive'}</span>`;
            }

            function assetOptionText(asset) {
                const parts = [
                    asset?.name || 'Unknown Asset',
                    asset?.asset_code ? `(${asset.asset_code})` : '',
                    asset?.asset_type?.name || '',
                    asset?.is_common_asset ? 'Common Asset' : (asset?.store?.title || '')
                ].filter(Boolean);

                return parts.join(' | ');
            }

            function storeLabel(store) {
                if (!store) {
                    return 'Common / No Store';
                }

                const location = [store.division?.name, store.district?.name].filter(Boolean).join(', ');
                const primary = [store.title, store.code ? `(${store.code})` : ''].filter(Boolean).join(' ');

                return [primary, location].filter(Boolean).join(' | ');
            }

            function clearSelect2Validation($field) {
                $field.removeClass('is-invalid');
                $field.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }

            function setSelect2Validation($field) {
                $field.addClass('is-invalid');
                $field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }

            function clearErrors() {
                $('#assignmentForm .is-invalid').removeClass('is-invalid');
                $('#assignmentForm .invalid-feedback').text('');
                $('#assignmentForm select').each(function () {
                    clearSelect2Validation($(this));
                });
            }

            function applyValidationErrors(errors) {
                Object.entries(errors || {}).forEach(function ([field, messages]) {
                    const normalizedField = field.startsWith('brand_ids') ? 'brand_ids' : field;
                    const fieldMap = {
                        brand_id: '#modal-brand',
                        brand_ids: '#modal-brand',
                        asset_id: '#modal-asset',
                        status: '#modal-status',
                    };
                    const selector = fieldMap[normalizedField];
                    if (!selector) {
                        return;
                    }

                    const $field = $(selector);
                    if ($field.is('select')) {
                        setSelect2Validation($field);
                    } else {
                        $field.addClass('is-invalid');
                    }
                    $('#error-' + normalizedField).text(messages[0] || '');
                });
            }

            function refreshSelectOptions($select, options, placeholder, selectedId = '') {
                const selectedValue = selectedId !== null && selectedId !== undefined ? String(selectedId) : '';
                let html = `<option value="">${escapeHtml(placeholder)}</option>`;

                options.forEach(function (item) {
                    html += `<option value="${item.id}">${escapeHtml(item.label)}</option>`;
                });

                $select.html(html);
                $select.val(selectedValue);
                $select.trigger('change.select2');
            }

            function buildDistrictOptions(divisionId) {
                return districts
                    .filter(function (district) {
                        return !divisionId || String(district.division_id) === String(divisionId);
                    })
                    .map(function (district) {
                        return {
                            id: district.id,
                            label: district.name,
                        };
                    });
            }

            function buildStoreOptions(divisionId, districtId) {
                return stores
                    .filter(function (store) {
                        if (divisionId && String(store.division_id) !== String(divisionId)) {
                            return false;
                        }

                        if (districtId && String(store.district_id) !== String(districtId)) {
                            return false;
                        }

                        return true;
                    })
                    .map(function (store) {
                        return {
                            id: store.id,
                            label: [store.title, store.code ? `(${store.code})` : ''].filter(Boolean).join(' '),
                        };
                    });
            }

            function syncDistrictAndStoreOptions(context) {
                const divisionSelector = context === 'modal' ? '#modal-division' : '#filter-division';
                const districtSelector = context === 'modal' ? '#modal-district' : '#filter-district';
                const storeSelector = context === 'modal' ? '#modal-store' : '#filter-store';

                const divisionId = $(divisionSelector).val();
                const currentDistrict = $(districtSelector).val();
                const currentStore = $(storeSelector).val();
                const districtOptions = buildDistrictOptions(divisionId);
                const validDistrict = districtOptions.some(function (district) {
                    return String(district.id) === String(currentDistrict);
                }) ? currentDistrict : '';

                refreshSelectOptions($(districtSelector), districtOptions, 'All Districts', validDistrict);

                const storeOptions = buildStoreOptions(divisionId, validDistrict || $(districtSelector).val());
                const validStore = storeOptions.some(function (store) {
                    return String(store.id) === String(currentStore);
                }) ? currentStore : '';

                refreshSelectOptions($(storeSelector), storeOptions, 'All Stores', validStore);
            }

            function buildAssetFilters(context) {
                if (context === 'modal') {
                    return {
                        division_id: $('#modal-division').val(),
                        district_id: $('#modal-district').val(),
                        store_id: $('#modal-store').val(),
                        asset_type_id: $('#modal-asset-type').val(),
                    };
                }

                return {
                    division_id: $('#filter-division').val(),
                    district_id: $('#filter-district').val(),
                    store_id: $('#filter-store').val(),
                    asset_type_id: $('#filter-asset-type').val(),
                };
            }

            function initializeRemoteAssetSelect(selector, context) {
                const $select = $(selector);
                const dropdownParent = context === 'modal' ? $('#assignmentModal') : $select.parent();

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: dropdownParent,
                    placeholder: context === 'modal' ? 'Search Asset' : 'All Assets',
                    allowClear: true,
                    minimumInputLength: 0,
                    ajax: {
                        url: routes.assets,
                        delay: 250,
                        dataType: 'json',
                        data: function (params) {
                            return {
                                ...buildAssetFilters(context),
                                q: params.term || '',
                                limit: 30,
                            };
                        },
                        processResults: function (response) {
                            const results = (response.data || []).map(function (asset) {
                                return {
                                    id: String(asset.id),
                                    text: assetOptionText(asset),
                                    asset: asset,
                                };
                            });

                            return { results: results };
                        }
                    }
                });

                $select.on('select2:select', function (event) {
                    const selectedAsset = event.params.data?.asset || null;
                    $select.data('selectedAsset', selectedAsset);
                    if (context === 'modal' && selectedAsset?.asset_type_id) {
                        $('#modal-asset-type').val(String(selectedAsset.asset_type_id)).trigger('change.select2');
                    }
                    clearSelect2Validation($select);
                });

                $select.on('select2:clear', function () {
                    $select.data('selectedAsset', null);
                });
            }

            function clearRemoteAssetSelection($select) {
                $select.val(null).trigger('change');
                $select.empty().append('<option value=""></option>');
                $select.data('selectedAsset', null);
                clearSelect2Validation($select);
            }

            function setRemoteAssetSelection($select, asset) {
                clearRemoteAssetSelection($select);

                if (!asset) {
                    return;
                }

                const option = new Option(assetOptionText(asset), asset.id, true, true);
                $select.append(option).trigger('change');
                $select.data('selectedAsset', asset);
            }

            function setSaveLoading(isLoading) {
                $('#btn-save-assignment').prop('disabled', isLoading);
                $('#assignment-spinner').toggleClass('d-none', !isLoading);
                $('#btn-save-assignment .btn-text').toggleClass('d-none', isLoading);
            }

            function setDeleteLoading(isLoading) {
                $('#btn-confirm-delete').prop('disabled', isLoading);
                $('#btn-confirm-delete .spinner-border').toggleClass('d-none', !isLoading);
                $('#btn-confirm-delete .btn-text').toggleClass('d-none', isLoading);
            }

            function renderTable(items, meta) {
                const $tbody = $('#assignment-tbody');
                $tbody.empty();

                if (!items.length) {
                    $('#assignment-table').hide();
                    $('#empty-state').removeClass('d-none').show();
                    $('#result-count').text('');
                    $('#pagination-summary').text('No records found.');
                    $('#pagination-links').empty();
                    return;
                }

                $('#assignment-table').show();
                $('#empty-state').hide();

                items.forEach(function (item, index) {
                    const asset = item.asset || {};
                    const store = asset.store || null;
                    const brand = item.brand || {};
                    const serial = (meta.from || 1) + index;

                    const row = `
                        <tr>
                            <td>${serial}</td>
                            <td>
                                <div class="assignment-table-cell">
                                    <div class="primary-line">${escapeHtml(brand.name || '-')}</div>
                                    <div class="secondary-line">${escapeHtml(brand.code || '-')}</div>
                                </div>
                            </td>
                            <td>
                                <div class="assignment-table-cell">
                                    <div class="primary-line">${escapeHtml(asset.name || '-')}</div>
                                    <div class="secondary-line">${escapeHtml(asset.asset_code || '-')}</div>
                                    <div class="secondary-line">${escapeHtml(asset.asset_type?.name || '-')}</div>
                                </div>
                            </td>
                            <td>
                                <div class="assignment-table-cell">
                                    <div class="primary-line">${escapeHtml(store?.title || 'Common / No Store')}</div>
                                    <div class="secondary-line">${escapeHtml(store?.code || '')}</div>
                                    <div class="secondary-line">${escapeHtml([store?.division?.name, store?.district?.name].filter(Boolean).join(', ') || (asset.is_common_asset ? 'Common asset' : '-'))}</div>
                                </div>
                            </td>
                            <td>${escapeHtml(formatCurrency(item.asset_charge))}</td>
                            <td>
                                <div class="assignment-table-cell">
                                    <div class="primary-line">${escapeHtml(formatDate(item.created_at))}</div>
                                    <div class="secondary-line">${escapeHtml(item.assigned_by?.name || '-')}</div>
                                </div>
                            </td>
                            <td>${escapeHtml(formatDate(item.close_date))}</td>
                            <td>${statusBadge(item.status)}</td>
                            <td>
                                <div class="btn-list">
                                    <button type="button" class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="${item.id}" title="View">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="${item.id}" title="Edit">
                                        <i class="ri-edit-box-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="${item.id}" title="Delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;

                    $tbody.append(row);
                });

                $('#result-count').text(`${meta.total} assignment(s)`);
                $('#pagination-summary').text(`Showing ${meta.from} to ${meta.to} of ${meta.total} assignments`);
            }

            function renderPagination(meta) {
                const $pagination = $('#pagination-links');
                $pagination.empty();

                if (!meta.total || meta.last_page <= 1) {
                    return;
                }

                const startPage = Math.max(1, meta.current_page - 2);
                const endPage = Math.min(meta.last_page, meta.current_page + 2);

                $pagination.append(`
                    <li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
                        <a class="page-link pagination-link" href="#" data-page="${meta.current_page - 1}">Prev</a>
                    </li>
                `);

                for (let page = startPage; page <= endPage; page++) {
                    $pagination.append(`
                        <li class="page-item ${page === meta.current_page ? 'active' : ''}">
                            <a class="page-link pagination-link" href="#" data-page="${page}">${page}</a>
                        </li>
                    `);
                }

                $pagination.append(`
                    <li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
                        <a class="page-link pagination-link" href="#" data-page="${meta.current_page + 1}">Next</a>
                    </li>
                `);
            }

            function currentFilters() {
                const payload = {
                    division_id: $('#filter-division').val(),
                    district_id: $('#filter-district').val(),
                    store_id: $('#filter-store').val(),
                    asset_type_id: $('#filter-asset-type').val(),
                    asset_id: $('#filter-asset').val(),
                    brand_id: $('#filter-brand').val(),
                    status: $('#filter-status').val(),
                    per_page: paginationState.per_page,
                };

                return Object.fromEntries(
                    Object.entries(payload).filter(function ([, value]) {
                        return value !== null && value !== '';
                    })
                );
            }

            function loadData(page = 1) {
                const params = {
                    ...currentFilters(),
                    page: page,
                };

                const $button = $('#btn-filter');
                $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...');

                $.get(routes.filter, params)
                    .done(function (response) {
                        currentAssignments = response.data || [];
                        Object.assign(paginationState, response.meta || {});
                        renderTable(currentAssignments, paginationState);
                        renderPagination(paginationState);
                    })
                    .fail(function () {
                        currentAssignments = [];
                        $('#assignment-tbody').empty();
                        $('#assignment-table').hide();
                        $('#empty-state').removeClass('d-none').show();
                        $('#result-count').text('');
                        $('#pagination-summary').text('Failed to load records.');
                        $('#pagination-links').empty();
                        showToast('Failed to load asset-to-brand assignments.', 'danger');
                    })
                    .always(function () {
                        $button.prop('disabled', false).html('<i class="ri-search-line me-1"></i> Search');
                    });
            }

            function resetModalForm() {
                $('#assignmentForm')[0].reset();
                $('#assignment_id').val('');
                $('#assignmentModalLabel').text('Assign Asset to Brand');
                $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Save Assignments');
                $('#modal-brand-help').text('Select one or more brands for this asset.');
                $('#modal-brand').val(null).trigger('change');
                $('#modal-division').val('').trigger('change.select2');
                $('#modal-asset-type').val('').trigger('change.select2');
                syncDistrictAndStoreOptions('modal');
                clearRemoteAssetSelection($('#modal-asset'));
                clearErrors();
            }

            function fillViewModal(data) {
                $('#view-brand').text(data.brand?.name ? `${data.brand.name}${data.brand.code ? ' (' + data.brand.code + ')' : ''}` : '-');
                $('#view-status').html(statusBadge(data.status));
                $('#view-asset').text(data.asset ? assetOptionText(data.asset) : '-');
                $('#view-store').text(storeLabel(data.asset?.store));
                $('#view-asset-type').text(data.asset?.asset_type?.name || '-');
                $('#view-charge').text(formatCurrency(data.asset_charge));
                $('#view-assigned-by').text(data.assigned_by?.name || '-');
                $('#view-created-at').text(formatDate(data.created_at));
                $('#view-close-date').text(formatDate(data.close_date));
            }

            function loadAssignmentForView(id) {
                $.get(endpoint(routes.show, id))
                    .done(function (data) {
                        fillViewModal(data);
                        viewAssignmentModal.show();
                    })
                    .fail(function () {
                        showToast('Failed to load assignment details.', 'danger');
                    });
            }

            function loadAssignmentForEdit(id) {
                resetModalForm();
                $('#assignmentModalLabel').text('Edit Asset to Brand Assignment');
                $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Update Assignment');
                $('#modal-brand-help').text('Editing updates one assignment row, so keep exactly one brand selected.');

                $.get(endpoint(routes.edit, id))
                    .done(function (data) {
                        $('#assignment_id').val(data.id);
                        $('#modal-brand').val(data.brand_id ? [String(data.brand_id)] : []).trigger('change');
                        $('#modal-status').val(String(data.status ?? 1));

                        const asset = data.asset || null;
                        const store = asset?.store || null;

                        $('#modal-division').val(store?.division_id ? String(store.division_id) : '').trigger('change.select2');
                        syncDistrictAndStoreOptions('modal');
                        $('#modal-district').val(store?.district_id ? String(store.district_id) : '').trigger('change.select2');
                        syncDistrictAndStoreOptions('modal');
                        $('#modal-store').val(store?.id ? String(store.id) : '').trigger('change.select2');
                        $('#modal-asset-type').val(asset?.asset_type_id ? String(asset.asset_type_id) : '').trigger('change.select2');
                        setRemoteAssetSelection($('#modal-asset'), asset);
                        assignmentModal.show();
                    })
                    .fail(function () {
                        showToast('Failed to load assignment for editing.', 'danger');
                    });
            }

            $('#filter-division').on('change', function () {
                syncDistrictAndStoreOptions('filter');
                clearRemoteAssetSelection($('#filter-asset'));
            });

            $('#filter-district').on('change', function () {
                const divisionId = $('#filter-division').val();
                const storeOptions = buildStoreOptions(divisionId, $(this).val());
                refreshSelectOptions($('#filter-store'), storeOptions, 'All Stores');
                clearRemoteAssetSelection($('#filter-asset'));
            });

            $('#filter-store, #filter-asset-type').on('change', function () {
                clearRemoteAssetSelection($('#filter-asset'));
            });

            $('#modal-division').on('change', function () {
                syncDistrictAndStoreOptions('modal');
                clearRemoteAssetSelection($('#modal-asset'));
            });

            $('#modal-district').on('change', function () {
                const divisionId = $('#modal-division').val();
                const storeOptions = buildStoreOptions(divisionId, $(this).val());
                refreshSelectOptions($('#modal-store'), storeOptions, 'All Stores');
                clearRemoteAssetSelection($('#modal-asset'));
            });

            $('#modal-store, #modal-asset-type').on('change', function () {
                clearRemoteAssetSelection($('#modal-asset'));
            });

            $('#modal-brand').on('change', function () {
                clearSelect2Validation($(this));
                $('#error-brand_id, #error-brand_ids').text('');
            });

            $('#btn-filter').on('click', function () {
                loadData(1);
            });

            $('#btn-reset').on('click', function () {
                $('#filter-division').val('').trigger('change.select2');
                $('#filter-asset-type').val('').trigger('change.select2');
                $('#filter-brand').val('').trigger('change.select2');
                $('#filter-status').val('');
                syncDistrictAndStoreOptions('filter');
                clearRemoteAssetSelection($('#filter-asset'));
                loadData(1);
            });

            $('#btn-add-assignment').on('click', function () {
                resetModalForm();
                assignmentModal.show();
            });

            $('#assignmentForm').on('submit', function (event) {
                event.preventDefault();
                clearErrors();

                const id = $('#assignment_id').val();
                const selectedBrands = $('#modal-brand').val() || [];

                if (id && selectedBrands.length !== 1) {
                    setSelect2Validation($('#modal-brand'));
                    $('#error-brand_id').text('Please keep exactly one brand selected while editing.');
                    return;
                }

                const payload = {
                    asset_id: $('#modal-asset').val(),
                    status: $('#modal-status').val(),
                };

                if (id) {
                    payload.brand_id = selectedBrands[0] || '';
                } else {
                    payload.brand_ids = selectedBrands;
                }

                setSaveLoading(true);

                $.ajax({
                    url: id ? endpoint(routes.update, id) : routes.store,
                    type: id ? 'PUT' : 'POST',
                    data: payload,
                    success: function (response) {
                        if (response.success === false) {
                            showToast(response.message || 'Failed to save assignment.', 'danger');
                            return;
                        }

                        assignmentModal.hide();
                        loadData(id ? paginationState.current_page : 1);
                        showToast(response.message || 'Assignment saved successfully.');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            applyValidationErrors(xhr.responseJSON?.errors || {});
                            return;
                        }

                        showToast(xhr.responseJSON?.message || 'Failed to save assignment.', 'danger');
                    },
                    complete: function () {
                        setSaveLoading(false);
                    }
                });
            });

            $(document).on('click', '.btn-view', function () {
                loadAssignmentForView($(this).data('id'));
            });

            $(document).on('click', '.btn-edit', function () {
                loadAssignmentForEdit($(this).data('id'));
            });

            $(document).on('click', '.btn-delete', function () {
                const assignmentId = $(this).data('id');
                const item = currentAssignments.find(function (assignment) {
                    return String(assignment.id) === String(assignmentId);
                });

                $('#delete-assignment-id').val(assignmentId);
                $('#delete-message').html(
                    `Remove <strong>${escapeHtml(item?.asset?.name || 'this asset')}</strong> from <strong>${escapeHtml(item?.brand?.name || 'this brand')}</strong>?`
                );
                deleteModal.show();
            });

            $('#btn-confirm-delete').on('click', function () {
                const id = $('#delete-assignment-id').val();
                if (!id) {
                    return;
                }

                setDeleteLoading(true);

                $.ajax({
                    url: endpoint(routes.destroy, id),
                    type: 'DELETE',
                    success: function (response) {
                        if (response.success === false) {
                            showToast(response.message || 'Failed to delete assignment.', 'danger');
                            return;
                        }

                        deleteModal.hide();
                        const targetPage = paginationState.current_page > 1 && currentAssignments.length === 1
                            ? paginationState.current_page - 1
                            : paginationState.current_page;
                        loadData(targetPage);
                        showToast(response.message || 'Assignment deleted successfully.');
                    },
                    error: function (xhr) {
                        showToast(xhr.responseJSON?.message || 'Failed to delete assignment.', 'danger');
                    },
                    complete: function () {
                        setDeleteLoading(false);
                    }
                });
            });

            $(document).on('click', '.pagination-link', function (event) {
                event.preventDefault();

                const page = Number($(this).data('page'));
                if (!page || page < 1 || page > paginationState.last_page || page === paginationState.current_page) {
                    return;
                }

                loadData(page);
            });

            syncDistrictAndStoreOptions('filter');
            syncDistrictAndStoreOptions('modal');
            initializeRemoteAssetSelect('#filter-asset', 'filter');
            initializeRemoteAssetSelect('#modal-asset', 'modal');
            resetModalForm();
            loadData(1);
        });
    </script>
@endpush
