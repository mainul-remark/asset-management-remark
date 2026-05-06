@if($permissions['canCreate'] || $permissions['canEdit'])
<div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="fileModalLabel">Add Key Visual File</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="fileForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                @csrf
                <input type="hidden" id="kv_file_id" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Homepage Hero File">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="key_visual_id" class="form-label">Key Visual <span class="text-danger">*</span></label>
                            <select class="form-select select-ele" id="key_visual_id" name="key_visual_id">
                                <option value="">Select key visual</option>
                                @foreach($keyVisuals as $keyVisual)
                                    <option value="{{ $keyVisual->id }}" data-kv-type="{{ $keyVisual->kv_type }}">
                                        {{ $keyVisual->name }}{{ $keyVisual->unique_code ? ' (' . $keyVisual->unique_code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-key_visual_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="key_visual_size_id" class="form-label">Key Visual Size</label>
                            <select class="form-select" id="key_visual_size_id" name="key_visual_size_id">
                                <option value="">Auto-detect from uploaded file</option>
                                @foreach($keyVisualSizes as $size)
                                    <option
                                        value="{{ $size->id }}"
                                        data-width="{{ (int) $size->width }}"
                                        data-height="{{ (int) $size->height }}"
                                        data-unit="{{ strtolower($size->unit_name) }}"
                                    >
                                        {{ $size->name }} ({{ rtrim(rtrim((string) $size->width, '0'), '.') }} x {{ rtrim(rtrim((string) $size->height, '0'), '.') }} {{ strtoupper($size->unit_name) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Optional. Matching sizes will be selected automatically from the uploaded file; missing dimensions will create a new size on save.</div>
                            <div class="invalid-feedback" id="error-key_visual_size_id"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="error-status"></div>
                        </div>

                        <div class="col-12">
                            <label for="kv_file_upload" class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file"
                                   class="filepond-kv-file"
                                   id="kv_file_upload"
                                   name="kv_file_upload"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp,video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">
                            <input type="hidden" id="media_width" name="media_width">
                            <input type="hidden" id="media_height" name="media_height">
                            <div class="form-text" id="kv-upload-hint">Images: max 5 MB. Videos: max 10 MB.</div>
                            <div class="invalid-feedback d-block" id="error-kv_file_upload"></div>
                            <div id="existing-file-wrap" class="small text-muted mt-2 d-none">
                                Current file: <a id="existing-file-link" href="#" target="_blank" rel="noopener">Open file</a>
                                <span class="ms-1">(uploading a new file replaces it)</span>
                            </div>
                        </div>

                        <div class="col-md-6 d-none" data-meta-field="kv_size">
                            <label for="kv_size" class="form-label">KV Size (KB) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kv_size" name="kv_size" min="0" step="1" readonly placeholder="Auto calculated">
                            <div class="invalid-feedback" id="error-kv_size"></div>
                        </div>
                        <div class="col-md-6 d-none" data-meta-field="aspect_ratio">
                            <label for="aspect_ratio" class="form-label">Aspect Ratio</label>
                            <input type="number" class="form-control" id="aspect_ratio" name="aspect_ratio" step="0.0001" min="0" readonly placeholder="Auto calculated">
                            <div class="invalid-feedback" id="error-aspect_ratio"></div>
                        </div>
                        <div class="col-md-6 d-none" data-meta-field="file_type">
                            <label for="file_type" class="form-label">File Type</label>
                            <input type="text" class="form-control" id="file_type" name="file_type" readonly placeholder="e.g. image/png">
                            <div class="invalid-feedback" id="error-file_type"></div>
                        </div>
                        <div class="col-md-6 d-none" data-meta-field="file_duration">
                            <label for="file_duration" class="form-label">File Duration</label>
                            <input type="text" class="form-control" id="file_duration" name="file_duration" readonly placeholder="Auto for video file">
                            <div class="invalid-feedback" id="error-file_duration"></div>
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
@endif

@if($permissions['canView'])
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Key Visual File Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tr><th width="32%">Name</th><td id="view-name"></td></tr>
                    <tr><th>Key Visual</th><td id="view-key-visual"></td></tr>
                    <tr><th>Key Visual Size</th><td id="view-key-visual-size"></td></tr>
                    <tr><th>KV File</th><td id="view-kv-file"></td></tr>
                    <tr><th>KV Size</th><td id="view-kv-size"></td></tr>
                    <tr><th>Aspect Ratio</th><td id="view-aspect-ratio"></td></tr>
                    <tr><th>File Type</th><td id="view-file-type"></td></tr>
                    <tr><th>File Duration</th><td id="view-file-duration"></td></tr>
                    <tr><th>Status</th><td id="view-status"></td></tr>
                    <tr><th>Created At</th><td id="view-created"></td></tr>
                    <tr><th>Updated At</th><td id="view-updated"></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@if($permissions['canDelete'])
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-4 pb-2">
                <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
                <h6>Delete Key Visual File</h6>
                <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-file-name"></strong>?</p>
                <input type="hidden" id="delete-file-id">
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="btn-confirm-delete">
                    <span class="btn-text">Yes, Delete</span>
                    <span class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
