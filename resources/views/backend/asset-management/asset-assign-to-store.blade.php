@extends('backend.master')

@section('title', 'Asset Assign to Store')

@section('body')
    <div class="container-fluid mt-4">

        {{-- ── Filter Bar ─────────────────────────────────────────────────────── --}}
        <div class="row mb-3">
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ri-filter-3-line text-primary fs-16"></i>
                            <span class="fw-semibold fs-14">Filters</span>
                        </div>
                        <div class="row g-2">
                            {{-- Location filters --}}
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

                            {{-- Asset filters --}}
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

        {{-- ── Table Card ─────────────────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="card-title mb-0">Assigned Assets</div>
                            <span class="badge bg-primary-transparent" id="result-count"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-assign-new">
                            <i class="ri-add-line me-1"></i> Assign Asset
                        </button>
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
                                    <th>Charge</th>
                                    <th>Assigned By</th>
                                    <th width="80">Actions</th>
                                </tr>
                                </thead>
                                <tbody id="assign-tbody">
                                </tbody>
                            </table>
                        </div>
                        <div id="empty-state" class="text-center py-4 text-muted d-none">
                            <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                            <span class="fs-13">Use filters above and click <strong>Search</strong> to load assignments.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')

    {{-- ── Assign / Edit Modal ────────────────────────────────────────────── --}}
    <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-semibold" id="assignModalLabel">
                        <i class="ri-links-line me-2 text-primary"></i>Assign Asset to Store
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignForm">
                    @csrf
                    <input type="hidden" id="assignment_id" value="">
                    <div class="modal-body">

                        {{-- Section: Select Store --}}
                        <p class="assign-section-label"><i class="ri-store-2-line me-1"></i>Store Location</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="modal-division" class="form-label">Division <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal-division">
                                    <option value="">— Select Division —</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modal-district" class="form-label">District <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal-district" disabled>
                                    <option value="">Select Division First</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="modal-store" class="form-label">Store <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal-store" name="store_id" disabled>
                                    <option value="">Select District First</option>
                                </select>
                                <div class="invalid-feedback" id="error-store_id"></div>
                            </div>
                        </div>

                        {{-- Section: Select Asset --}}
                        <p class="assign-section-label"><i class="ri-box-3-line me-1"></i>Asset Details</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="modal-asset-type" class="form-label">Asset Category <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal-asset-type">
                                    <option value="">— Select Category —</option>
                                    @foreach($assetTypes as $assetType)
                                        <option value="{{ $assetType->id }}">{{ $assetType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modal-asset" class="form-label">Asset <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal-asset" name="asset_id" disabled>
                                    <option value="">Select Category First</option>
                                </select>
                                <div class="invalid-feedback" id="error-asset_id"></div>
                            </div>
                        </div>

                        {{-- Section: Assignment Info --}}
                        <p class="assign-section-label"><i class="ri-calendar-check-line me-1"></i>Assignment Info</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="assign_date" class="form-label">Assign Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="assign_date" name="assign_date"
                                    value="{{ date('Y-m-d') }}">
                                <div class="invalid-feedback" id="error-assign_date"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="asset_charge" class="form-label">Asset Charge</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="asset_charge" name="asset_charge" placeholder="0.00">
                                </div>
                                <div class="invalid-feedback d-block" id="error-asset_charge"></div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="ri-close-line me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="btn-save">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Assign</span>
                            <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Delete Confirmation Modal ──────────────────────────────────────── --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4 pb-2">
                    <div class="mb-3">
                        <span class="avatar avatar-lg bg-danger-transparent rounded-circle">
                            <i class="ri-delete-bin-line text-danger fs-24"></i>
                        </span>
                    </div>
                    <h6 class="fw-semibold mb-1">Remove Assignment?</h6>
                    <p class="text-muted fs-13 mb-0">
                        <strong id="delete-asset-name" class="text-dark"></strong>
                        <br>from <strong id="delete-store-name" class="text-dark"></strong>
                    </p>
                    <p class="text-danger fs-11 mt-1 mb-0">This action cannot be undone.</p>
                    <input type="hidden" id="delete-assignment-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-sm btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger px-4" id="btn-confirm-delete">
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
.assign-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgb(var(--primary-rgb));
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--default-border);
    margin-bottom: 0.875rem;
}
.btn-list { display: flex; gap: 3px; }
.btn-list .btn {
    width: 26px; height: 26px; min-width: 26px;
    padding: 0; font-size: 0.7rem;
    display: inline-flex; align-items: center; justify-content: center;
}
/* ── Store rowspan cell ── */
.store-cell {
    vertical-align: middle !important;
    background: rgba(var(--primary-rgb), .03) !important;
    border-right: 2px solid rgba(var(--primary-rgb), .12) !important;
}
.store-cell .store-name {
    font-weight: 600; font-size: 0.82rem;
    color: var(--default-text-color);
    display: flex; align-items: center; gap: 5px;
}
.store-cell .store-name i { color: rgb(var(--primary-rgb)); font-size: 0.95rem; }
.store-cell .store-location {
    font-size: 0.72rem; color: var(--text-muted);
    margin-top: 2px; display: flex; align-items: center; gap: 3px;
}
.store-cell .store-count {
    font-size: 0.7rem; color: var(--text-muted);
    margin-top: 4px;
}
.sl-cell { vertical-align: middle !important; text-align: center; font-weight: 600; color: var(--text-muted); font-size: 0.8rem; }
#assign-table tbody tr:hover { background: rgba(var(--primary-rgb), .03); }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    <script>
    $(document).ready(function () {

        // ── Bootstrap modals & AJAX setup ────────────────────────────────────
        const assignModal   = new bootstrap.Modal(document.getElementById('assignModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const assignUrl = base_url + 'store/assign-assets';

        // ── Utility helpers ──────────────────────────────────────────────────
        function setBtnLoading($btn, loading) {
            $btn.prop('disabled', loading);
            $btn.find('.spinner-border').toggleClass('d-none', !loading);
        }

        function showToast(message, type) {
            $(`<div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`).appendTo('body').delay(3000).queue(function () { $(this).remove(); });
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('[id^="error-"]').text('');
        }

        // ── Cascade helpers ──────────────────────────────────────────────────
        function cascadeDivisionToDistrict(divisionId, $district, $store, ph) {
            $district.html(`<option value="">${ph.district}</option>`).val('').trigger('change.select2');
            if ($store) $store.html(`<option value="">${ph.store}</option>`).val('').trigger('change.select2').prop('disabled', true);
            if (!divisionId) { $district.prop('disabled', true); return; }
            $district.prop('disabled', false);
            $.get(base_url + 'get-districts/' + divisionId, function (data) {
                let o = `<option value="">${ph.districtAll || ph.district}</option>`;
                data.forEach(d => o += `<option value="${d.id}">${d.name}</option>`);
                $district.html(o).trigger('change.select2');
            });
        }
        function cascadeDistrictToStore(districtId, $store, ph) {
            $store.html(`<option value="">${ph.store}</option>`).val('').trigger('change.select2');
            if (!districtId) { $store.prop('disabled', true); return; }
            $store.prop('disabled', false);
            $.get(base_url + 'get-stores-by-district/' + districtId, function (data) {
                let o = `<option value="">${ph.storeAll || ph.store}</option>`;
                data.forEach(s => o += `<option value="${s.id}">${s.title} (${s.code})</option>`);
                $store.html(o).trigger('change.select2');
            });
        }
        function cascadeTypeToAsset(typeId, $asset, ph) {
            $asset.html(`<option value="">${ph.asset}</option>`).val('').trigger('change.select2');
            if (!typeId) { $asset.prop('disabled', true); return; }
            $asset.prop('disabled', false);
            $.get(base_url + 'get-assets-by-type/' + typeId, function (data) {
                let o = `<option value="">${ph.assetAll || ph.asset}</option>`;
                data.forEach(a => o += `<option value="${a.id}">${a.name} (${a.asset_code})</option>`);
                $asset.html(o).trigger('change.select2');
            });
        }

        // ── FILTER cascades ──────────────────────────────────────────────────
        const fPH = { district: 'Select Division First', districtAll: 'All Districts', store: 'Select District First', storeAll: 'All Stores', asset: 'Select Category First', assetAll: 'All Assets' };
        $('#filter-division').on('change', function () { cascadeDivisionToDistrict($(this).val(), $('#filter-district'), $('#filter-store'), fPH); });
        $('#filter-district').on('change', function () { cascadeDistrictToStore($(this).val(), $('#filter-store'), fPH); });
        $('#filter-asset-type').on('change', function () { cascadeTypeToAsset($(this).val(), $('#filter-asset'), fPH); });

        // ── MODAL cascades ───────────────────────────────────────────────────
        const mPH = { district: '— Select District —', store: '— Select Store —', asset: '— Select Asset —' };
        $('#modal-division').on('change', function () { cascadeDivisionToDistrict($(this).val(), $('#modal-district'), $('#modal-store'), mPH); });
        $('#modal-district').on('change', function () { cascadeDistrictToStore($(this).val(), $('#modal-store'), mPH); });
        $('#modal-asset-type').on('change', function () { cascadeTypeToAsset($(this).val(), $('#modal-asset'), mPH); });

        // ══════════════════════════════════════════════════════════════════════
        //  TABLE RENDERING WITH ROWSPAN
        // ══════════════════════════════════════════════════════════════════════

        $('#btn-filter').on('click', loadData);

        function loadData() {
            const params = {};
            const fields = { division_id: '#filter-division', district_id: '#filter-district', store_id: '#filter-store', asset_type_id: '#filter-asset-type', asset_id: '#filter-asset' };
            Object.entries(fields).forEach(([k, sel]) => { const v = $(sel).val(); if (v) params[k] = v; });

            const $btn = $('#btn-filter');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...');

            $.get(base_url + 'assign-assets/filter', params, function (data) {
                renderTable(data);
            }).always(function () {
                $btn.prop('disabled', false).html('<i class="ri-search-line me-1"></i> Search');
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

            // Group by store_id
            data.sort((a, b) => (a.store?.title || '').localeCompare(b.store?.title || ''));

            const groups = {};
            data.forEach(item => {
                const sid = item.store_id || 0;
                if (!groups[sid]) groups[sid] = [];
                groups[sid].push(item);
            });

            let storeSl = 0;
            Object.values(groups).forEach(items => {
                storeSl++;
                const store = items[0].store || {};
                const location = [store.division?.name, store.district?.name].filter(Boolean).join(', ');
                const storeLabel = store.title || 'Unassigned';
                const storeCode  = store.code ? `(${store.code})` : '';

                items.forEach((item, idx) => {
                    const asset = item.asset || {};
                    const dateFmt = item.assign_date
                        ? new Date(item.assign_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                        : '—';

                    let row = '<tr>';

                    // SL + Store cell only on first row of group (rowspan)
                    if (idx === 0) {
                        const span = items.length;
                        row += `<td class="sl-cell" rowspan="${span}">${storeSl}</td>`;
                        row += `<td class="store-cell" rowspan="${span}">
                            <div class="store-name"><i class="ri-store-2-line"></i> ${storeLabel} <span class="text-muted fw-normal fs-11">${storeCode}</span></div>
                            ${location ? `<div class="store-location"><i class="ri-map-pin-2-line"></i> ${location}</div>` : ''}
                            <div class="store-count">${span} asset${span > 1 ? 's' : ''} assigned</div>
                        </td>`;
                    }

                    row += `<td class="text-muted">${idx + 1}</td>`;
                    row += `<td><div class="fw-semibold">${asset.name || '—'}</div><small class="text-muted">${asset.asset_code || ''}</small></td>`;
                    row += `<td>${asset.asset_type?.name || '—'}</td>`;
                    row += `<td>${dateFmt}</td>`;
                    row += `<td>${item.asset_charge ? '৳ ' + Number(item.asset_charge).toFixed(2) : '—'}</td>`;
                    row += `<td>${item.assigned_by?.name || '—'}</td>`;
                    row += `<td>
                        <div class="btn-list">
                            <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="${item.id}" title="Edit"><i class="ri-edit-box-line"></i></button>
                            <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="${item.id}" data-asset="${asset.name || ''}" data-store="${storeLabel}" title="Delete"><i class="ri-delete-bin-line"></i></button>
                        </div>
                    </td>`;
                    row += '</tr>';
                    $tbody.append(row);
                });
            });

            const storeCount = Object.keys(groups).length;
            $('#result-count').text(`${data.length} asset(s) in ${storeCount} store(s)`);
        }

        // ── Reset filters ────────────────────────────────────────────────────
        $('#btn-reset').on('click', function () {
            $('#filter-division').val('').trigger('change').trigger('change.select2');
            $('#filter-asset-type').val('').trigger('change').trigger('change.select2');
            $('#assign-tbody').empty();
            $('#assign-table').hide();
            $('#empty-state').removeClass('d-none').show();
            $('#result-count').text('');
        });

        // Show empty state initially
        $('#assign-table').hide();
        $('#empty-state').removeClass('d-none');

        // ── Open Assign Modal (new) ──────────────────────────────────────────
        $('#btn-assign-new').on('click', function () {
            resetAssignForm();
            $('#assignModalLabel').html('<i class="ri-links-line me-2 text-primary"></i>Assign Asset to Store');
            $('#btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Assign');
            assignModal.show();
        });

        // ── Open Edit Modal ──────────────────────────────────────────────────
        $(document).on('click', '.btn-edit', function () {
            resetAssignForm();
            const id = $(this).data('id');
            $('#assignModalLabel').html('<i class="ri-edit-box-line me-2 text-primary"></i>Edit Assignment');
            $('#btn-save .btn-text').html('<i class="ri-save-line me-1"></i>Update');

            $.get(assignUrl + '/' + id + '/edit', function (data) {
                $('#assignment_id').val(data.id);
                $('#assign_date').val(data.assign_date ? data.assign_date.substring(0, 10) : '');
                $('#asset_charge').val(data.asset_charge);

                // Load store cascade: division → district → store
                if (data.store) {
                    const store = data.store;
                    $.get(base_url + 'stores/' + store.id, function (sd) {
                        if (sd.division_id) {
                            $('#modal-division').val(sd.division_id).trigger('change.select2');
                            setTimeout(function () {
                                $('#modal-district').prop('disabled', false);
                                $.get(base_url + 'get-districts/' + sd.division_id, function (districts) {
                                    let o = '<option value="">— Select District —</option>';
                                    districts.forEach(d => o += `<option value="${d.id}">${d.name}</option>`);
                                    $('#modal-district').html(o);
                                    if (sd.district_id) {
                                        $('#modal-district').val(sd.district_id).trigger('change.select2');
                                        setTimeout(function () {
                                            $.get(base_url + 'get-stores-by-district/' + sd.district_id, function (stores) {
                                                let s = '<option value="">— Select Store —</option>';
                                                stores.forEach(st => s += `<option value="${st.id}">${st.title} (${st.code})</option>`);
                                                $('#modal-store').html(s).prop('disabled', false).val(store.id).trigger('change.select2');
                                            });
                                        }, 100);
                                    }
                                });
                            }, 100);
                        }
                    });
                }

                // Load asset cascade: type → asset
                if (data.asset) {
                    const asset = data.asset;
                    if (asset.asset_type_id) {
                        $('#modal-asset-type').val(asset.asset_type_id).trigger('change.select2');
                        setTimeout(function () {
                            $.get(base_url + 'get-assets-by-type/' + asset.asset_type_id, function (assets) {
                                let o = '<option value="">— Select Asset —</option>';
                                assets.forEach(a => o += `<option value="${a.id}">${a.name} (${a.asset_code})</option>`);
                                $('#modal-asset').html(o).prop('disabled', false).val(asset.id).trigger('change.select2');
                            });
                        }, 100);
                    }
                }

                assignModal.show();
            });
        });

        // ── Submit Form (Create / Update) ────────────────────────────────────
        $('#assignForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#assignment_id').val();
            const payload = {
                asset_id:     $('#modal-asset').val(),
                store_id:     $('#modal-store').val(),
                assign_date:  $('#assign_date').val(),
                asset_charge: $('#asset_charge').val() || 0,
            };

            const $btn = $('#btn-save');
            setBtnLoading($btn, true);

            $.ajax({
                url:  id ? (assignUrl + '/' + id) : assignUrl,
                type: id ? 'PUT' : 'POST',
                data: payload,
                success: function (res) {
                    assignModal.hide();
                    showToast(res.message, 'success');
                    loadData();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors || {}, function (field, messages) {
                            $(`#modal-${field.replace('_id', '')}, [name="${field}"]`).addClass('is-invalid');
                            $(`#error-${field}`).text(messages[0]);
                        });
                    } else {
                        showToast('Something went wrong. Please try again.', 'danger');
                    }
                },
                complete: () => setBtnLoading($btn, false),
            });
        });

        // ── Delete ───────────────────────────────────────────────────────────
        $(document).on('click', '.btn-delete', function () {
            $('#delete-assignment-id').val($(this).data('id'));
            $('#delete-asset-name').text($(this).data('asset'));
            $('#delete-store-name').text($(this).data('store'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const $btn = $(this);
            setBtnLoading($btn, true);
            $.ajax({
                url:  assignUrl + '/' + $('#delete-assignment-id').val(),
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    loadData();
                },
                error: () => showToast('Failed to delete assignment.', 'danger'),
                complete: () => setBtnLoading($btn, false),
            });
        });

        // ── Reset assign form ────────────────────────────────────────────────
        function resetAssignForm() {
            $('#assignForm')[0].reset();
            $('#assignment_id').val('');
            $('#assign_date').val(new Date().toISOString().substring(0, 10));
            $('#asset_charge').val('');
            $('#modal-division').val('').trigger('change.select2');
            $('#modal-district').html('<option value="">— Select District —</option>').prop('disabled', true).trigger('change.select2');
            $('#modal-store').html('<option value="">— Select Store —</option>').prop('disabled', true).trigger('change.select2');
            $('#modal-asset-type').val('').trigger('change.select2');
            $('#modal-asset').html('<option value="">— Select Asset —</option>').prop('disabled', true).trigger('change.select2');
            clearErrors();
        }

    });
    </script>
@endpush
