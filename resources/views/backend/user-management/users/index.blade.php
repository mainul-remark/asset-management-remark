@extends('backend.master')
@section('title','User')
@push('styles')
    @include('backend.user-management.datatables.datatable-style')
    <link rel="modulepreload" href="{{asset('backend/build/assets/date_time_pickers-CfSDcSmz.js')}}" />
    <link rel="stylesheet" href="{{asset('backend/reza-custom/css/custom.css')}}"/>
{{--    <style>--}}
{{--        /* Bottom info + pagination same line */--}}
{{--        .dt-container .dt-info,--}}
{{--        .dt-container .dt-paging {--}}
{{--            display: inline-flex;--}}
{{--            align-items: center;--}}
{{--        }--}}
{{--        /* Wrap both in one row */--}}
{{--        .dt-container {--}}
{{--            position: relative;--}}
{{--        }--}}

{{--        /* Info (Showing...) left */--}}
{{--        .dt-container .dt-info {--}}
{{--            float: left;--}}
{{--        }--}}

{{--        /* Pagination right */--}}
{{--        .dt-container .dt-paging {--}}
{{--            float: right;--}}
{{--        }--}}


{{--        .dt-buttons .btn-group .flex-wrap .gap-2 { float: left }--}}
{{--        .dt-search {float: right}--}}
{{--        .dt-length {margin-left: 10px!important; float: left}--}}

{{--        /* Mobile fix for dt-search and dt-length (100px to 488px) */--}}
{{--        @media (min-width: 100px) and (max-width: 488px) {--}}
{{--            .dt-length,--}}
{{--            .dt-search {--}}
{{--                float: left !important;--}}
{{--                width: 50% !important;--}}
{{--                margin: 0 !important;--}}
{{--                box-sizing: border-box !important;--}}
{{--            }--}}
{{--            .dt-length select,--}}
{{--            .dt-search input {--}}
{{--                width: auto !important;--}}
{{--            }--}}
{{--            /*.dt-length {margin-left: 5px!important;}*/--}}
{{--        }--}}
{{--    </style>--}}
@endpush

