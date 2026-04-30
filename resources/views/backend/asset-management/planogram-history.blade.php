@extends('backend.master')

@section('title', 'Planogram History')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Planogram History</div>

                    </div>
                    <div class="card-body">
                        <div class="mb-4 filters" id="filters">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <p class="mb-0"><i class="ri-filter-3-line text-primary fs-16"></i> Filters</p>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-filter">
                                        <i class="ri-search-line me-1"></i> Search
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light btn-wave" id="btn-reset-filter">
                                        <i class="ri-refresh-line me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="filter-store" class="form-label">Store</label>
                                    <select name="store_id" id="filter-store" class="form-select select-ele">
                                        <option value="">All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->title.' ('. $store->code .')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter-asset" class="form-label">Asset</label>
                                    <select name="asset_id" id="filter-asset" class="form-select select-ele">
                                        <option value="">All Assets</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}">{{ $asset->name.' ('. $asset->asset_code .')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter-brand" class="form-label">Brand</label>
                                    <select name="brand_id" id="filter-brand" class="form-select select-ele">
                                        <option value="">All Brands</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name.' ('. $brand->code .')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100 align-middle" data-datatable-manual="true">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Store</th>
                                        <th>Asset</th>
                                        <th>Assign Brands</th>
                                        <th>Assigned By</th>
                                        <th>Status</th>
                                        <th>View Planogram</th>
                                        <th class="d-none">Store Group</th>
                                        <th class="d-none">Asset Group</th>
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

@section('modal')
    <div class="modal fade" id="viewPlanogramPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Planogram</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="planogram-preview" class="planogram-preview-empty">
                        <div class="text-muted">Select a planogram to preview.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="planogram-open-link" target="_blank" rel="noopener" class="btn btn-primary d-none">
                        Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .planogram-preview-frame {
            width: 100%;
            height: 75vh;
            border: 0;
        }

        .planogram-preview-empty {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }

        #data-table tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')
    <script>
        $(document).ready(function () {
            const historyUrl = @json(route('assets.planogram-histories'));
            const previewModal = new bootstrap.Modal(document.getElementById('viewPlanogramPdfModal'));
            const $preview = $('#planogram-preview');
            const $openLink = $('#planogram-open-link');
            const $filterStore = $('#filter-store');
            const $filterAsset = $('#filter-asset');
            const $filterBrand = $('#filter-brand');

            $('.select-ele').select2({
                width: '100%'
            });

            function renderGroupedRows(api) {
                const rows = api.rows({ page: 'current' });
                const nodes = rows.nodes();
                const allData = rows.data();

                if (!nodes.length) {
                    return;
                }

                $(nodes).each(function () {
                    $(this).children('td').each(function () {
                        $(this).show().removeAttr('rowspan');
                    });
                });

                const items = [];
                for (let i = 0; i < allData.length; i++) {
                    items.push({
                        node: nodes[i],
                        data: allData[i],
                        storeGroup: allData[i].store_group,
                        assetGroup: allData[i].asset_group,
                    });
                }

                const pageInfo = api.page.info();
                let storeSerial = pageInfo.start;
                let i = 0;

                while (i < items.length) {
                    const storeGroupKey = items[i].storeGroup;
                    const storeStart = i;
                    storeSerial++;

                    while (i < items.length && items[i].storeGroup === storeGroupKey) {
                        i++;
                    }

                    const storeEnd = i;
                    const storeRowCount = storeEnd - storeStart;
                    const $firstStoreRow = $(items[storeStart].node).children('td');

                    $firstStoreRow.eq(0).attr('rowspan', storeRowCount).text(storeSerial);
                    $firstStoreRow.eq(1).attr('rowspan', storeRowCount);

                    for (let rowIndex = storeStart + 1; rowIndex < storeEnd; rowIndex++) {
                        $(items[rowIndex].node).children('td').eq(0).hide();
                        $(items[rowIndex].node).children('td').eq(1).hide();
                    }

                    let j = storeStart;
                    while (j < storeEnd) {
                        const assetGroupKey = items[j].assetGroup;
                        const assetStart = j;

                        while (j < storeEnd && items[j].assetGroup === assetGroupKey) {
                            j++;
                        }

                        const assetEnd = j;
                        const assetRowCount = assetEnd - assetStart;
                        const $firstAssetRow = $(items[assetStart].node).children('td');
                        const mergeColumns = [2, 4, 5, 6];

                        mergeColumns.forEach(function (columnIndex) {
                            $firstAssetRow.eq(columnIndex).attr('rowspan', assetRowCount);
                        });

                        for (let rowIndex = assetStart + 1; rowIndex < assetEnd; rowIndex++) {
                            mergeColumns.forEach(function (columnIndex) {
                                $(items[rowIndex].node).children('td').eq(columnIndex).hide();
                            });
                        }
                    }
                }
            }

            const dataTable = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                searchDelay: 400,
                ordering: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center gap-2"f>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                language: {
                    processing: 'Loading...',
                    searchPlaceholder: 'Search planogram history...',
                    sSearch: '',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ history rows',
                    infoEmpty: 'No planogram history found',
                    zeroRecords: 'No matching planogram history found',
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>"
                    }
                },
                ajax: {
                    url: historyUrl,
                    data: function (d) {
                        d.store_id = $filterStore.val();
                        d.asset_id = $filterAsset.val();
                        d.brand_id = $filterBrand.val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                    { data: 'store_display', name: 'stores.title' },
                    { data: 'asset_display', name: 'assets.name' },
                    { data: 'brand_display', name: 'brands.name' },
                    { data: 'assigned_by_display', name: 'users.name' },
                    { data: 'status_display', name: 'planogram_histories.status', searchable: false },
                    { data: 'actions', name: 'actions', searchable: false, orderable: false },
                    { data: 'store_group', name: 'store_group', visible: false, searchable: false },
                    { data: 'asset_group', name: 'asset_group', visible: false, searchable: false },
                ],
                drawCallback: function () {
                    renderGroupedRows(this.api());
                }
            });

            $('#btn-filter').on('click', function () {
                dataTable.ajax.reload(null, true);
            });

            $('#btn-reset-filter').on('click', function () {
                $filterStore.val('').trigger('change');
                $filterAsset.val('').trigger('change');
                $filterBrand.val('').trigger('change');
                dataTable.ajax.reload(null, true);
            });

            $filterStore.add($filterAsset).add($filterBrand).on('change', function () {
                dataTable.ajax.reload(null, true);
            });

            $(document).on('click', '.btn-view', function () {
                const fileUrl = $(this).data('file-url');
                const storeTitle = $(this).data('store-title') || 'Store';
                const assetName = $(this).data('asset-name') || 'Asset';

                $('#viewPlanogramPdfModal .modal-title').text(`${assetName} - ${storeTitle}`);

                if (!fileUrl) {
                    $preview.html('<div class="planogram-preview-empty"><div class="text-muted">No planogram file found.</div></div>');
                    $openLink.addClass('d-none').attr('href', '#');
                } else {
                    $preview.html(`<iframe class="planogram-preview-frame" src="${fileUrl}" title="Planogram Preview"></iframe>`);
                    $openLink.removeClass('d-none').attr('href', fileUrl);
                }

                previewModal.show();
            });

            $('#viewPlanogramPdfModal').on('hidden.bs.modal', function () {
                $preview.html('<div class="planogram-preview-empty"><div class="text-muted">Select a planogram to preview.</div></div>');
                $openLink.addClass('d-none').attr('href', '#');
            });
        });
    </script>
@endpush
