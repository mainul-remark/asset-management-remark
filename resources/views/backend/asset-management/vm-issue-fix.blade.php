@extends('backend.master')

@section('title', 'Vm Issue Fix')

@section('body')
    <section class="py-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <div class="">
                                <h3 class="float-start">VM Issues</h3>
{{--                                <a href="" class="float-end btn btn-secondary" data-bs-toggle="modal" data-bs-target="#vmFixUpdate">Create</a>--}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
{{--                                <table class="table" id="data-table">--}}
{{--                                    <thead>--}}
{{--                                        <tr>--}}
{{--                                            <th>#</th>--}}
{{--                                            <th>Store + Asset</th>--}}
{{--                                            <th>Vm Issue</th>--}}
{{--                                            <th>Issue Photo</th>--}}
{{--                                            <th>Assigned Person</th>--}}
{{--                                            <th>Assigned By</th>--}}
{{--                                            <th>Photos</th>--}}
{{--                                            <th>Status</th>--}}
{{--                                            <th>Action</th>--}}
{{--                                        </tr>--}}
{{--                                    </thead>--}}
{{--                                    <tbody>--}}
{{--                                        @foreach($vmIssues as $vmIssue)--}}
{{--                                            <tr>--}}
{{--                                                <td>{{ $loop->iteration }}</td>--}}
{{--                                                <td>--}}
{{--                                                    <span>{{ $vmIssue?->store?->title ?? '' }} ({{ $vmIssue?->asset?->name ?? '' }})</span>--}}
{{--                                                </td>--}}
{{--                                                <td>{!! $vmIssue?->issue_text ?? '' !!}</td>--}}
{{--                                                <td>--}}
{{--                                                    @foreach($vmIssue->visualMerchandisingFiles as $issueFile)--}}
{{--                                                        <img src="{{ asset($issueFile->file_path) }}" alt="" style="height: 60px; padding: 3px" />--}}
{{--                                                    @endforeach--}}
{{--                                                </td>--}}
{{--                                                <td>{{ $vmIssue?->assignedTo?->name ?? '' }}</td>--}}
{{--                                                <td>{{ $vmIssue?->assignedBy?->name ?? '' }}</td>--}}
{{--                                                <td>--}}
{{--                                                    @if($vmIssue->fix_proof)--}}
{{--                                                        @foreach(json_decode($vmIssue->fix_proof) as $fixFile)--}}
{{--                                                            <img src="{{ asset($fixFile) }}" alt="" style="height: 60px; padding: 3px" />--}}
{{--                                                        @endforeach--}}
{{--                                                    @endif--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <select name="issue_fix_status" class="form-control select-ele change-status" data-vm-id="{{ $vmIssue->id }}">--}}
{{--                                                        <option value="assigned" {{ $vmIssue->issue_fix_status == 'assigned' ? 'selected' : '' }}>Assigned</option>--}}
{{--                                                        <option value="planned" {{ $vmIssue->issue_fix_status == 'planned' ? 'selected' : '' }}>Planned</option>--}}
{{--                                                        <option value="processing" {{ $vmIssue->issue_fix_status == 'processing' ? 'selected' : '' }}>Processing</option>--}}
{{--                                                        <option value="solved" {{ $vmIssue->issue_fix_status == 'solved' ? 'selected' : '' }}>Solved</option>--}}
{{--                                                    </select>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <a href="" class="btn btn-sm btn-secondary view-vm" data-vm-id="{{ $vmIssue->id }}"><i class="ri-eye-line"></i></a>--}}
{{--                                                    <a href="" class="btn btn-sm btn-secondary assign-user" data-vm-id="{{ $vmIssue->id }}"><i class="ri-user-line"></i></a>--}}
{{--                                                    <a href="" class="btn btn-sm btn-secondary upload-proof" data-vm-id="{{ $vmIssue->id }}"><i class="ri-file-2-line"></i></a>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                        @endforeach--}}
{{--                                    </tbody>--}}
{{--                                </table>--}}

                                <table class="table" id="vm-issue-fix-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Store + Asset</th>
                                            <th>Issue</th>
                                            <th>Issue Photos</th>
                                            <th>Assigned To</th>
                                            <th>Assigned By</th>
                                            <th>Fix Proof</th>
                                            <th>Status</th>
                                            <th>Action</th>
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
    </section>
@endsection

@section('modal')
    <div class="modal fade" id="assignVmFixPerson">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="assignVmFixPersonForm">
                        @csrf
                        <div class="mt-2 row">
                            <label for="assignTo" class="col-md-4">Assign To</label>
                            <div class="col-md-8">
                                <select name="assigned_to" id="" class="form-control select-ele">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="btnVmIssueFixUser">Assign</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="vmFixPhotos">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Fix Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="vmFixPhotosForm">
                        @csrf
                        <div class="mt-2">
                            <label class="form-label">Upload Fix Proof Images</label>
                            <input type="file" id="vmFixProofInput" name="fix_proof[]" multiple accept="image/*" class="filepond-vm-fix-proof">
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="btnVmFixProofSubmit">Upload</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="viewVmIssueFix">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0" id="viewVmTitle">VM Issue Detail</h5>
                        <small class="text-muted" id="viewVmMeta"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewVmBody">
                    {{-- skeleton shown while loading --}}
                    <div id="viewVmSkeleton" class="text-center py-5">
                        <div class="spinner-border text-secondary" role="status"></div>
                    </div>
                    {{-- content populated by JS --}}
                    <div id="viewVmContent" class="d-none">

                        {{-- Status badge --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge fs-12 px-3 py-2" id="viewVmStatusBadge"></span>
                        </div>

                        {{-- Meta row --}}
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Store</div>
                                    <div class="fw-semibold" id="viewVmStore">—</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Asset</div>
                                    <div class="fw-semibold" id="viewVmAsset">—</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Assigned To</div>
                                    <div id="viewVmAssignedTo">—</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded border bg-light">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Assigned By</div>
                                    <div id="viewVmAssignedBy">—</div>
                                </div>
                            </div>
                        </div>

                        {{-- Issue description --}}
                        <div class="mb-3">
                            <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Issue Description</div>
                            <div class="p-3 rounded border" id="viewVmIssueText" style="min-height:48px"></div>
                        </div>

                        {{-- Fix note --}}
                        <div class="mb-3 d-none" id="viewVmFixNoteWrap">
                            <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Fix Note</div>
                            <div class="p-3 rounded border" id="viewVmFixNote"></div>
                        </div>

                        {{-- Issue photos --}}
                        <div class="mb-3" id="viewVmIssuePhotosWrap">
                            <div class="text-muted fs-11 text-uppercase fw-semibold mb-2">Issue Photos</div>
                            <div class="d-flex flex-wrap gap-2" id="viewVmIssuePhotos"></div>
                        </div>

                        {{-- Fix proof --}}
                        <div class="mb-1 d-none" id="viewVmFixProofWrap">
                            <div class="text-muted fs-11 text-uppercase fw-semibold mb-2">Fix Proof Photos</div>
                            <div class="d-flex flex-wrap gap-2" id="viewVmFixProof"></div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <small class="text-muted me-auto" id="viewVmDates"></small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')

    <script>
        const statusConfig = {
            assigned:   { label: 'Assigned',   cls: 'bg-warning-transparent text-warning' },
            planned:    { label: 'Planned',     cls: 'bg-info-transparent text-info' },
            processing: { label: 'Processing',  cls: 'bg-primary-transparent text-primary' },
            solved:     { label: 'Solved',      cls: 'bg-success-transparent text-success' },
        };

        function renderPhotoStrip(containerId, urls) {
            var $el = $('#' + containerId).empty();
            if (!urls || !urls.length) {
                $el.append('<span class="text-muted fs-12">No photos</span>');
                return;
            }
            urls.forEach(function (url) {
                $el.append(
                    $('<a>').attr({ href: url, target: '_blank', rel: 'noopener' }).append(
                        $('<img>').attr('src', url).css({ height: '80px', width: 'auto', borderRadius: '6px', objectFit: 'cover', border: '1px solid var(--default-border)' })
                    )
                );
            });
        }

        $(document).on('click', '.view-vm', function () {
            event.preventDefault();
            var vmId = $(this).data('vm-id');

            $('#viewVmSkeleton').removeClass('d-none');
            $('#viewVmContent').addClass('d-none');
            $('#viewVmTitle').text('VM Issue Detail');
            $('#viewVmMeta').text('');
            $('#viewVmDates').text('');
            $('#viewVmIssueFix').modal('show');

            $.get(base_url + 'vm/fix-issues/' + vmId)
                .done(function (data) {
                    var status  = statusConfig[data.issue_fix_status] || { label: data.issue_fix_status, cls: 'bg-secondary-transparent' };

                    $('#viewVmTitle').text('VM Issue #' + data.id);
                    $('#viewVmMeta').text((data.store || '') + (data.asset ? ' · ' + data.asset : ''));
                    $('#viewVmStatusBadge').text(status.label).attr('class', 'badge fs-12 px-3 py-2 ' + status.cls);
                    $('#viewVmStore').text(data.store || '—');
                    $('#viewVmAsset').text(data.asset || '—');
                    $('#viewVmAssignedTo').text(data.assigned_to || '—');
                    $('#viewVmAssignedBy').text(data.assigned_by || '—');
                    $('#viewVmIssueText').html(data.issue_text || '<span class="text-muted">No description</span>');
                    $('#viewVmDates').text('Created: ' + (data.created_at || '—') + '  ·  Updated: ' + (data.updated_at || '—'));

                    if (data.fix_note) {
                        $('#viewVmFixNote').text(data.fix_note);
                        $('#viewVmFixNoteWrap').removeClass('d-none');
                    } else {
                        $('#viewVmFixNoteWrap').addClass('d-none');
                    }

                    renderPhotoStrip('viewVmIssuePhotos', data.issue_photos);

                    if (data.fix_proof && data.fix_proof.length) {
                        renderPhotoStrip('viewVmFixProof', data.fix_proof);
                        $('#viewVmFixProofWrap').removeClass('d-none');
                    } else {
                        $('#viewVmFixProofWrap').addClass('d-none');
                    }

                    $('#viewVmSkeleton').addClass('d-none');
                    $('#viewVmContent').removeClass('d-none');
                })
                .fail(function () {
                    $('#viewVmSkeleton').addClass('d-none');
                    $('#viewVmContent').removeClass('d-none').html('<div class="alert alert-danger">Failed to load issue details.</div>');
                });
        })
        $(document).on('click', '.assign-user', function () {
            event.preventDefault();
            var vmId = $(this).data('vm-id');
            $('#assignVmFixPersonForm').attr('action', base_url+'vm/fix-issues/'+vmId+'/assign-user');
            $('#assignVmFixPerson').modal('show');
        })
        $(document).on('click', '.upload-proof', function () {
            event.preventDefault();
            var vmId = $(this).data('vm-id');
            $('#vmFixPhotosForm').attr('action', base_url+'vm/fix-issues/'+vmId+'/upload-proof');
            $('#vmFixPhotos').modal('show');
        })
        $(document).on('change', '.change-status', function () {
            var vmId = $(this).data('vm-id');
            var selectedValue = $(this).val();
            var $select = $(this);
            $select.prop('disabled', true);
            $.post(base_url + 'vm/fix-issues/' + vmId + '/change-fix-status', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                issue_fix_status: selectedValue
            }).done(function () {
                if (window.vmFixTable) window.vmFixTable.ajax.reload(null, false);
            }).fail(function () {
                alert('Failed to update status. Please try again.');
            }).always(function () {
                $select.prop('disabled', false);
            });
        })
    </script>

    {{-- Server-side DataTable for VM Issue Fix --}}
    <script>
        $(function () {
            var vmFixTable = $('#vm-issue-fix-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: base_url + 'vm/fix-issues/datatable',
                    type: 'GET',
                },
                columns: [
                    { data: 'DT_RowIndex',       name: 'DT_RowIndex',       orderable: false, searchable: false },
                    { data: 'store_asset',        name: 'store_asset' },
                    { data: 'issue_preview',      name: 'issue_preview' },
                    { data: 'issue_photos',       name: 'issue_photos',      orderable: false, searchable: false },
                    { data: 'assigned_to_name',   name: 'assigned_to_name',  orderable: false },
                    { data: 'assigned_by_name',   name: 'assigned_by_name',  orderable: false, searchable: false },
                    { data: 'fix_photos',         name: 'fix_photos',        orderable: false, searchable: false },
                    { data: 'status_select',      name: 'status_select',     orderable: false, searchable: false },
                    { data: 'actions',            name: 'actions',           orderable: false, searchable: false },
                ],
                order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center gap-2"f>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                language: {
                    searchPlaceholder: 'Search...',
                    sSearch: '',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'No records found',
                    zeroRecords: 'No matching records found',
                    processing: '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>',
                    paginate: {
                        previous: "<i class='ri-arrow-left-s-line'></i>",
                        next: "<i class='ri-arrow-right-s-line'></i>"
                    }
                },
            });

            window.vmFixTable = vmFixTable;
        });
    </script>

    <script src="{{ asset('backend/build/assets/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}"></script>
    <script src="{{ asset('backend/build/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}"></script>
    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginImageExifOrientation,
            FilePondPluginFileValidateType,
            FilePondPluginFileValidateSize
        );

        const vmFixPond = FilePond.create(document.querySelector('.filepond-vm-fix-proof'), {
            allowMultiple: true,
            instantUpload: false,
            allowProcess: false,
            allowRevert: false,
            maxFiles: 10,
            credits: false,
            acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
            maxFileSize: '5MB',
            labelMaxFileSizeExceeded: 'Image is too large',
            labelMaxFileSize: 'Maximum image size is 5 MB',
            labelIdle: '<i class="ri-upload-cloud-2-line" style="font-size:1.45rem;color:var(--text-muted)"></i><br><span class="text-muted fs-13">Drag & drop images or <span class="filepond--label-action">browse</span></span>',
            imagePreviewHeight: 150,
        });

        // Reset FilePond files when modal closes
        document.getElementById('vmFixPhotos').addEventListener('hidden.bs.modal', function () {
            vmFixPond.removeFiles();
        });

        $('#vmFixPhotosForm').on('submit', function (e) {
            e.preventDefault();

            const files = vmFixPond.getFiles();
            if (!files.length) {
                alert('Please select at least one image.');
                return;
            }

            const formData = new FormData(this);
            formData.delete('fix_proof[]');
            files.forEach(function (fileItem) {
                formData.append('fix_proof[]', fileItem.file);
            });

            const $btn = $('#btnVmFixProofSubmit');
            $btn.prop('disabled', true).text('Uploading...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('#vmFixPhotos').modal('hide');
                    if (window.vmFixTable) window.vmFixTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Upload failed. Please try again.');
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Upload');
                }
            });
        });

        $('#assignVmFixPersonForm').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            const $btn = $('#btnVmIssueFixUser');
            $btn.prop('disabled', true).text('Uploading...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('#assignVmFixPerson').modal('hide');
                    if (window.vmFixTable) window.vmFixTable.ajax.reload(null, false);
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Could not assign to this issue. Please try again.');
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Upload');
                }
            });
        });
    </script>
@endpush
