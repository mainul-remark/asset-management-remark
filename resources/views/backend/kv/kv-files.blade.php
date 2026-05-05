@extends('backend.master')

@section('title', 'Key Visual Files')

@section('body')
    @include('backend.includes.temp.prototype-callouts')
@php
    $hasDependencies = isset($keyVisuals, $keyVisualSizes) && $keyVisuals->isNotEmpty() && $keyVisualSizes->isNotEmpty();
    $selectedKeyVisualId = $selectedKeyVisualId ?? null;
@endphp
<div class="container m-t-50">
    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-title mb-1">Key Visual Files</div>
                        <p class="text-muted fs-12 mb-0">Manage key visual files.</p>
                    </div>
                    @if($permissions['canCreate'])
                    <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-file" @disabled(!$hasDependencies) title="{{ $hasDependencies ? 'Add key visual file' : 'Create key visual and size first' }}">
                        <i class="ri-add-line me-1"></i> Add File
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @unless($hasDependencies)
                        <div class="alert alert-warning">
                            You need at least one key visual and one key visual size before creating a key visual file.
                        </div>
                    @endunless
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered text-nowrap w-100 align-middle">
                            <thead>
                                <tr>
                                    <th width="45">#</th>
                                    <th>Name</th>
                                    <th>Key Visual</th>
                                    <th>Size</th>
                                    <th>File</th>
                                    <th>KV Size</th>
                                    <th>Aspect Ratio</th>
                                    <th>Type</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="110">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kvFiles as $kvFile)
                                    <tr>
                                        @php
                                            $fileType = strtolower((string) ($kvFile->file_type ?? ''));
                                            $fileExtension = strtolower(pathinfo((string) $kvFile->kv_file, PATHINFO_EXTENSION));
                                            $isImagePreview = str_starts_with($fileType, 'image/')
                                                || in_array($fileExtension, ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'], true);
                                            $isVideoPreview = str_starts_with($fileType, 'video/')
                                                || in_array($fileExtension, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true);
                                        @endphp
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $kvFile->name }}</td>
                                        <td>
                                            @if($kvFile->keyVisual)
                                                {{ $kvFile->keyVisual->name }}
                                                @if($kvFile->keyVisual->unique_code)
                                                    <span class="badge bg-light text-dark ms-1">{{ $kvFile->keyVisual->unique_code }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($kvFile->keyVisualSize)
                                                {{ $kvFile->keyVisualSize->name }}
                                                <span class="text-muted d-block fs-11">
                                                    {{ rtrim(rtrim((string) $kvFile->keyVisualSize->width, '0'), '.') }}
                                                    x
                                                    {{ rtrim(rtrim((string) $kvFile->keyVisualSize->height, '0'), '.') }}
                                                    {{ strtoupper($kvFile->keyVisualSize->unit_name) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($kvFile->kv_file)
                                                <div class="kv-file-preview">
                                                    @if($isImagePreview)
                                                        <a href="{{ asset($kvFile->kv_file) }}" target="_blank" rel="noopener" class="kv-file-preview__link" aria-label="Preview {{ $kvFile->name }}">
                                                            <img
                                                                src="{{ asset($kvFile->kv_file) }}"
                                                                alt="{{ $kvFile->name }}"
                                                                class="kv-file-preview__media"
                                                                loading="lazy"
                                                            >
                                                        </a>
                                                    @elseif($isVideoPreview)
                                                        <video
                                                            class="kv-file-preview__media"
                                                            controls
                                                            muted
                                                            playsinline
                                                            preload="metadata"
                                                        >
                                                            <source src="{{ asset($kvFile->kv_file) }}" @if($kvFile->file_type) type="{{ $kvFile->file_type }}" @endif>
                                                        </video>
                                                    @else
                                                        <span class="text-muted fs-11">Preview unavailable</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format((int) $kvFile->kv_size) }} KB</td>
                                        <td>{{ $kvFile->aspect_ratio !== null ? number_format((float) $kvFile->aspect_ratio, 4) : 'N/A' }}</td>
                                        <td>{{ $kvFile->file_type ?: 'N/A' }}</td>
                                        <td>{{ $kvFile->file_duration ?: 'N/A' }}</td>
                                        <td>
                                            @if((int) $kvFile->status === 1)
                                                <span class="badge bg-outline-success">Active</span>
                                            @else
                                                <span class="badge bg-outline-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($kvFile->created_at)->format('d M Y') }}</td>
                                        <td>
                                            <div class="btn-list">
                                                @if($permissions['canView'])
                                                <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{{ $kvFile->id }}" title="View">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @endif
                                                @if($permissions['canEdit'])
                                                <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{{ $kvFile->id }}" title="Edit">
                                                    <i class="ri-edit-box-line"></i>
                                                </button>
                                                @endif
                                                @if($permissions['canDelete'])
                                                <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{{ $kvFile->id }}" data-name="{{ $kvFile->name }}" title="Delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                @endif
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
@include('backend.kv.Modals.keyVisualFiles')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<style>
    .btn-list { display: flex; gap: 4px; }
    .kv-file-preview {
        width: 112px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 0.75rem;
        border: 1px solid var(--default-border);
        background: rgb(var(--light-rgb));
    }
    .kv-file-preview__link {
        display: block;
        width: 100%;
        height: 100%;
    }
    .kv-file-preview__media {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        background: #000;
    }
</style>
@endpush

@push('scripts')
@include('backend.includes.plugins.datatable')
@include('backend.includes.plugins.select2')
@if($permissions['canCreate'] || $permissions['canEdit'])
<script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
<script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
@endif
<script>
const kvFilesPermissions = @json($permissions);
$(function () {
    const fileModal   = (kvFilesPermissions.canCreate || kvFilesPermissions.canEdit)
        ? new bootstrap.Modal(document.getElementById('fileModal'))   : null;
    const viewModal   = kvFilesPermissions.canView
        ? new bootstrap.Modal(document.getElementById('viewModal'))   : null;
    const deleteModal = kvFilesPermissions.canDelete
        ? new bootstrap.Modal(document.getElementById('deleteModal')) : null;
    const preselectedKeyVisualId = @json($selectedKeyVisualId);

    const BASE = base_url;
    const apiUrl = (id = '') => base_url + 'key-visual-files' + (id ? '/' + id : '');

    @if($permissions['canCreate'] || $permissions['canEdit'])
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize
    );

    const MAX_IMAGE_FILE_SIZE = 5 * 1024 * 1024;
    const MAX_VIDEO_FILE_SIZE = 10 * 1024 * 1024;

    const IMAGE_MIMES = ['image/jpeg','image/png','image/jpg','image/gif','image/svg+xml','image/webp'];
    const VIDEO_MIMES = ['video/mp4','video/quicktime','video/x-msvideo','video/x-matroska','video/webm'];

    function configureFilePondByKvType() {
        const kvType = $('#key_visual_id option:selected').data('kv-type') || '';
        const isVideo = kvType === 'video';
        const isImage = kvType === 'image';
        if (isVideo) {
            kvFilePond.setOptions({ acceptedFileTypes: VIDEO_MIMES });
            $('#kv-upload-hint').html('Videos only &bull; MP4 / MOV / AVI / MKV / WEBM &bull; max 10 MB');
        } else if (isImage) {
            kvFilePond.setOptions({ acceptedFileTypes: IMAGE_MIMES });
            $('#kv-upload-hint').html('Images only &bull; JPG / PNG / GIF / SVG / WEBP &bull; max 5 MB');
        } else {
            kvFilePond.setOptions({ acceptedFileTypes: [...IMAGE_MIMES, ...VIDEO_MIMES] });
            $('#kv-upload-hint').html('Images: max 5 MB &bull; Videos: max 10 MB');
        }
    }

    function getUploadSizeLimit(file) {
        const fileType = String(file?.type || '');

        if (fileType.startsWith('image/')) {
            return {
                maxBytes: MAX_IMAGE_FILE_SIZE,
                message: 'Image files must not exceed 5 MB.',
            };
        }

        if (fileType.startsWith('video/')) {
            return {
                maxBytes: MAX_VIDEO_FILE_SIZE,
                message: 'Video files must not exceed 10 MB.',
            };
        }

        return {
            maxBytes: MAX_VIDEO_FILE_SIZE,
            message: 'Upload file must be an image or video.',
        };
    }

    const kvFilePond = FilePond.create(document.querySelector('.filepond-kv-file'), {
        allowMultiple: false,
        instantUpload: false,
        allowProcess: false,
        allowRevert: false,
        maxFiles: 1,
        credits: false,
        acceptedFileTypes: [
            'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp',
            'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm'
        ],
        maxFileSize: '10MB',
        labelMaxFileSizeExceeded: 'File is too large',
        labelMaxFileSize: 'Maximum video size is 10 MB and maximum image size is 5 MB',
        labelIdle: '<i class="ri-upload-cloud-2-line" style="font-size:1.45rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop image/video or <span class="filepond--label-action">browse</span></span>',
        beforeAddFile: function (item) {
            const file = item?.file ?? item;

            if (!file) {
                return false;
            }

            const type = String(file.type || '');
            const isImage = type.startsWith('image/');
            const isVideo = type.startsWith('video/');
            const kvType = $('#key_visual_id option:selected').data('kv-type') || '';
            $('#error-kv_file_upload').text('');

            if (kvType === 'image' && !isImage) {
                $('#error-kv_file_upload').text('This key visual only accepts image files.');
                return false;
            }
            if (kvType === 'video' && !isVideo) {
                $('#error-kv_file_upload').text('This key visual only accepts video files.');
                return false;
            }

            const limit = getUploadSizeLimit(file);
            if ((file.size || 0) > limit.maxBytes) {
                $('#error-kv_file_upload').text(limit.message);
                return false;
            }

            return true;
        },
    });
    @endif

    const metaFields = {
        kv_size: {
            $input: $('#kv_size'),
            $wrapper: $('[data-meta-field="kv_size"]'),
            hasValue(value) {
                return value !== null && value !== undefined && value !== '' && !Number.isNaN(Number(value));
            },
        },
        aspect_ratio: {
            $input: $('#aspect_ratio'),
            $wrapper: $('[data-meta-field="aspect_ratio"]'),
            hasValue(value) {
                return Number(value) > 0;
            },
        },
        file_type: {
            $input: $('#file_type'),
            $wrapper: $('[data-meta-field="file_type"]'),
            hasValue(value) {
                return String(value ?? '').trim() !== '';
            },
        },
        file_duration: {
            $input: $('#file_duration'),
            $wrapper: $('[data-meta-field="file_duration"]'),
            hasValue(value) {
                return String(value ?? '').trim() !== '';
            },
        },
    };

    let fallbackMeta = emptyMeta();
    let metaReadToken = 0;
    let fallbackSizeId = '';

    function showToast(message, type = 'success') {
        const $toast = $(`
            <div class="toast align-items-center text-bg-${type} border-0 show position-fixed top-0 end-0 m-3" style="z-index:99999" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).appendTo('body');
        setTimeout(() => $toast.remove(), 3500);
    }

    function emptyMeta() {
        return {
            kv_size: '',
            aspect_ratio: '',
            file_type: '',
            file_duration: '',
        };
    }

    function setMediaDimensions(width = '', height = '') {
        $('#media_width').val(width);
        $('#media_height').val(height);
    }

    function syncDetectedKeyVisualSize(width, height) {
        const detectedWidth = Number(width);
        const detectedHeight = Number(height);

        if (!(detectedWidth > 0 && detectedHeight > 0)) {
            return;
        }

        const matchingOption = $('#key_visual_size_id option').filter(function () {
            return Number($(this).data('width')) === detectedWidth
                && Number($(this).data('height')) === detectedHeight
                && String($(this).data('unit') || '').toLowerCase() === 'px';
        }).first();

        $('#key_visual_size_id').val(matchingOption.length ? matchingOption.val() : '');
    }

    function normalizeMeta(meta = {}) {
        return {
            kv_size: meta.kv_size ?? '',
            aspect_ratio: meta.aspect_ratio ?? '',
            file_type: meta.file_type ?? '',
            file_duration: meta.file_duration ?? '',
        };
    }

    function refreshMetaVisibility() {
        Object.values(metaFields).forEach(function (field) {
            field.$wrapper.toggleClass('d-none', !field.hasValue(field.$input.val()));
        });
    }

    function setMetaValues(meta = {}) {
        const values = normalizeMeta(meta);

        Object.entries(metaFields).forEach(function ([key, field]) {
            field.$input.val(values[key]);
        });

        refreshMetaVisibility();
    }

    function formatDuration(seconds) {
        if (!Number.isFinite(seconds) || seconds < 0) return '';
        const total = Math.floor(seconds);
        const h = Math.floor(total / 3600);
        const m = Math.floor((total % 3600) / 60);
        const s = total % 60;

        if (h > 0) {
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
        }

        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function clearErrors() {
        $('#fileForm .is-invalid').removeClass('is-invalid');
        $('#fileForm .invalid-feedback').text('');
        $('#kv_file_upload').closest('.filepond--root').removeClass('is-invalid');
    }

    function resetForm() {
        $('#fileForm')[0].reset();
        $('#kv_file_id').val('');
        $('#key_visual_id').val(preselectedKeyVisualId || '');
        $('#key_visual_size_id').val('');
        fallbackSizeId = '';
        setMediaDimensions();
        $('#status').val('1');
        metaReadToken += 1;
        fallbackMeta = emptyMeta();
        setMetaValues(fallbackMeta);
        $('#existing-file-link').attr('href', '#');
        $('#existing-file-wrap').addClass('d-none');
        kvFilePond.removeFiles();
        clearErrors();
        configureFilePondByKvType();
    }

    function applyFileMeta(file) {
        if (!file) return;

        const currentMetaToken = ++metaReadToken;

        setMetaValues({
            kv_size: Math.round(((file.size || 0) / 1024)),
            aspect_ratio: '',
            file_type: file.type || '',
            file_duration: '',
        });
        setMediaDimensions();

        const objectUrl = URL.createObjectURL(file);

        if ((file.type || '').startsWith('video/')) {
            const video = document.createElement('video');
            video.preload = 'metadata';
            video.src = objectUrl;
            video.onloadedmetadata = function () {
                if (currentMetaToken !== metaReadToken) {
                    URL.revokeObjectURL(objectUrl);
                    return;
                }

                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    setMediaDimensions(video.videoWidth, video.videoHeight);
                    syncDetectedKeyVisualSize(video.videoWidth, video.videoHeight);
                    $('#aspect_ratio').val((video.videoWidth / video.videoHeight).toFixed(4));
                } else {
                    setMediaDimensions();
                    $('#aspect_ratio').val('');
                }
                $('#file_duration').val(formatDuration(video.duration));
                refreshMetaVisibility();
                URL.revokeObjectURL(objectUrl);
            };
            video.onerror = function () {
                if (currentMetaToken !== metaReadToken) {
                    URL.revokeObjectURL(objectUrl);
                    return;
                }

                refreshMetaVisibility();
                URL.revokeObjectURL(objectUrl);
            };
        } else if ((file.type || '').startsWith('image/')) {
            const image = new Image();
            image.onload = function () {
                if (currentMetaToken !== metaReadToken) {
                    URL.revokeObjectURL(objectUrl);
                    return;
                }

                if (image.width > 0 && image.height > 0) {
                    setMediaDimensions(image.width, image.height);
                    syncDetectedKeyVisualSize(image.width, image.height);
                    $('#aspect_ratio').val((image.width / image.height).toFixed(4));
                } else {
                    setMediaDimensions();
                    $('#aspect_ratio').val('');
                }
                refreshMetaVisibility();
                URL.revokeObjectURL(objectUrl);
            };
            image.onerror = function () {
                if (currentMetaToken !== metaReadToken) {
                    URL.revokeObjectURL(objectUrl);
                    return;
                }

                refreshMetaVisibility();
                URL.revokeObjectURL(objectUrl);
            };
            image.src = objectUrl;
        } else {
            refreshMetaVisibility();
            URL.revokeObjectURL(objectUrl);
        }
    }

    kvFilePond.on('addfile', function (error, fileItem) {
        if (error || !fileItem?.file) return;
        $('#error-kv_file_upload').text('');
        $('#existing-file-wrap').addClass('d-none');
        applyFileMeta(fileItem.file);
    });

    kvFilePond.on('removefile', function () {
        metaReadToken += 1;
        $('#error-kv_file_upload').text('');
        $('#key_visual_size_id').val(fallbackSizeId);
        setMediaDimensions();
        setMetaValues(fallbackMeta);

        if ($('#kv_file_id').val() && $('#existing-file-link').attr('href') !== '#') {
            $('#existing-file-wrap').removeClass('d-none');
        }
    });

    function formatDate(dateString) {
        return dateString
            ? new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
            : 'N/A';
    }

    function formatRatio(value) {
        if (value === null || value === undefined || value === '') return 'N/A';
        const number = Number(value);
        return Number.isNaN(number) ? value : number.toFixed(4);
    }

    $('#key_visual_id').on('change', function () {
        kvFilePond.removeFiles();
        $('#error-kv_file_upload').text('');
        configureFilePondByKvType();
    });

    $('#btn-add-file').on('click', function () {
        resetForm();
        $('#fileModalLabel').text('Add Key Visual File');
        $('#btn-save .btn-text').text('Save');
        fileModal.show();
    });

    $(document).on('click', '.btn-edit', function () {
        resetForm();
        const id = $(this).data('id');

        $.get(apiUrl(id) + '/edit')
            .done(function (data) {
                $('#kv_file_id').val(data.id);
                $('#name').val(data.name || '');
                $('#key_visual_id').val(data.key_visual_id || '');
                configureFilePondByKvType();
                $('#key_visual_size_id').val(data.key_visual_size_id || '');
                fallbackSizeId = data.key_visual_size_id || '';
                setMediaDimensions();
                fallbackMeta = normalizeMeta({
                    kv_size: data.kv_size ?? '',
                    aspect_ratio: data.aspect_ratio ?? '',
                    file_type: data.file_type || '',
                    file_duration: data.file_duration || '',
                });
                setMetaValues(fallbackMeta);
                $('#status').val(Number(data.status) === 1 ? '1' : '0');

                if (data.kv_file) {
                    $('#existing-file-link').attr('href', BASE + data.kv_file);
                    $('#existing-file-wrap').removeClass('d-none');
                } else {
                    $('#existing-file-link').attr('href', '#');
                    $('#existing-file-wrap').addClass('d-none');
                }

                $('#fileModalLabel').text('Edit Key Visual File');
                $('#btn-save .btn-text').text('Update');

                fileModal.show();
            })
            .fail(function () {
                showToast('Failed to load key visual file data.', 'danger');
            });
    });

    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');

        $.get(apiUrl(id))
            .done(function (data) {
                $('#view-name').text(data.name || 'N/A');
                $('#view-key-visual').text(
                    data.key_visual
                        ? (data.key_visual.name + (data.key_visual.unique_code ? ` (${data.key_visual.unique_code})` : ''))
                        : 'N/A'
                );
                $('#view-key-visual-size').text(
                    data.key_visual_size
                        ? `${data.key_visual_size.name} (${data.key_visual_size.width ?? 0} x ${data.key_visual_size.height ?? 0} ${(data.key_visual_size.unit_name || 'px').toUpperCase()})`
                        : 'N/A'
                );
                $('#view-kv-file').html(
                    data.kv_file
                        ? `<a href="${BASE + data.kv_file}" target="_blank" rel="noopener"><i class="ri-external-link-line me-1"></i>Open file</a>`
                        : 'N/A'
                );
                $('#view-kv-size').text(`${Number(data.kv_size || 0).toLocaleString()} KB`);
                $('#view-aspect-ratio').text(formatRatio(data.aspect_ratio));
                $('#view-file-type').text(data.file_type || 'N/A');
                $('#view-file-duration').text(data.file_duration || 'N/A');
                $('#view-status').html(Number(data.status) === 1
                    ? '<span class="badge bg-success-transparent">Active</span>'
                    : '<span class="badge bg-danger-transparent">Inactive</span>');
                $('#view-created').text(formatDate(data.created_at));
                $('#view-updated').text(formatDate(data.updated_at));
                viewModal.show();
            })
            .fail(function () {
                showToast('Failed to load details.', 'danger');
            });
    });

    $(document).on('click', '.btn-delete', function () {
        $('#delete-file-id').val($(this).data('id'));
        $('#delete-file-name').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btn-confirm-delete').on('click', function () {
        const id = $('#delete-file-id').val();
        const $btn = $(this);

        $btn.prop('disabled', true);
        $btn.find('.btn-text').text('Deleting...');
        $btn.find('.spinner-border').removeClass('d-none');

        $.ajax({
            url: apiUrl(id),
            type: 'DELETE',
            success: function (res) {
                deleteModal.hide();
                showToast(res.message || 'Deleted successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to delete file.', 'danger');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $btn.find('.btn-text').text('Yes, Delete');
                $btn.find('.spinner-border').addClass('d-none');
            }
        });
    });

    $('#fileForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const id = $('#kv_file_id').val();
        const formData = new FormData(this);
        formData.delete('kv_file_upload');

        const uploadFile = kvFilePond.getFile();
        if (uploadFile?.file) {
            formData.append('kv_file_upload', uploadFile.file);
        }

        if (id) {
            formData.append('_method', 'PUT');
        }

        $('#btn-save').prop('disabled', true);
        $('#btn-spinner').removeClass('d-none');

        $.ajax({
            url: apiUrl(id || ''),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                fileModal.hide();
                showToast(res.message || 'Saved successfully.', 'success');
                setTimeout(() => location.reload(), 700);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        const msg = messages[0];
                        if (field === 'kv_file_upload') {
                            $('#kv_file_upload').closest('.filepond--root').addClass('is-invalid');
                            $('#error-kv_file_upload').text(msg);
                            return;
                        }

                        const $field = $('#' + field);
                        $field.addClass('is-invalid');
                        $('#error-' + field).text(msg);
                    });
                    return;
                }

                showToast(xhr.responseJSON?.message || 'Something went wrong. Please try again.', 'danger');
            },
            complete: function () {
                $('#btn-save').prop('disabled', false);
                $('#btn-spinner').addClass('d-none');
            }
        });
    });
});
</script>
@endpush
