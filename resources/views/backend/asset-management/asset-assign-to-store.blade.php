@extends('backend.master')

@section('title', 'Assigned Assets')

@section('body')
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-filter-3-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-14">Filters</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">Division</label>
                                <select id="filter-division" class="form-select form-select-sm select-ele">
                                    <option value="">All Divisions</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">District</label>
                                <select id="filter-district" class="form-select form-select-sm select-ele" disabled>
                                    <option value="">Select Division First</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">Store</label>
                                <select id="filter-store" class="form-select form-select-sm select-ele" disabled>
                                    <option value="">Select District First</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">Asset Category</label>
                                <select id="filter-asset-type" class="form-select form-select-sm select-ele">
                                    <option value="">All Categories</option>
                                    @foreach($assetTypes as $assetType)
                                        <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">Asset</label>
                                <select id="filter-asset" class="form-select form-select-sm select-ele" disabled>
                                    <option value="">Select Category First</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
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
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="card-title mb-0">Assigned Assets</div>
                            <span class="badge bg-primary-transparent" id="result-count"></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="assign-table" class="table table-bordered text-nowrap w-100 mb-0">
                                <thead>
                                <tr>
                                    <th width="50">SL</th>
                                    <th>Store</th>
                                    <th>#</th>
                                    <th>Asset</th>
                                    <th>Category</th>
                                    <th>Assign Date</th>
                                    <th>Assigned By</th>
                                </tr>
                                </thead>
                                <tbody id="assign-tbody"></tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="text-center py-4 text-muted d-none">
                            <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                            <span class="fs-13">No assigned assets found for the current filters.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.store-cell {
    vertical-align: middle !important;
    background: rgba(var(--primary-rgb), .03) !important;
    border-right: 2px solid rgba(var(--primary-rgb), .12) !important;
}
.store-cell .store-name {
    font-weight: 600;
    font-size: 0.82rem;
    color: var(--default-text-color);
    display: flex;
    align-items: center;
    gap: 5px;
}
.store-cell .store-name i {
    color: rgb(var(--primary-rgb));
    font-size: 0.95rem;
}
.store-cell .store-location {
    font-size: 0.72rem;
    color: var(--text-muted);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 3px;
}
.store-cell .store-count {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-top: 4px;
}
.sl-cell {
    vertical-align: middle !important;
    text-align: center;
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.8rem;
}
#assign-table tbody tr:hover {
    background: rgba(var(--primary-rgb), .03);
}
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {
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

        function cascadeDivisionToDistrict(divisionId, $district, $store, placeholders) {
            $district.html(`<option value="">${placeholders.district}</option>`)
                .val('')
                .trigger('change.select2');

            if ($store) {
                $store.html(`<option value="">${placeholders.store}</option>`)
                    .val('')
                    .trigger('change.select2')
                    .prop('disabled', true);
            }

            if (!divisionId) {
                $district.prop('disabled', true);
                return;
            }

            $district.prop('disabled', false);

            $.get(base_url + 'get-districts/' + divisionId, function (data) {
                let options = `<option value="">${placeholders.districtAll || placeholders.district}</option>`;
                data.forEach(function (district) {
                    options += `<option value="${district.id}">${district.name}</option>`;
                });
                $district.html(options).trigger('change.select2');
            });
        }

        function cascadeDistrictToStore(districtId, $store, placeholders) {
            $store.html(`<option value="">${placeholders.store}</option>`)
                .val('')
                .trigger('change.select2');

            if (!districtId) {
                $store.prop('disabled', true);
                return;
            }

            $store.prop('disabled', false);

            $.get(base_url + 'get-stores-by-district/' + districtId, function (data) {
                let options = `<option value="">${placeholders.storeAll || placeholders.store}</option>`;
                data.forEach(function (store) {
                    options += `<option value="${store.id}">${store.title} (${store.code})</option>`;
                });
                $store.html(options).trigger('change.select2');
            });
        }

        function cascadeTypeToAsset(typeId, $asset, placeholders) {
            $asset.html(`<option value="">${placeholders.asset}</option>`)
                .val('')
                .trigger('change.select2');

            if (!typeId) {
                $asset.prop('disabled', true);
                return;
            }

            $asset.prop('disabled', false);

            $.get(base_url + 'get-assets-by-type/' + typeId, function (data) {
                let options = `<option value="">${placeholders.assetAll || placeholders.asset}</option>`;
                data.forEach(function (asset) {
                    options += `<option value="${asset.id}">${asset.name} (${asset.asset_code})</option>`;
                });
                $asset.html(options).trigger('change.select2');
            });
        }

        const filterPlaceholders = {
            district: 'Select Division First',
            districtAll: 'All Districts',
            store: 'Select District First',
            storeAll: 'All Stores',
            asset: 'Select Category First',
            assetAll: 'All Assets'
        };

        $('#filter-division').on('change', function () {
            cascadeDivisionToDistrict($(this).val(), $('#filter-district'), $('#filter-store'), filterPlaceholders);
        });

        $('#filter-district').on('change', function () {
            cascadeDistrictToStore($(this).val(), $('#filter-store'), filterPlaceholders);
        });

        $('#filter-asset-type').on('change', function () {
            cascadeTypeToAsset($(this).val(), $('#filter-asset'), filterPlaceholders);
        });

        $('#btn-filter').on('click', loadData);

        $('#btn-reset').on('click', function () {
            $('#filter-division').val('').trigger('change').trigger('change.select2');
            $('#filter-asset-type').val('').trigger('change').trigger('change.select2');
            loadData();
        });

        function loadData() {
            const params = {};
            const fields = {
                division_id: '#filter-division',
                district_id: '#filter-district',
                store_id: '#filter-store',
                asset_type_id: '#filter-asset-type',
                asset_id: '#filter-asset'
            };

            Object.entries(fields).forEach(function ([key, selector]) {
                const value = $(selector).val();
                if (value) {
                    params[key] = value;
                }
            });

            const $button = $('#btn-filter');
            $button.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...');

            $.get(base_url + 'assign-assets/filter', params)
                .done(function (data) {
                    renderTable(data);
                })
                .fail(function () {
                    $('#assign-tbody').empty();
                    $('#assign-table').hide();
                    $('#empty-state').removeClass('d-none').show();
                    $('#result-count').text('');
                    showToast('Failed to load assigned assets.', 'danger');
                })
                .always(function () {
                    $button.prop('disabled', false)
                        .html('<i class="ri-search-line me-1"></i> Search');
                });
        }

        function renderTable(data) {
            const $tbody = $('#assign-tbody');
            $tbody.empty();

            if (!data.length) {
                $('#assign-table').hide();
                $('#empty-state').removeClass('d-none').show();
                $('#result-count').text('');
                return;
            }

            $('#assign-table').show();
            $('#empty-state').hide();

            data.sort(function (left, right) {
                return (left.store?.title || '').localeCompare(right.store?.title || '');
            });

            const groups = {};
            data.forEach(function (item) {
                const storeId = item.store_id || 0;
                if (!groups[storeId]) {
                    groups[storeId] = [];
                }
                groups[storeId].push(item);
            });

            let storeSerial = 0;

            Object.values(groups).forEach(function (items) {
                storeSerial++;

                const store = items[0].store || {};
                const location = [store.division?.name, store.district?.name]
                    .filter(Boolean)
                    .join(', ');
                const storeLabel = store.title || 'Unassigned';
                const storeCode = store.code ? `(${store.code})` : '';

                items.forEach(function (item, index) {
                    const asset = item.asset || {};
                    const dateLabel = item.assign_date
                        ? new Date(item.assign_date).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        })
                        : '-';

                    let row = '<tr>';

                    if (index === 0) {
                        const span = items.length;
                        row += `<td class="sl-cell" rowspan="${span}">${storeSerial}</td>`;
                        row += `<td class="store-cell" rowspan="${span}">
                            <div class="store-name"><i class="ri-store-2-line"></i> ${storeLabel} <span class="text-muted fw-normal fs-11">${storeCode}</span></div>
                            ${location ? `<div class="store-location"><i class="ri-map-pin-2-line"></i> ${location}</div>` : ''}
                            <div class="store-count">${span} asset${span > 1 ? 's' : ''} assigned</div>
                        </td>`;
                    }

                    row += `<td class="text-muted">${index + 1}</td>`;
                    row += `<td><div class="fw-semibold">${asset.name || '-'}</div><small class="text-muted">${asset.asset_code || ''}</small></td>`;
                    row += `<td>${asset.asset_type?.name || '-'}</td>`;
                    row += `<td>${dateLabel}</td>`;
                    row += `<td>${item.assigned_by?.name || '-'}</td>`;
                    row += '</tr>';

                    $tbody.append(row);
                });
            });

            const storeCount = Object.keys(groups).length;
            $('#result-count').text(`${data.length} asset(s) in ${storeCount} store(s)`);
        }

        loadData();
    });
    </script>
@endpush
