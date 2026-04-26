@extends('backend.master')

@section('title', 'User Store Assignments')

@section('body')
    <div class="container-fluid pt-3">
        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb mb-3">
            <div class="my-auto">
                <h4 class="mb-sm-0 text-uppercase assignment-page-title">
                    <i class="ri-store-3-line me-2"></i>User Store Assignments
                </h4>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center gap-2">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Store Assignments</li>
                    </ol>
                </nav>
            </div>
        </div>

{{--        <div class="row g-3 mb-3">--}}
{{--            <div class="col-xl-3 col-md-6">--}}
{{--                <div class="card custom-card assignment-summary-card h-100">--}}
{{--                    <div class="card-body">--}}
{{--                        <span class="summary-icon bg-primary-transparent text-primary">--}}
{{--                            <i class="ri-user-settings-line"></i>--}}
{{--                        </span>--}}
{{--                        <div class="mt-3">--}}
{{--                            <p class="text-muted mb-1">Assigned Users</p>--}}
{{--                            <h4 class="mb-0">{{ $assignmentGroups->total() }}</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-xl-3 col-md-6">--}}
{{--                <div class="card custom-card assignment-summary-card h-100">--}}
{{--                    <div class="card-body">--}}
{{--                        <span class="summary-icon bg-success-transparent text-success">--}}
{{--                            <i class="ri-store-2-line"></i>--}}
{{--                        </span>--}}
{{--                        <div class="mt-3">--}}
{{--                            <p class="text-muted mb-1">Available Stores</p>--}}
{{--                            <h4 class="mb-0">{{ $stores->count() }}</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-xl-3 col-md-6">--}}
{{--                <div class="card custom-card assignment-summary-card h-100">--}}
{{--                    <div class="card-body">--}}
{{--                        <span class="summary-icon bg-info-transparent text-info">--}}
{{--                            <i class="ri-shield-user-line"></i>--}}
{{--                        </span>--}}
{{--                        <div class="mt-3">--}}
{{--                            <p class="text-muted mb-1">Role Options</p>--}}
{{--                            <h4 class="mb-0">{{ $roles->count() }}</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-xl-3 col-md-6">--}}
{{--                <div class="card custom-card assignment-summary-card h-100">--}}
{{--                    <div class="card-body">--}}
{{--                        <span class="summary-icon bg-warning-transparent text-warning">--}}
{{--                            <i class="ri-links-line"></i>--}}
{{--                        </span>--}}
{{--                        <div class="mt-3">--}}
{{--                            <p class="text-muted mb-1">Store Links</p>--}}
{{--                            <h4 class="mb-0">{{ $assignmentGroups->getCollection()->sum('store_count') }}</h4>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="card custom-card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('user-store-assignments.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-5">
                            <label for="search" class="form-label">Search</label>
                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ $filters['search'] }}"
                                placeholder="Search by user, email, store, or role"
                            >
                        </div>
                        <div class="col-lg-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="1" @selected((string) $filters['status'] === '1')>Active</option>
                                <option value="0" @selected((string) $filters['status'] === '0')>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-wave">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                                <a href="{{ route('user-store-assignments.index') }}" class="btn btn-light btn-wave">
                                    <i class="ri-refresh-line me-1"></i>Reset
                                </a>
                                <button type="button" class="btn btn-success btn-wave ms-auto" id="btn-open-create-modal">
                                    <i class="ri-add-line me-1"></i>Assign User
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Assignment Directory</h5>
                    <p class="text-muted fs-12 mb-0">Each row represents one user with one or more assigned stores.</p>
                </div>
                <span class="badge bg-primary-transparent">{{ $assignmentGroups->total() }} user(s)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th width="60">SL</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Assigned Stores</th>
                            <th>Status</th>
                            <th>Assigned Info</th>
                            <th width="140">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($assignmentGroups as $index => $group)
                            <tr>
                                <td>{{ $assignmentGroups->firstItem() + $index }}</td>
                                <td>
                                    <div class="assignment-cell">
                                        <div class="primary-line">{{ $group['user']['name'] }}</div>
                                        <div class="secondary-line">{{ $group['user']['email'] }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($group['role'])
                                        <span class="badge bg-info-transparent text-info">{{ $group['role']['name'] }}</span>
                                    @else
                                        <span class="text-muted">No role</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($group['stores'] as $store)
                                            <span class="badge bg-light text-dark border store-chip">
                                                {{ $store['title'] }}
                                                @if($store['code'])
                                                    <small class="text-muted ms-1">{{ $store['code'] }}</small>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="secondary-line mt-2">{{ $group['store_count'] }} store(s)</div>
                                </td>
                                <td>
                                    <span class="badge {{ $group['status'] === 1 ? 'bg-success-transparent text-success' : 'bg-danger-transparent text-danger' }}">
                                        {{ $group['status_label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="assignment-cell">
                                        <div class="primary-line">{{ $group['assigned_at'] ?? 'N/A' }}</div>
                                        <div class="secondary-line">
                                            {{ $group['assigned_by']['name'] ?? 'System' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary-light btn-wave btn-edit-assignment"
                                            data-id="{{ $group['id'] }}"
                                            title="Edit"
                                        >
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger-light btn-wave btn-delete-assignment"
                                            data-id="{{ $group['id'] }}"
                                            data-user-name="{{ $group['user']['name'] }}"
                                            data-store-count="{{ $group['store_count'] }}"
                                            title="Delete"
                                        >
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                        No user store assignments found.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assignmentGroups->hasPages())
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <div class="text-muted fs-13">
                            Showing {{ $assignmentGroups->firstItem() }} to {{ $assignmentGroups->lastItem() }} of {{ $assignmentGroups->total() }} entries
                        </div>
                        <div>
                            {{ $assignmentGroups->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold" id="assignmentModalTitle">Assign User to Stores</h5>
                        <p class="text-muted fs-12 mb-0">One user can be linked with multiple stores from a single action.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignmentForm">
                    <div class="modal-body pt-3">
                        <input type="hidden" id="assignment_id">
                        <input type="hidden" id="assignment_user_id_hidden">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="assignment_user_id" class="form-label">User <span class="text-danger">*</span></label>
                                <select
                                    class="form-select select-ele"
                                    id="assignment_user_id"
                                    data-search-url="{{ url('user-store-assignments/users/search') }}"
                                >
                                    <option value="">Select User</option>
                                    @foreach($users->take(10) as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                            @if($user->employee_id)
                                                ({{ $user->employee_id }})
                                            @endif
                                            @if($user->email)
                                                - {{ $user->email }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block" id="error-user_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="assignment_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="assignment_status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-feedback d-block" id="error-status"></div>
                            </div>
                            <div class="col-md-6 d-none">
                                <label for="assignment_role_id" class="form-label">Role</label>
                                <select class="form-select select-ele" id="assignment_role_id" >
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block" id="error-role_id"></div>
                            </div>
                            <div class="col-12">
                                <label for="assignment_store_ids" class="form-label">Stores <span class="text-danger">*</span></label>
                                <select class="form-select select-ele" id="assignment_store_ids" multiple>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->title }}{{ $store->code ? ' (' . $store->code . ')' : '' }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Choose one or more active stores for the selected user.</div>
                                <div class="invalid-feedback d-block" id="error-store_ids"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="assignment-note-box h-100">
                                    <span class="note-label">Update behavior</span>
                                    <p class="mb-0 fs-12 text-muted">Editing replaces the selected user’s full store list to keep the group consistent.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-assignment">
                            <span class="btn-text"><i class="ri-save-line me-1"></i>Save Assignment</span>
                            <span class="spinner-border spinner-border-sm d-none" id="save-spinner"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAssignmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <span class="avatar avatar-lg bg-danger-transparent rounded-circle">
                            <i class="ri-delete-bin-line text-danger fs-24"></i>
                        </span>
                    </div>
                    <h6 class="fw-semibold mb-1">Delete Assignment Group?</h6>
                    <p class="text-muted fs-13 mb-0" id="deleteAssignmentMessage"></p>
                    <input type="hidden" id="delete_assignment_id">
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete-assignment">
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
.assignment-page-title {
    font-family: 'Bell MT';
    font-size: 16px;
}
.assignment-summary-card {
    border: 1px solid var(--default-border, #e9ebec);
}
.summary-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}
.assignment-cell .primary-line {
    font-weight: 600;
    color: var(--default-text-color);
}
.assignment-cell .secondary-line {
    color: var(--text-muted, #6c757d);
    font-size: 0.75rem;
    margin-top: 3px;
}
.store-chip {
    font-size: 0.73rem;
    font-weight: 500;
}
.assignment-note-box {
    background: rgba(var(--primary-rgb), 0.06);
    border: 1px dashed rgba(var(--primary-rgb), 0.28);
    border-radius: 10px;
    padding: 14px;
}
.assignment-note-box .note-label {
    display: inline-block;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 6px;
    color: rgba(var(--primary-rgb), 1);
}
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.select2')
    @include('backend.includes.plugins.toastr')

    <script>
        $(function () {
            const assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteAssignmentModal'));
            const assignmentBaseUrl = "{{ url('user-store-assignments') }}";
            const userSearchUrl = $('#assignment_user_id').data('search-url');

            function formatUserText(user) {
                if (user.text && !user.name) {
                    return user.text;
                }

                const employeeId = user.employee_id ? ` (${user.employee_id})` : '';
                const email = user.email ? ` - ${user.email}` : '';

                return `${user.name || ''}${employeeId}${email}`;
            }

            $('#assignment_user_id').select2({
                dropdownParent: $('#assignmentModal'),
                width: '100%',
                placeholder: 'Select User',
                allowClear: true,
                ajax: {
                    url: userSearchUrl,
                    dataType: 'json',
                    delay: 250,
                    cache: true,
                    data: function (params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: (response.data || []).map(function (user) {
                                return {
                                    id: user.id,
                                    text: formatUserText(user),
                                    name: user.name,
                                    email: user.email,
                                    employee_id: user.employee_id
                                };
                            })
                        };
                    }
                },
                minimumInputLength: 0,
                templateResult: function (user) {
                    return formatUserText(user);
                },
                templateSelection: function (user) {
                    return formatUserText(user);
                }
            });

            function clearErrors() {
                ['user_id', 'role_id', 'store_ids', 'status'].forEach(function (field) {
                    $('#error-' + field).text('');
                });

                $('#assignment_user_id, #assignment_role_id, #assignment_store_ids, #assignment_status').removeClass('is-invalid');
            }

            function resetForm() {
                clearErrors();
                $('#assignmentForm')[0].reset();
                $('#assignment_id').val('');
                $('#assignment_user_id_hidden').val('');
                $('#assignment_user_id').prop('disabled', false).val('').trigger('change.select2');
                $('#assignment_role_id').val('').trigger('change.select2');
                $('#assignment_store_ids').val([]).trigger('change.select2');
                $('#assignment_status').val('1');
                $('#assignmentModalTitle').text('Assign User to Stores');
                $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Save Assignment');
            }

            function setSavingState(isLoading) {
                $('#btn-save-assignment').prop('disabled', isLoading);
                $('#save-spinner').toggleClass('d-none', !isLoading);
            }

            function setDeleteState(isLoading) {
                $('#btn-confirm-delete-assignment').prop('disabled', isLoading);
                $('#btn-confirm-delete-assignment .spinner-border').toggleClass('d-none', !isLoading);
            }

            function applyErrors(errors) {
                if (errors.user_id) {
                    $('#assignment_user_id').addClass('is-invalid');
                    $('#error-user_id').text(errors.user_id[0]);
                }

                if (errors.role_id) {
                    $('#assignment_role_id').addClass('is-invalid');
                    $('#error-role_id').text(errors.role_id[0]);
                }

                if (errors.store_ids || errors['store_ids.0']) {
                    $('#assignment_store_ids').addClass('is-invalid');
                    $('#error-store_ids').text((errors.store_ids && errors.store_ids[0]) || errors['store_ids.0'][0]);
                }

                if (errors.status) {
                    $('#assignment_status').addClass('is-invalid');
                    $('#error-status').text(errors.status[0]);
                }
            }

            function fillForm(data) {
                clearErrors();
                $('#assignment_id').val(data.id);
                $('#assignment_user_id_hidden').val(data.user_id);
                $('#assignment_user_id').val(String(data.user_id)).trigger('change.select2').prop('disabled', true);
                $('#assignment_role_id').val(data.role_id ? String(data.role_id) : '').trigger('change.select2');
                $('#assignment_store_ids').val((data.store_ids || []).map(String)).trigger('change.select2');
                $('#assignment_status').val(String(data.status ?? 1));
                $('#assignmentModalTitle').text('Edit User Store Assignment');
                $('#btn-save-assignment .btn-text').html('<i class="ri-save-line me-1"></i>Update Assignment');
            }

            $('#btn-open-create-modal').on('click', function () {
                resetForm();
                assignmentModal.show();
            });

            $(document).on('click', '.btn-edit-assignment', function () {
                const id = $(this).data('id');
                resetForm();

                $.get(`${assignmentBaseUrl}/${id}/edit`)
                    .done(function (response) {
                        fillForm(response);
                        assignmentModal.show();
                    })
                    .fail(function () {
                        toastr.error('Failed to load assignment details.');
                    });
            });

            $('#assignmentForm').on('submit', function (event) {
                event.preventDefault();
                clearErrors();

                const id = $('#assignment_id').val();
                const payload = {
                    user_id: $('#assignment_user_id_hidden').val() || $('#assignment_user_id').val(),
                    role_id: $('#assignment_role_id').val(),
                    store_ids: $('#assignment_store_ids').val(),
                    status: $('#assignment_status').val()
                };

                setSavingState(true);

                $.ajax({
                    url: id ? `${assignmentBaseUrl}/${id}` : assignmentBaseUrl,
                    type: id ? 'PUT' : 'POST',
                    data: payload,
                    success: function (response) {
                        toastr.success(response.message || 'Assignment saved successfully.');
                        assignmentModal.hide();
                        window.location.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            applyErrors(xhr.responseJSON.errors || {});
                            return;
                        }

                        toastr.error(xhr.responseJSON?.message || 'Failed to save assignment.');
                    },
                    complete: function () {
                        setSavingState(false);
                    }
                });
            });

            $(document).on('click', '.btn-delete-assignment', function () {
                const id = $(this).data('id');
                const userName = $(this).data('user-name');
                const storeCount = $(this).data('store-count');

                $('#delete_assignment_id').val(id);
                $('#deleteAssignmentMessage').html(`Remove <strong>${storeCount}</strong> store assignment(s) for <strong>${userName}</strong>?`);
                deleteModal.show();
            });

            $('#btn-confirm-delete-assignment').on('click', function () {
                const id = $('#delete_assignment_id').val();

                if (!id) {
                    return;
                }

                setDeleteState(true);

                $.ajax({
                    url: `${assignmentBaseUrl}/${id}`,
                    type: 'DELETE',
                    success: function (response) {
                        toastr.success(response.message || 'Assignment deleted successfully.');
                        deleteModal.hide();
                        window.location.reload();
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to delete assignment.');
                    },
                    complete: function () {
                        setDeleteState(false);
                    }
                });
            });
        });
    </script>
@endpush
