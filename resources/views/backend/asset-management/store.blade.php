@extends('backend.master')

@section('title', 'Stores')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Store Management</div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-store">
                            <i class="ri-add-line me-1"></i> Add Store
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Code</th>
                                        <th>Location</th>
                                        <th>Contact</th>
                                        <th>Manager</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($stores as $store)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $store->title }}</td>
                                        <td><span class="badge bg-primary-transparent">{{ $store->code }}</span></td>
                                        <td>
                                            @if($store->area || $store->district)
                                                {{ $store->area }}{{ $store->area && $store->district ? ', ' : '' }}{{ $store->district?->name }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $store->shop_official_mobile ?: '—' }}</td>
                                        <td>{{ $store->storeManager?->name ?: '—' }}</td>
                                        <td>
                                            @if($store->status == 1)
                                                <span class="badge bg-outline-success">Active</span>
                                            @else
                                                <span class="badge bg-outline-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $store->id }}" title="View"><i class="ri-eye-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $store->id }}" title="Edit"><i class="ri-edit-box-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $store->id }}" data-name="{{ $store->title }}" title="Delete"><i class="ri-delete-bin-line"></i></button>
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
    {{-- Create / Edit Modal --}}
    <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="storeModalLabel">Add Store</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="storeForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="store_id" value="">

                        {{-- Basic Info --}}
                        <h6 class="fw-semibold text-muted mb-3"><i class="ri-store-2-line me-1"></i> Basic Information</h6>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="title" class="form-label">Store Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter store title">
                                <div class="invalid-feedback" id="error-title"></div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" readonly id="code" name="code" placeholder="Auto" maxlength="3">
                                <div class="invalid-feedback" id="error-code"></div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="store_code" class="form-label">Store Code</label>
                                <input type="text" class="form-control" id="store_code" name="store_code" placeholder="e.g. S001">
                                <div class="invalid-feedback" id="error-store_code"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="total_area_sqft" class="form-label">Area (sqft)</label>
                                <input type="number" step="0.01" class="form-control" id="total_area_sqft" name="total_area_sqft" placeholder="0.00">
                                <div class="invalid-feedback" id="error-total_area_sqft"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="monthly_rent" class="form-label">Monthly Rent</label>
                                <input type="number" step="0.01" class="form-control" id="monthly_rent" name="monthly_rent" placeholder="0.00">
                                <div class="invalid-feedback" id="error-monthly_rent"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="per_sqr_feet_rent" class="form-label">Per Sqft Rent</label>
                                <input type="number" step="0.01" class="form-control" id="per_sqr_feet_rent" name="per_sqr_feet_rent" placeholder="0.00">
                                <div class="invalid-feedback" id="error-per_sqr_feet_rent"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="opened_date" class="form-label">Opened Date</label>
                                <input type="date" class="form-control" id="opened_date" name="opened_date">
                                <div class="invalid-feedback" id="error-opened_date"></div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="store_manager_id" class="form-label">Store Manager</label>
                                <select class="form-select" id="store_manager_id" name="store_manager_id">
                                    <option value="">— Select Manager —</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-store_manager_id"></div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Location --}}
                        <h6 class="fw-semibold text-muted mb-3"><i class="ri-map-pin-line me-1"></i> Location</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="division_id" class="form-label">Division</label>
                                <select class="form-select" id="division_id" name="division_id">
                                    <option value="">— Select Division —</option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="error-division_id"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district_id" class="form-label">District</label>
                                <select class="form-select" id="district_id" name="district_id" disabled>
                                    <option value="">— Select District —</option>
                                </select>
                                <div class="invalid-feedback" id="error-district_id"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="thana_id" class="form-label">Thana</label>
                                <select class="form-select" id="thana_id" name="thana_id" disabled>
                                    <option value="">— Select Thana —</option>
                                </select>
                                <div class="invalid-feedback" id="error-thana_id"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="area" class="form-label">Area/Locality</label>
                                <input type="text" class="form-control" id="area" name="area" placeholder="Area or locality name">
                                <div class="invalid-feedback" id="error-area"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Postal Code">
                                <div class="invalid-feedback" id="error-postal_code"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Full Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Full address"></textarea>
                            <div class="invalid-feedback" id="error-address"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="e.g. 23.8103">
                                <div class="invalid-feedback" id="error-latitude"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="e.g. 90.4125">
                                <div class="invalid-feedback" id="error-longitude"></div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Contact Info --}}
                        <h6 class="fw-semibold text-muted mb-3"><i class="ri-phone-line me-1"></i> Contact Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contact_persion" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_persion" name="contact_persion" placeholder="Contact person name">
                                <div class="invalid-feedback" id="error-contact_persion"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shop_official_mobile" class="form-label">Official Mobile</label>
                                <input type="text" class="form-control" id="shop_official_mobile" name="shop_official_mobile" placeholder="Mobile number">
                                <div class="invalid-feedback" id="error-shop_official_mobile"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shop_official_email" class="form-label">Official Email</label>
                                <input type="email" class="form-control" id="shop_official_email" name="shop_official_email" placeholder="Email address">
                                <div class="invalid-feedback" id="error-shop_official_email"></div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Layout Files --}}
                        <h6 class="fw-semibold text-muted mb-3"><i class="ri-layout-3-line me-1"></i> Store Layout</h6>
                        <div class="mb-3">
                            <label for="store_layout_pdf" class="form-label">Layout PDF</label>
                            <input type="file" class="filepond-pdf" id="store_layout_pdf" name="store_layout_pdf" accept="application/pdf">
                            <div class="invalid-feedback d-block" id="error-store_layout_pdf" style="display:none !important;"></div>
                        </div>

                        <hr class="my-3">

                        {{-- Status --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="toggle-switch">
                                <label class="switch">
                                    <input type="checkbox" id="status-switch" checked>
                                    <span class="slider round"></span>
                                </label>
                                <span class="ms-2" id="status-label">Active</span>
                            </div>
                            <input type="hidden" id="status" name="status" value="1">
                            <div class="invalid-feedback" id="error-status"></div>
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

    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Store Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm">
                                <tr><th width="40%">Title</th><td id="view-title"></td></tr>
                                <tr><th>Code</th><td id="view-code"></td></tr>
                                <tr><th>Store Code</th><td id="view-store-code"></td></tr>
                                <tr><th>Area (sqft)</th><td id="view-area-sqft"></td></tr>
                                <tr><th>Monthly Rent</th><td id="view-rent"></td></tr>
                                <tr><th>Per Sqft Rent</th><td id="view-per-sqft-rent"></td></tr>
                                <tr><th>Opened Date</th><td id="view-opened"></td></tr>
                                <tr><th>Manager</th><td id="view-manager"></td></tr>
                                <tr><th>Status</th><td id="view-status"></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered table-sm">
                                <tr><th width="40%">Address</th><td id="view-address"></td></tr>
                                <tr><th>Area</th><td id="view-area"></td></tr>
                                <tr><th>Thana</th><td id="view-thana"></td></tr>
                                <tr><th>District</th><td id="view-district"></td></tr>
                                <tr><th>Division</th><td id="view-division"></td></tr>
                                <tr><th>Postal Code</th><td id="view-postal"></td></tr>
                                <tr><th>Coordinates</th><td id="view-coords"></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-sm">
                                <tr><th width="20%">Contact Person</th><td id="view-contact"></td></tr>
                                <tr><th>Mobile</th><td id="view-mobile"></td></tr>
                                <tr><th>Email</th><td id="view-email"></td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Layout History --}}
                    <div id="view-layouts-section" class="mt-3" style="display:none;">
                        <h6 class="fw-semibold text-muted mb-2"><i class="ri-layout-3-line me-1"></i> Layout History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Changed At</th>
                                        <th>PDF</th>
                                        <th>Active</th>
                                    </tr>
                                </thead>
                                <tbody id="view-layouts-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
                    <h6>Delete Store</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-store-name"></strong>?</p>
                    <input type="hidden" id="delete-store-id">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<style>
    .btn-list { display: flex; gap: 4px; }
    .toggle-switch { display: flex; align-items: center; }
    .toggle-switch .switch { position: relative; display: inline-block; width: 44px; height: 24px; margin-bottom: 0; }
    .toggle-switch .switch input { opacity: 0; width: 0; height: 0; }
    .toggle-switch .slider { position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .3s; }
    .toggle-switch .slider.round { border-radius: 24px; }
    .toggle-switch .slider.round:before { border-radius: 50%; }
    .toggle-switch .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #fff; transition: .3s; border-radius: 50%; }
    .toggle-switch .switch input:checked + .slider { background-color: #5b6edf; }
    .toggle-switch .switch input:checked + .slider:before { transform: translateX(20px); }
    .filepond--root { margin-bottom: 0; }
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    <script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script>
    $(document).ready(function () {
        const storeModal = new bootstrap.Modal(document.getElementById('storeModal'));
        const viewModalEl = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));

        // --- Auto-generate code from title ---
        $('#title').on('input', function () {
            if (!$('#store_id').val()) {
                const name = $(this).val().trim();
                if (name.length >= 2) {
                    let code = '';
                    const words = name.split(/\s+/);
                    if (words.length >= 3) {
                        code = words.map(w => w[0]).join('').substring(0, 3);
                    } else if (words.length === 2) {
                        code = words[0].substring(0, 2) + words[1][0];
                    } else {
                        const consonants = name.replace(/[aeiou]/gi, '');
                        if (consonants.length >= 2) {
                            code = name[0] + consonants[1] + (consonants[2] || name[name.length - 1]);
                        } else {
                            code = name.substring(0, 3);
                        }
                    }
                    $('#code').val(code.toUpperCase().substring(0, 3));
                }
            }
        });

        // --- Cascading Dropdowns ---
        $('#division_id').on('change', function () {
            const divisionId = $(this).val();
            $('#district_id').html('<option value="">— Select District —</option>').prop('disabled', true);
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);

            if (divisionId) {
                $.get(base_url + 'get-districts/' + divisionId, function (districts) {
                    let options = '<option value="">— Select District —</option>';
                    districts.forEach(function (d) {
                        options += `<option value="${d.id}">${d.name}</option>`;
                    });
                    $('#district_id').html(options).prop('disabled', false);
                });
            }
        });

        $('#district_id').on('change', function () {
            const districtId = $(this).val();
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);

            if (districtId) {
                $.get(base_url + 'get-thanas/' + districtId, function (thanas) {
                    let options = '<option value="">— Select Thana —</option>';
                    thanas.forEach(function (t) {
                        options += `<option value="${t.id}">${t.name}</option>`;
                    });
                    $('#thana_id').html(options).prop('disabled', false);
                });
            }
        });

        // --- FilePond for Layout PDF ---
        FilePond.registerPlugin(
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        const pdfPond = FilePond.create(document.querySelector('.filepond-pdf'), {
            labelIdle: '<i class="ri-file-pdf-2-line" style="font-size:1.2rem;"></i><br>Drag & Drop layout PDF or <span class="filepond--label-action">Browse</span>',
            acceptedFileTypes: ['application/pdf'],
            maxFileSize: '10MB',
            credits: false,
        });

        // --- Status Switch ---
        $('#status-switch').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('#status').val(isChecked ? '1' : '0');
            $('#status-label').text(isChecked ? 'Active' : 'Inactive');
        });

        // --- Open Add Modal ---
        $('#btn-add-store').on('click', function () {
            resetForm();
            $('#storeModalLabel').text('Add Store');
            $('#btn-save .btn-text').text('Save');
            storeModal.show();
        });

        // --- Open Edit Modal ---
        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#storeModalLabel').text('Edit Store');
            $('#btn-save .btn-text').text('Update');
            $.get(base_url + 'stores/' + id + '/edit', function (data) {
                $('#store_id').val(data.id);
                $('#title').val(data.title);
                $('#code').val(data.code);
                $('#store_code').val(data.store_code);
                $('#total_area_sqft').val(data.total_area_sqft);
                $('#monthly_rent').val(data.monthly_rent);
                $('#per_sqr_feet_rent').val(data.per_sqr_feet_rent);
                $('#opened_date').val(data.opened_date);
                $('#store_manager_id').val(data.store_manager_id || '');
                $('#address').val(data.address);
                $('#area').val(data.area);
                $('#postal_code').val(data.postal_code);
                $('#latitude').val(data.latitude);
                $('#longitude').val(data.longitude);
                $('#contact_persion').val(data.contact_persion);
                $('#shop_official_mobile').val(data.shop_official_mobile);
                $('#shop_official_email').val(data.shop_official_email);
                $('#status').val(data.status);
                $('#status-switch').prop('checked', data.status == 1).trigger('change');

                // Load cascading dropdowns
                if (data.division_id) {
                    $('#division_id').val(data.division_id);
                    $.get(base_url + 'get-districts/' + data.division_id, function (districts) {
                        let options = '<option value="">— Select District —</option>';
                        districts.forEach(function (d) {
                            options += `<option value="${d.id}" ${d.id == data.district_id ? 'selected' : ''}>${d.name}</option>`;
                        });
                        $('#district_id').html(options).prop('disabled', false);

                        if (data.district_id) {
                            $.get(base_url + 'get-thanas/' + data.district_id, function (thanas) {
                                let options = '<option value="">— Select Thana —</option>';
                                thanas.forEach(function (t) {
                                    options += `<option value="${t.id}" ${t.id == data.thana_id ? 'selected' : ''}>${t.name}</option>`;
                                });
                                $('#thana_id').html(options).prop('disabled', false);
                            });
                        }
                    });
                }

                storeModal.show();
            });
        });

        // --- View Store ---
        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'stores/' + id, function (data) {
                $('#view-title').text(data.title);
                $('#view-code').text(data.code);
                $('#view-store-code').text(data.store_code || '—');
                $('#view-area-sqft').text(data.total_area_sqft ? data.total_area_sqft + ' sqft' : '—');
                $('#view-rent').text(data.monthly_rent ? '৳' + parseFloat(data.monthly_rent).toLocaleString() : '—');
                $('#view-per-sqft-rent').text(data.per_sqr_feet_rent ? '৳' + parseFloat(data.per_sqr_feet_rent).toLocaleString() : '—');
                $('#view-opened').text(data.opened_date || '—');
                $('#view-manager').text(data.store_manager ? data.store_manager.name : '—');
                $('#view-status').html(data.status == 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-address').text(data.address || '—');
                $('#view-area').text(data.area || '—');
                $('#view-thana').text(data.thana ? data.thana.name : '—');
                $('#view-district').text(data.district ? data.district.name : '—');
                $('#view-division').text(data.division ? data.division.name : '—');
                $('#view-postal').text(data.postal_code || '—');
                $('#view-coords').text(data.latitude && data.longitude ? data.latitude + ', ' + data.longitude : '—');
                $('#view-contact').text(data.contact_persion || '—');
                $('#view-mobile').text(data.shop_official_mobile || '—');
                $('#view-email').text(data.shop_official_email || '—');

                // Layout history
                if (data.store_layouts && data.store_layouts.length) {
                    $('#view-layouts-section').show();
                    let html = '';
                    data.store_layouts.forEach(function (layout, i) {
                        html += '<tr>';
                        html += '<td>' + (i + 1) + '</td>';
                        html += '<td>' + (layout.changed_at || '—') + '</td>';
                        html += '<td>' + (layout.layout_pdf ? '<a href="' + base_url + layout.layout_pdf + '" target="_blank" class="btn btn-xs btn-info-light">Download</a>' : '—') + '</td>';
                        html += '<td>' + (layout.is_currently_active == 1 ? '<span class="badge bg-success-transparent">Active</span>' : '<span class="text-muted">—</span>') + '</td>';
                        html += '</tr>';
                    });
                    $('#view-layouts-body').html(html);
                } else {
                    $('#view-layouts-section').hide();
                }

                viewModalEl.show();
            });
        });

        // --- Delete ---
        $(document).on('click', '.btn-delete', function () {
            $('#delete-store-id').val($(this).data('id'));
            $('#delete-store-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-store-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'stores/' + id,
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    showToast('Failed to delete store.', 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        // --- Submit Form ---
        $('#storeForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#store_id').val();
            const url = id ? base_url + 'stores/' + id : base_url + 'stores';
            const formData = new FormData(this);

            // Append FilePond PDF file
            const pdfFile = pdfPond.getFile();
            if (pdfFile) {
                formData.append('store_layout_pdf', pdfFile.file);
            }

            if (id) formData.append('_method', 'PUT');

            $('#btn-save').prop('disabled', true);
            $('#btn-spinner').removeClass('d-none');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    storeModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            if (field === 'store_layout_pdf') {
                                $('#error-store_layout_pdf').text(messages[0]).css('display', 'block');
                            } else {
                                $('#' + field).addClass('is-invalid');
                                $('#error-' + field).text(messages[0]);
                            }
                        });
                    } else {
                        showToast('Something went wrong.', 'danger');
                    }
                },
                complete: function () {
                    $('#btn-save').prop('disabled', false);
                    $('#btn-spinner').addClass('d-none');
                }
            });
        });

        // --- Helpers ---
        function resetForm() {
            $('#storeForm')[0].reset();
            $('#store_id').val('');
            pdfPond.removeFiles();
            $('#status-switch').prop('checked', true).trigger('change');
            $('#district_id').html('<option value="">— Select District —</option>').prop('disabled', true);
            $('#thana_id').html('<option value="">— Select Thana —</option>').prop('disabled', true);
            clearErrors();
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').css('display', '');
        }

        function showToast(message, type) {
            const toast = $(`
                <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:9999" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `).appendTo('body');
            setTimeout(() => toast.remove(), 3000);
        }
    });
    </script>
@endpush
