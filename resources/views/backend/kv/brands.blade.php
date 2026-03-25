@extends('backend.master')

@section('title', 'Brands')

@section('body')
    <div class="container m-t-50">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Brands List</div>
                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-brand">
                            <i class="ri-add-line me-1"></i> Add Brand
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Logo</th>
                                        <th>Code</th>
                                        <th>Description</th>
{{--                                        <th>Common</th>--}}
{{--                                        <th>Status</th>--}}
                                        <th>Info</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($brands as $brand)
                                    <tr id="brand-row-{{ $brand->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>
                                            @if($brand->logo)
                                                <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" style="height: 40px; border-radius: 5px;">
                                            @else
                                                <span class="badge bg-light text-muted">No Logo</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-primary-transparent">{{ $brand->code }}</span></td>
                                        <td class="text-wrap">{{ \Str::limit($brand->description, 50) }}</td>
{{--                                        <td>--}}
{{--                                            @if($brand->is_common)--}}
{{--                                                <span class="badge bg-outline-success">Yes</span>--}}
{{--                                            @else--}}
{{--                                                <span class="badge bg-outline-secondary">No</span>--}}
{{--                                            @endif--}}
{{--                                        </td>--}}
                                        <td>
                                            @if($brand->status == 1)
                                                <span class="badge bg-outline-success">Published</span>
                                            @else
                                                <span class="badge bg-outline-danger">Unpublished</span>
                                            @endif

                                                @if($brand->is_common)
                                                    <span class="badge bg-outline-success">Common</span>
                                                @else
                                                    <span class="badge bg-outline-secondary">Not Common</span>
                                                @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $brand->id }}" title="View"><i class="ri-eye-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $brand->id }}" title="Edit"><i class="ri-edit-box-line"></i></button>
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete " data-id="{{ $brand->id }}" data-name="{{ $brand->name }}" title="Delete"><i class="ri-delete-bin-line"></i></button>
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
    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="brandModalLabel">Add Brand</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="createAppendCodehere">
                    <form id="brandForm" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                        <div class="modal-body">
                            <input type="hidden" id="brand_id" value="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter brand name">
                                <div class="invalid-feedback" id="error-name"></div>
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control text-uppercase" readonly id="code" name="code" placeholder="2-3 letter abbreviation" maxlength="3">
                                <small class="form-text text-muted">A 2-3 letter abbreviation that sounds close to the brand name.</small>
                                <div class="invalid-feedback" id="error-code"></div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
                                <div class="invalid-feedback" id="error-description"></div>
                            </div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="filepond logo" id="logo" name="logo" accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml, image/webp">
                                <div class="invalid-feedback d-block" id="error-logo" style="display:none !important;"></div>
                                <div id="logo-preview" class="mt-2 d-none">
                                    <img src="" alt="Logo Preview" style="height: 60px; border-radius: 5px;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label d-block">Is Common</label>
                                        <div class="toggle-switch">
                                            <label class="switch">
                                                <input type="checkbox" id="common-switch" >
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="ms-2" id="status-label">Published</span>
                                        </div>
                                        <input type="hidden" id="isCommon" name="is_common" value="0">
                                        <div class="invalid-feedback" id="error-status"></div>
                                    </div>
                                    <div class="col-md-4">
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
    </div>
    {{-- View Modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Brand Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3" id="view-logo-container">
                        <img id="view-logo" src="" alt="" style="max-height: 80px; border-radius: 8px;">
                    </div>
                    <table class="table table-bordered">
                        <tr><th width="30%">Name</th><td id="view-name"></td></tr>
                        <tr><th>Code</th><td id="view-code"></td></tr>
                        <tr><th>Description</th><td id="view-description"></td></tr>
                        <tr><th>Is Common</th><td id="view-is-common"></td></tr>
                        <tr><th>Status</th><td id="view-status"></td></tr>
{{--                        <tr><th>Created</th><td id="view-created"></td></tr>--}}
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
                    <h6>Delete Brand</h6>
                    <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-brand-name"></strong>?</p>
                    <input type="hidden" id="delete-brand-id">
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
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">
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
    @include('backend.includes.plugins.toastr')
    <script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script>
    $(document).ready(function () {
        const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));
        const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
        const deleteModalEl = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Auto-generate code from name
        $('#name').on('input', function () {
            if (!$('#brand_id').val()) { // Only auto-generate for new brands
                const name = $(this).val().trim();
                if (name.length >= 2) {
                    let code = '';
                    const words = name.split(/\s+/);
                    if (words.length >= 3) {
                        code = words.map(w => w[0]).join('').substring(0, 3);
                    } else if (words.length === 2) {
                        code = words[0].substring(0, 2) + words[1][0];
                    } else {
                        // Single word: take first consonant cluster + vowel pattern
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

        // Open Add Modal
        $('#btn-add-brand').on('click', function () {
            resetForm();
            $('#brandModalLabel').text('Add Brand');
            $('#btn-save .btn-text').text('Save');
            brandModal.show();
        });

        // Open Edit Modal
        $(document).on('click', '.btn-edit', function () {
            resetForm();
            const id = $(this).data('id');
            $('#brandModalLabel').text('Edit Brand');
            $('#btn-save .btn-text').text('Update');
            $.get(base_url + 'brands/' + id + '/edit', function (data) {
                $('#brand_id').val(data.id);
                $('#name').val(data.name);
                $('#code').val(data.code);
                $('#description').val(data.description);
                $('#status').val(data.status);
                $('#status-switch').prop('checked', data.status == 1).trigger('change');
                $('#isCommon').val(data.is_common);
                $('#common-switch').prop('checked', data.is_common == 1).trigger('change');
                if (data.logo) {
                    $('#logo-preview').removeClass('d-none').find('img').attr('src', base_url + data.logo);
                }
                brandModal.show();
            }).fail(function (xhr) {
                showToast(getErrorMessage(xhr, 'Failed to load brand data.'), 'danger');
            });
        });
        // View Brand
        $(document).on('click', '.btn-view', function () {
            const id = $(this).data('id');
            $.get(base_url + 'brands/' + id, function (data) {
                $('#view-name').text(data.name);
                $('#view-code').text(data.code);
                $('#view-description').text(data.description || '—');
                $('#view-is-common').html(data.is_common == 1
                    ? '<span class="badge bg-success-transparent">Yes</span>'
                    : '<span class="badge bg-secondary-transparent">No</span>');
                $('#view-status').html(data.status == 1
                    ? '<span class="badge bg-success-transparent">Published</span>'
                    : '<span class="badge bg-danger-transparent">Unpublished</span>');
                // $('#view-created').text(new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }));
                if (data.logo) {
                    $('#view-logo').attr('src', base_url + data.logo);
                    $('#view-logo-container').show();
                } else {
                    $('#view-logo-container').hide();
                }
                viewModal.show();
            }).fail(function (xhr) {
                showToast(getErrorMessage(xhr, 'Failed to load brand details.'), 'danger');
            });
        });

        // Open Delete Modal
        $(document).on('click', '.btn-delete', function () {
            $('#delete-brand-id').val($(this).data('id'));
            $('#delete-brand-name').text($(this).data('name'));
            deleteModalEl.show();
        });

        // Confirm Delete
        $('#btn-confirm-delete').on('click', function () {
            const id = $('#delete-brand-id').val();
            const btn = $(this);
            btn.prop('disabled', true).text('Deleting...');
            $.ajax({
                url: base_url + 'brands/' + id,
                type: 'DELETE',
                success: function (res) {
                    if (res.success === false) {
                        showToast(res.message || 'Failed to delete brand.', 'danger');
                        return;
                    }

                    deleteModalEl.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    showToast(getErrorMessage(xhr, 'Failed to delete brand.'), 'danger');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Yes, Delete');
                }
            });
        });

        // Submit Form (Create / Update)
        $('#brandForm').on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            const id = $('#brand_id').val();
            const url = id ? base_url + 'brands/' + id : base_url + 'brands';
            const formData = new FormData(this);
            // Add FilePond file to formData
            const pondFile = pond.getFile();
            if (pondFile) {
                formData.append('logo', pondFile.file);
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
                    if (res.success === false) {
                        showToast(res.message || 'Something went wrong.', 'danger');
                        return;
                    }

                    brandModal.hide();
                    showToast(res.message, 'success');
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function (field, messages) {
                            if (field === 'logo') {
                                $('#error-logo').text(messages[0]).css('display', 'block');
                            } else {
                                $('#' + field).addClass('is-invalid');
                                $('#error-' + field).text(messages[0]);
                            }
                        });
                    } else {
                        showToast(getErrorMessage(xhr, 'Something went wrong.'), 'danger');
                    }
                },
                complete: function () {
                    $('#btn-save').prop('disabled', false);
                    $('#btn-spinner').addClass('d-none');
                }
            });
        });

        // FilePond setup
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize,
            FilePondPluginImageExifOrientation
        );

        const pond = FilePond.create(document.querySelector('.logo'), {
            labelIdle: '<i class="ri-upload-cloud-2-line" style="font-size:1.5rem;"></i><br>Drag & Drop your logo or <span class="filepond--label-action">Browse</span>',
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'],
            maxFileSize: '2MB',
            imagePreviewHeight: 100,
            stylePanelLayout: 'compact',
            credits: false,
        });

        // Common switch toggle
        $('#common-switch').on('change', function () {
            $('#isCommon').val($(this).is(':checked') ? '1' : '0');
        });

        // Status switch toggle
        $('#status-switch').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('#status').val(isChecked ? '1' : '0');
            $('#status-label').text(isChecked ? 'Published' : 'Unpublished');
        });

        function resetForm() {
            $('#brandForm')[0].reset();
            $('#brand_id').val('');
            $('#logo-preview').addClass('d-none');
            pond.removeFiles();
            $('#common-switch').prop('checked', false).trigger('change');
            $('#status-switch').prop('checked', true).trigger('change');
            clearErrors();
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').css('display', '');
        }

        function getErrorMessage(xhr, fallbackMessage) {
            return xhr.responseJSON?.message || fallbackMessage;
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
