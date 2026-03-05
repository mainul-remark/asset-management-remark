@extends('backend.master')

@section('title', 'Categories')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-title mb-1">Categories Management</div>
                            {{-- Breadcrumb Navigation --}}
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                                    <li class="breadcrumb-item">
                                        @if($parent)
                                            <a href="{{ route('categories.index') }}"><i class="ri-home-4-line"></i> All Categories</a>
                                        @else
                                            <i class="ri-home-4-line"></i> All Categories
                                        @endif
                                    </li>
                                    @if($breadcrumbs->count())
                                        @foreach($breadcrumbs as $crumb)
                                            <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                                @if($loop->last)
                                                    {{ $crumb->name }}
                                                @else
                                                    <a href="{{ route('categories.index', ['category' => $crumb->id]) }}">{{ $crumb->name }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ol>
                            </nav>
                        </div>
                        <div class="d-flex gap-2">
                            @if($parent)
                                <a href="{{ $breadcrumbs->count() > 1 ? route('categories.index', ['category' => $breadcrumbs[$breadcrumbs->count() - 2]->id]) : route('categories.index') }}" class="btn btn-sm btn-light btn-wave">
                                    <i class="ri-arrow-left-line me-1"></i> Back
                                </a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-category">
                                <i class="ri-add-line me-1"></i> Add {{ $parent ? 'Subcategory' : 'Category' }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Code</th>
{{--                                        <th>Subcategories</th>--}}
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if($category->children_count > 0)
                                                <a href="{{ route('categories.index', ['category' => $category->id]) }}" class="fw-semibold text-primary">
                                                    <i class="ri-folder-open-line me-1"></i>{{ $category->name }}
                                                </a>
                                            @else
                                                <i class="ri-file-list-3-line me-1 text-muted"></i>{{ $category->name }}
                                            @endif
                                        </td>
                                        <td><span class="badge bg-primary-transparent">{{ $category->code }}</span></td>
{{--                                        <td>--}}
{{--                                            @if($category->children_count > 0)--}}
{{--                                                <a href="{{ route('categories.index', ['category' => $category->id]) }}" class="badge bg-info-transparent">--}}
{{--                                                    {{ $category->children_count }} {{ Str::plural('subcategory', $category->children_count) }}--}}
{{--                                                </a>--}}
{{--                                            @else--}}
{{--                                                <span class="text-muted">—</span>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
                                        <td class="text-wrap">{{ \Str::limit($category->description, 50) }}</td>
                                        <td>
                                            @if($category->status == 1)
                                                <span class="badge bg-outline-success">Published</span>
                                            @else
                                                <span class="badge bg-outline-danger">Unpublished</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $category->id }}" title="View"><i class="ri-eye-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $category->id }}" title="Edit"><i class="ri-edit-box-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-has-children="{{ $category->children_count > 0 ? 1 : 0 }}" title="Delete"><i class="ri-delete-bin-line"></i></button>
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
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="categoryModalLabel">Add Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="categoryForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                    <div class="modal-body">
                        <input type="hidden" id="category_id" value="">
{{--                        <div class="mb-3">--}}
{{--                            <label for="category_id_parent" class="form-label">Parent Category</label>--}}
{{--                            <select class="form-select" id="category_id_parent" name="category_id">--}}
{{--                                <option value="">— None (Root Category) —</option>--}}
{{--                                @foreach($allCategories as $cat)--}}
{{--                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                            <div class="invalid-feedback" id="error-category_id"></div>--}}
{{--                        </div>--}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter category name">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" readonly id="code" name="code" placeholder="2-3 letter abbreviation" maxlength="3">
                            <small class="form-text text-muted">A 2-3 letter abbreviation that sounds close to the category name.</small>
                            <div class="invalid-feedback" id="error-code"></div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
                            <div class="invalid-feedback" id="error-description"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="toggle-switch">
                                <label class="switch">
                                    <input type="checkbox" id="status-switch" checked>
                                    <span class="slider round"></span>
                                </label>
                                <span class="ms-2" id="status-label">Published</span>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Category Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">Name</th><td id="view-name"></td></tr>
                        <tr><th>Code</th><td id="view-code"></td></tr>
{{--                        <tr><th>Parent</th><td id="view-parent"></td></tr>--}}
                        <tr><th>Description</th><td id="view-description"></td></tr>
                        <tr><th>Status</th><td id="view-status"></td></tr>
                        <tr><th>Created</th><td id="view-created"></td></tr>
                    </table>
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
                    <h6>Delete Category</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-category-name"></strong>?</p>
                    <p class="text-danger small mt-1 mb-0 d-none" id="delete-warning">This will also delete all subcategories.</p>
                    <input type="hidden" id="delete-category-id">
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
</style>
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    <script>
    $(document).ready(function () {
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));
        const currentParentId = '{{ $parent?->id ?? '' }}';

        // Auto-generate code from name
        $('#name').on('input', function () {
            if (!$('#category_id').val()) {
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

        // Status switch toggle
        $('#status-switch').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('#status').val(isChecked ? '1' : '0');
            $('#status-label').text(isChecked ? 'Published' : 'Unpublished');
        });

        // Open Add Modal — pre-select current parent
        $('#btn-add-category').on('click', function () {
            resetForm();
            $('#categoryModalLabel').text('Add {{ $parent ? "Subcategory" : "Category" }}');
            $('#btn-save .btn-text').text('Save');
            // if (currentParentId) {
            //     $('#category_id_parent').val(currentParentId);
            // }
            categoryModal.show();
        });

        // Open Edit Modal
        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#categoryModalLabel').text('Edit Category');
            $('#btn-save .btn-text').text('Update');
            $.get(base_url + 'categories/' + id + '/edit', function (data) {
                $('#category_id').val(data.id);
                // $('#category_id_parent').val(data.category_id || '');
                // Hide self from parent dropdown
                // $('#category_id_parent option').show();
                // $('#category_id_parent option[value="' + data.id + '"]').hide();
                $('#name').val(data.name);
                $('#code').val(data.code);
                $('#description').val(data.description);
                $('#status').val(data.status);
                $('#status-switch').prop('checked', data.status == 1).trigger('change');
                categoryModal.show();
            });
        });

        // View Category
        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'categories/' + id, function (data) {
                $('#view-name').text(data.name);
                $('#view-code').text(data.code);
                // $('#view-parent').text(data.parent ? data.parent.name : '— Root —');
                $('#view-description').text(data.description || '—');
                $('#view-status').html(data.status == 1
                    ? '<span class="badge bg-success-transparent">Published</span>'
                    : '<span class="badge bg-danger-transparent">Unpublished</span>');
                $('#view-created').text(new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
                viewModal.show();
            });
        });

        // Open Delete Modal
        $(document).on('click', '.btn-delete', function () {
            $('#delete-category-id').val($(this).data('id'));
            $('#delete-category-name').text($(this).data('name'));
            if ($(this).data('has-children')) {
                $('#delete-warning').removeClass('d-none');
            } else {
                $('#delete-warning').addClass('d-none');
            }
            deleteModalEl.show();
        });

        // Confirm Delete
        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-category-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'categories/' + id,
                type: 'DELETE',
                success: function (res) {
                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function () {
                    showToast('Failed to delete category.', 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        // Submit Form (Create / Update)
        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#category_id').val();
            const url = id ? base_url + 'categories/' + id : base_url + 'categories';
            const formData = new FormData(this);
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
                    categoryModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            const fieldId = field === 'category_id' ? 'category_id_parent' : field;
                            $('#' + fieldId).addClass('is-invalid');
                            $('#error-' + field).text(messages[0]);
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

        function resetForm() {
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            // $('#category_id_parent').val('');
            // $('#category_id_parent option').show();
            $('#status-switch').prop('checked', true).trigger('change');
            clearErrors();
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
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