@section('body')
    <div class="container-fluid pt-3">
        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb mb-3">
            <div class="my-auto">
                <h4 class="mb-sm-0 text-uppercase" style="font-family: 'Bell MT';font-size: 16px"><i class="mdi mdi-checkbox-marked-outline me-2"></i>Users</h4>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-7 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form id="filter_form" class="form-inline justify-content-center">
                            <div class="row">
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="input-group">--}}
{{--                                        <span class="input-group-text text-muted"><i class="ri-calendar-line"></i></span>--}}
{{--                                        <input type="text" name="from_date"  max="{{date('Y-m-d H:i:s')}}"  class="form-control py-2" id="from_date" placeholder="From date">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-4">--}}
{{--                                    <div class="input-group">--}}
{{--                                        <span class="input-group-text text-muted"><i class="ri-calendar-line"></i></span>--}}
{{--                                        <input type="text" name="to_date"  max="{{date('Y-m-d H:i:s')}}"  class="form-control py-2" id="to_date" placeholder="To date">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-md-4">
                                    <div class="input-group ">
                                        <span class="input-group-text text-muted"><i class="ri-group-line"></i></span>
                                        <select name="role_id" id="selectRole" class="form-control ">
                                            <option value="">All Roles</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->role_id }}" >{{ $role->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="btn-group" role="group" aria-label="Filter actions">
                                        <button type="submit" class="btn btn-outline-primary " id="filterBtn" title="Filter">
                                            <i class="ri-search-line"></i> Filter
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary  ajax_reload" id="resetBtn" title="Refresh">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-view-list me-1"></i> User List
                        </h5>
                        <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-plus-circle me-1"></i> Create
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table  class="table table-bordered text-nowrap w-100 mb-3" id="userDataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="storeAssignModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold" id="storeAssignModalTitle">Manage Store Assignment</h5>
                        <p class="text-muted fs-12 mb-0">Assign one or more stores to the selected user.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userStoreAssignmentModalForm">
                    <div class="modal-body pt-3">
                        <input type="hidden" id="modal_assignment_id">
                        <input type="hidden" id="modal_assignment_user_id">

                        <div class="assignment-user-summary mb-3">
                            <div class="assignment-user-summary__meta">
                                <span class="summary-label">Selected User</span>
                                <div class="summary-name" id="assignment_modal_user_name">-</div>
                                <div class="summary-email" id="assignment_modal_user_email">-</div>
                                <div class="summary-email" id="assignment_modal_user_employee_id">Employee ID: -</div>
                            </div>
                            <div class="assignment-user-summary__state">
                                <span class="badge bg-light text-dark border" id="assignment-modal-mode-badge">New assignment</span>
                            </div>
                        </div>

                        <div class="assignment-current-state d-none mb-3" id="assignment-current-state">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <div class="summary-label">Current Assignment</div>
                                    <div class="summary-name fs-14 d-none" id="assignment-current-role">No role</div>
                                    <div class="summary-email" id="assignment-current-meta">No assignment yet</div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger-light d-none" id="btn-delete-user-assignment">
                                    <i class="ri-delete-bin-line me-1"></i>Delete Assignment
                                </button>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3" id="assignment-current-stores"></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 d-none">
                                <label for="modal_assignment_role_id" class="form-label">Role</label>
                                <select class="form-select select-ele" id="modal_assignment_role_id">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block" id="modal-error-role_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_assignment_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="modal_assignment_status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback d-block" id="modal-error-status"></div>
                            </div>
                            <div class="col-12">
                                <label for="modal_assignment_store_ids" class="form-label">Stores <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="modal_assignment_store_ids" multiple>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }}{{ $store->code ? ' (' . $store->code . ')' : '' }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Selecting stores here will create or replace this user’s complete assignment group.</div>
                                <div class="invalid-feedback d-block" id="modal-error-store_ids"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-user-assignment">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Save Assignment</span>
                            <span class="spinner-border spinner-border-sm d-none" id="modal-save-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .assignment-user-summary,
        .assignment-current-state {
            border: 1px solid var(--default-border, #e9ebec);
            border-radius: 12px;
            padding: 14px 16px;
            background: #fff;
        }

        .assignment-user-summary {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        .summary-label {
            display: inline-block;
            margin-bottom: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text-muted, #6c757d);
        }

        .summary-name {
            font-weight: 600;
            color: var(--default-text-color);
        }

        .summary-email {
            color: var(--text-muted, #6c757d);
            font-size: 0.8rem;
            margin-top: 4px;
        }
    </style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    <script src="{{asset('backend/build/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    @include('backend.user-management.datatables.datatable-script')
    @include('backend.user-management.toasts')
    @include('backend.user-management.partials.user.user-index-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function () {
            const storeAssignModalEl = document.getElementById('storeAssignModal');
            const storeAssignModal = new bootstrap.Modal(storeAssignModalEl);
            const assignmentBaseUrl = "{{ url('user-store-assignments') }}";
            const currentAssignmentUrlTemplate = @json(route('user-store-assignments.current-by-user', ['user' => '__USER__']));

            const assignmentState = {
                userId: '',
                userName: '',
                userEmail: '',
                userEmployeeId: '',
                assignmentId: '',
                hasExistingAssignment: false,
            };

            function currentAssignmentUrl(userId) {
                return currentAssignmentUrlTemplate.replace('__USER__', String(userId));
            }

            function clearAssignmentErrors() {
                ['role_id', 'store_ids', 'status'].forEach(function (field) {
                    $('#modal-error-' + field).text('');
                });

                $('#modal_assignment_role_id, #modal_assignment_store_ids, #modal_assignment_status').removeClass('is-invalid');
            }

            function setAssignmentSavingState(isLoading) {
                $('#btn-save-user-assignment').prop('disabled', isLoading);
                $('#modal-save-spinner').toggleClass('d-none', !isLoading);
            }

            function resetAssignmentModal() {
                assignmentState.userId = '';
                assignmentState.userName = '';
                assignmentState.userEmail = '';
                assignmentState.userEmployeeId = '';
                assignmentState.assignmentId = '';
                assignmentState.hasExistingAssignment = false;

                clearAssignmentErrors();
                $('#userStoreAssignmentModalForm')[0].reset();
                $('#modal_assignment_id').val('');
                $('#modal_assignment_user_id').val('');
                $('#modal_assignment_role_id').val('').trigger('change.select2');
                $('#modal_assignment_store_ids').val([]).trigger('change.select2');
                $('#modal_assignment_status').val('1');
                $('#assignment_modal_user_name').text('-');
                $('#assignment_modal_user_email').text('-');
                $('#assignment_modal_user_employee_id').text('Employee ID: -');
                $('#assignment-current-role').text('No role');
                $('#assignment-current-meta').text('No assignment yet');
                $('#assignment-current-stores').empty();
                $('#assignment-current-state').addClass('d-none');
                $('#btn-delete-user-assignment').addClass('d-none').prop('disabled', false);
                $('#assignment-modal-mode-badge').text('New assignment');
                $('#storeAssignModalTitle').text('Manage Store Assignment');
                $('#btn-save-user-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Save Assignment');
            }

            function applyAssignmentErrors(errors) {
                if (errors.role_id) {
                    $('#modal_assignment_role_id').addClass('is-invalid');
                    $('#modal-error-role_id').text(errors.role_id[0]);
                }

                if (errors.store_ids || errors['store_ids.0']) {
                    $('#modal_assignment_store_ids').addClass('is-invalid');
                    $('#modal-error-store_ids').text((errors.store_ids && errors.store_ids[0]) || errors['store_ids.0'][0]);
                }

                if (errors.status) {
                    $('#modal_assignment_status').addClass('is-invalid');
                    $('#modal-error-status').text(errors.status[0]);
                }
            }

            function renderCurrentAssignment(data) {
                const stores = Array.isArray(data?.stores) ? data.stores : [];
                const assignedBy = data?.assigned_by?.name || 'System';
                const assignedAt = data?.assigned_at || 'N/A';
                const roleName = data?.role?.name || 'No role';
                const storeCount = Number(data?.store_count || 0);

                assignmentState.assignmentId = String(data?.id || '');
                assignmentState.hasExistingAssignment = !!assignmentState.assignmentId;

                $('#modal_assignment_id').val(assignmentState.assignmentId);
                $('#modal_assignment_role_id').val(data?.role_id ? String(data.role_id) : '').trigger('change.select2');
                $('#modal_assignment_store_ids').val((data?.store_ids || []).map(String)).trigger('change.select2');
                $('#modal_assignment_status').val(String(data?.status ?? 1));
                $('#assignment-current-role').text(roleName);
                $('#assignment-current-meta').text(`${storeCount} store(s) • ${assignedAt} • ${assignedBy}`);
                $('#assignment-current-stores').html(
                    stores.map(function (store) {
                        const title = $('<div>').text(store.title || '').html();
                        const code = store.code ? ` <small class="text-muted ms-1">${$('<div>').text(store.code).html()}</small>` : '';
                        return `<span class="badge bg-light text-dark border">${title}${code}</span>`;
                    }).join('')
                );
                $('#assignment-current-state').removeClass('d-none');
                $('#btn-delete-user-assignment').removeClass('d-none');
                $('#assignment-modal-mode-badge').text('Existing assignment');
                $('#storeAssignModalTitle').text(`Manage Store Assignment: ${assignmentState.userName}`);
                $('#btn-save-user-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Update Assignment');
            }

            function renderEmptyAssignmentState() {
                assignmentState.assignmentId = '';
                assignmentState.hasExistingAssignment = false;

                $('#modal_assignment_id').val('');
                $('#modal_assignment_role_id').val('').trigger('change.select2');
                $('#modal_assignment_store_ids').val([]).trigger('change.select2');
                $('#modal_assignment_status').val('1');
                $('#assignment-current-role').text('No role');
                $('#assignment-current-meta').text('No assignment yet');
                $('#assignment-current-stores').empty();
                $('#assignment-current-state').removeClass('d-none');
                $('#btn-delete-user-assignment').addClass('d-none');
                $('#assignment-modal-mode-badge').text('New assignment');
                $('#storeAssignModalTitle').text(`Manage Store Assignment: ${assignmentState.userName}`);
                $('#btn-save-user-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Save Assignment');
            }

            function loadCurrentAssignment(userId) {
                return $.get(currentAssignmentUrl(userId))
                    .done(function (response) {
                        if (response?.exists && response.data) {
                            renderCurrentAssignment(response.data);
                            return;
                        }

                        renderEmptyAssignmentState();
                    })
                    .fail(function () {
                        showAjaxToast('error', 'Failed to load store assignment details.');
                    });
            }

            $(document).on('click', '.open-store-assign-modal', function (event) {
                event.preventDefault();

                assignmentState.usagesState = $(this).data('usages-sector') || 'field';
                if (assignmentState.usagesState == 'corporate'){
                    showAjaxToast('danger', 'No need to assign stores to corporate users.');
                    return;
                }

                resetAssignmentModal();

                assignmentState.userId = String($(this).data('user-id') || '');
                assignmentState.userName = $(this).data('user-name') || 'Selected User';
                assignmentState.userEmail = $(this).data('user-email') || '';
                assignmentState.userEmployeeId = $(this).data('user-employee-id') || '';

                $('#modal_assignment_user_id').val(assignmentState.userId);
                $('#assignment_modal_user_name').text(assignmentState.userName);
                $('#assignment_modal_user_email').text(assignmentState.userEmail || 'No email available');
                $('#assignment_modal_user_employee_id').text(`Employee ID: ${assignmentState.userEmployeeId || 'N/A'}`);
                $('#storeAssignModalTitle').text(`Manage Store Assignment: ${assignmentState.userName}`);

                storeAssignModal.show();
                loadCurrentAssignment(assignmentState.userId);
            });

            $('#userStoreAssignmentModalForm').on('submit', function (event) {
                event.preventDefault();
                clearAssignmentErrors();

                const assignmentId = $('#modal_assignment_id').val();
                const payload = {
                    user_id: $('#modal_assignment_user_id').val(),
                    role_id: $('#modal_assignment_role_id').val(),
                    store_ids: $('#modal_assignment_store_ids').val(),
                    status: $('#modal_assignment_status').val()
                };

                setAssignmentSavingState(true);

                $.ajax({
                    url: assignmentId ? `${assignmentBaseUrl}/${assignmentId}` : assignmentBaseUrl,
                    type: assignmentId ? 'PUT' : 'POST',
                    data: payload,
                    success: function (response) {
                        showAjaxToast('success', response.message || 'Store assignment saved successfully.');

                        if (response?.data) {
                            renderCurrentAssignment(response.data);
                            return;
                        }

                        loadCurrentAssignment(payload.user_id);
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            applyAssignmentErrors(xhr.responseJSON?.errors || {});
                            return;
                        }

                        showAjaxToast('error', xhr.responseJSON?.message || 'Failed to save store assignment.');
                    },
                    complete: function () {
                        setAssignmentSavingState(false);
                    }
                });
            });

            $('#btn-delete-user-assignment').on('click', function () {
                const assignmentId = $('#modal_assignment_id').val();

                if (!assignmentId) {
                    return;
                }

                // if (!confirm(`Remove all store assignments for ${assignmentState.userName}?`)) {
                //     return;
                // }
                $(this).prop('disabled', true);
                Swal.fire({
                    title: `Are you sure?`,
                    text: `Remove all store assignments for ${assignmentState.userName}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Remove !"
                }).then((result) => {
                    if (result.isConfirmed)
                    {
                        $.ajax({
                            url: `${assignmentBaseUrl}/${assignmentId}`,
                            type: 'DELETE',
                            success: function (response) {
                                showAjaxToast('success', response.message || 'Store assignment deleted successfully.');
                                renderEmptyAssignmentState();
                            },
                            error: function (xhr) {
                                showAjaxToast('error', xhr.responseJSON?.message || 'Failed to delete store assignment.');
                            },
                            complete: function () {
                                $('#btn-delete-user-assignment').prop('disabled', false);
                            }
                        });
                    }
                });
                $(this).prop('disabled', false);
            });

            storeAssignModalEl.addEventListener('hidden.bs.modal', function () {
                resetAssignmentModal();
            });
        });
    </script>
@endpush
