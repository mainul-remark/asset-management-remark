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
                                <select id="filter-district" class="form-select form-select-sm select-ele">
                                    <option value="">Select District</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 mb-1">Store</label>
                                <select id="filter-store" class="form-select form-select-sm select-ele">
                                    <option value="">Select Store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }} ({{ $store->code }})</option>
                                    @endforeach
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
{{--                                    <th width="60">SL</th>--}}
                                    <th>Store</th>
                                    <th width="60">#</th>
                                    <th>Asset</th>
                                    <th>Key Visual</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
#assign-table tbody td {
    vertical-align: middle;
}
#assign-table tbody tr:hover {
    background: rgba(var(--primary-rgb), .03);
}
#assign-table .sl-cell {
    vertical-align: middle !important;
    text-align: center;
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.8rem;
}
#assign-table .store-cell {
    vertical-align: middle !important;
    background: rgba(var(--primary-rgb), .03) !important;
    border-right: 2px solid rgba(var(--primary-rgb), .12) !important;
}
#assign-table .group-item-cell {
    text-align: center;
    font-weight: 600;
    color: var(--text-muted);
    font-size: 0.8rem;
}
#assign-table .asset-name {
    font-weight: 600;
    color: var(--default-text-color);
}
#assign-table .asset-code {
    display: inline-block;
    margin-top: 2px;
    font-size: 0.72rem;
    color: var(--text-muted);
}
#assign-table_wrapper .dataTables_processing {
    border-radius: 10px;
    border: 1px solid rgba(var(--primary-rgb), .15);
    box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
    background: var(--custom-white, #fff);
    z-index: 20;
}
.store-group-heading {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.store-group-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    color: var(--default-text-color);
}
.store-group-name i {
    color: rgb(var(--primary-rgb));
    font-size: 1rem;
}
.store-group-code {
    font-size: 0.74rem;
    font-weight: 500;
    color: var(--text-muted);
}
.store-group-location {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    color: var(--text-muted);
}
.store-count {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-top: 4px;
}
#assign-table .kv-name {
    font-weight: 600;
    color: var(--default-text-color);
}
#assign-table .kv-code {
    display: inline-block;
    margin-top: 2px;
    font-size: 0.72rem;
    color: var(--text-muted);
}
#assign-table .kv-info .badge {
    margin-top: 3px;
}
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {
        const assignAssetsDatatableUrl = @json(route('assets.assign-assets.datatable'));
        const initialDistrictOptions = $('#filter-district').html();
        const initialStoreOptions = $('#filter-store').html();

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
                    .prop('disabled', false);
            }

            if (!divisionId) {
                $district.html(initialDistrictOptions)
                    .val('')
                    .trigger('change.select2')
                    .prop('disabled', false);

                if ($store) {
                    $store.html(initialStoreOptions)
                        .val('')
                        .trigger('change.select2')
                        .prop('disabled', false);
                }

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
                $store.html(initialStoreOptions)
                    .val('')
                    .trigger('change.select2')
                    .prop('disabled', false);
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

        function collectFilters() {
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

            return params;
        }

        function setFilterButtonLoading(isLoading) {
            const $button = $('#btn-filter');

            $button.prop('disabled', isLoading)
                .html(
                    isLoading
                        ? '<span class="spinner-border spinner-border-sm me-1"></span> Searching...'
                        : '<i class="ri-search-line me-1"></i> Search'
                );
        }

        function renderStoreGroupRows(api) {
            const rows = api.rows({ page: 'current' });
            const nodes = rows.nodes();
            const allData = rows.data();

            if (!nodes.length) return;

            // Reset all cells
            $(nodes).each(function () {
                $(this).children('td').each(function () {
                    $(this).show().removeAttr('rowspan')
                        .removeClass('sl-cell store-cell group-item-cell');
                });
            });

            // Build items array
            const items = [];
            for (let i = 0; i < allData.length; i++) {
                items.push({
                    node: nodes[i],
                    data: allData[i],
                    storeGroup: allData[i].store_group,
                    assetGroup: allData[i].asset_group,
                });
            }

            // Two-level grouping: Store > Asset
            let i = 0;

            while (i < items.length) {
                const storeGroupKey = items[i].storeGroup;
                const storeStart = i;

                // Find all rows in this store group
                while (i < items.length && items[i].storeGroup === storeGroupKey) {
                    i++;
                }
                const storeEnd = i;
                const storeRowCount = storeEnd - storeStart;

                // Store-level rowspan on Store (col 0)
                const $firstStoreRow = $(items[storeStart].node).children('td');
                $firstStoreRow.eq(0)
                    .attr('rowspan', storeRowCount)
                    .addClass('store-cell')
                    .html(items[storeStart].data.store_summary || '');

                // Hide Store for subsequent rows
                for (let r = storeStart + 1; r < storeEnd; r++) {
                    $(items[r].node).children('td').eq(0).hide();
                }

                // Asset-level grouping within this store
                let assetIndex = 0;
                let j = storeStart;

                while (j < storeEnd) {
                    const assetGroupKey = items[j].assetGroup;
                    const assetStart = j;
                    assetIndex++;

                    while (j < storeEnd && items[j].assetGroup === assetGroupKey) {
                        j++;
                    }
                    const assetEnd = j;
                    const assetRowCount = assetEnd - assetStart;

                    // Asset-level rowspan on # (col 1) and Asset (col 2)
                    const $firstAssetRow = $(items[assetStart].node).children('td');
                    $firstAssetRow.eq(1)
                        .attr('rowspan', assetRowCount)
                        .addClass('group-item-cell')
                        .text(assetIndex);
                    $firstAssetRow.eq(2)
                        .attr('rowspan', assetRowCount);

                    // Hide # and Asset for subsequent KV rows
                    for (let r = assetStart + 1; r < assetEnd; r++) {
                        $(items[r].node).children('td').eq(1).hide();
                        $(items[r].node).children('td').eq(2).hide();
                    }
                }

                // Update store summary with distinct asset count
                const distinctAssets = new Set();
                for (let r = storeStart; r < storeEnd; r++) {
                    distinctAssets.add(items[r].assetGroup);
                }
                const assetCount = distinctAssets.size;
                $firstStoreRow.eq(0).append(
                    `<div class="store-count">${assetCount} asset${assetCount > 1 ? 's' : ''} assigned</div>`
                );
            }
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

        const assignTable = $('#assign-table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            searchDelay: 500,
            stateSave: true,
            orderFixed: [[4, 'asc'], [5, 'asc']],
            order: [[2, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            dom: '<"d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"<"d-flex align-items-center"l><"d-flex align-items-center gap-2"f>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            language: {
                processing: 'Loading...',
                searchPlaceholder: 'Search assigned assets...',
                sSearch: '',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ assigned assets',
                infoEmpty: 'No assigned assets found',
                zeroRecords: 'No matching assigned assets found',
                paginate: {
                    previous: "<i class='ri-arrow-left-s-line'></i>",
                    next: "<i class='ri-arrow-right-s-line'></i>"
                }
            },
            ajax: {
                url: assignAssetsDatatableUrl,
                data: function (d) {
                    Object.assign(d, collectFilters());
                },
                error: function (xhr) {
                    const message = xhr.status === 422
                        ? xhr.responseJSON?.message || 'The selected filter values are not valid.'
                        : 'Failed to load assigned assets.';

                    showToast(message, 'danger');
                    setFilterButtonLoading(false);
                }
            },
            columns: [
                // { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center text-muted fw-semibold' },
                { data: 'store_summary', name: 'store_summary' },
                { data: null, name: 'group_item_index', searchable: false, orderable: false, defaultContent: '' },
                { data: 'asset_display', name: 'asset_display' },
                { data: 'kv_display', name: 'kv_display' },
                { data: 'store_group', name: 'store_group', visible: false },
                { data: 'asset_group', name: 'asset_group', visible: false },
            ],
            drawCallback: function () {
                const api = this.api();
                const info = api.page.info();

                renderStoreGroupRows(api);
                $('#result-count').text(`${info.recordsDisplay} asset(s)`);
                setFilterButtonLoading(false);
            }
        });

        $('#btn-filter').on('click', function () {
            setFilterButtonLoading(true);
            assignTable.ajax.reload(null, true);
        });

        $('#btn-reset').on('click', function () {
            $('#filter-division').val('').trigger('change.select2');
            $('#filter-district')
                .html(initialDistrictOptions)
                .val('')
                .trigger('change.select2')
                .prop('disabled', false);
            $('#filter-store')
                .html(initialStoreOptions)
                .val('')
                .trigger('change.select2')
                .prop('disabled', false);
            $('#filter-asset-type').val('').trigger('change.select2');
            $('#filter-asset')
                .html('<option value="">Select Category First</option>')
                .val('')
                .trigger('change.select2')
                .prop('disabled', true);
            assignTable.search('');
            $('#assign-table_filter input[type="search"]').val('');
            $('#result-count').text('');
            setFilterButtonLoading(true);
            assignTable.ajax.reload(null, true);
        });
    });
    </script>
@endpush
