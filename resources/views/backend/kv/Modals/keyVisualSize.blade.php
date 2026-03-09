<div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="sizeModalLabel">Add Key Visual Size</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sizeForm" style="display:flex;flex-direction:column;flex:1;overflow:hidden;min-height:0;">
                @csrf
                <input type="hidden" id="size_id" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Homepage Banner">
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="height" class="form-label">Height <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="height" name="height" min="0" step="0.01" placeholder="1080">
                            <div class="invalid-feedback" id="error-height"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="width" class="form-label">Width <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="width" name="width" min="0" step="0.01" placeholder="1920">
                            <div class="invalid-feedback" id="error-width"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="unit_name" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_name" name="unit_name">
                                <option value="">Select unit</option>
                                <option value="px">px</option>
                                <option value="in">in</option>
                                <option value="ft">ft</option>
                                <option value="cm">cm</option>
                                <option value="mm">mm</option>
                                <option value="m">m</option>
                                <option value="yd">yd</option>
                            </select>
                            <div class="invalid-feedback" id="error-unit_name"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback" id="error-status"></div>
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

<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Key Visual Size Details</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-0">
                    <tr><th width="32%">Name</th><td id="view-name"></td></tr>
                    <tr><th>Height</th><td id="view-height"></td></tr>
                    <tr><th>Width</th><td id="view-width"></td></tr>
                    <tr><th>Unit</th><td id="view-unit"></td></tr>
                    <tr><th>Status</th><td id="view-status"></td></tr>
                    <tr><th>Created At</th><td id="view-created"></td></tr>
                    <tr><th>Updated At</th><td id="view-updated"></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-4 pb-2">
                <div class="mb-3"><i class="ri-delete-bin-line text-danger" style="font-size: 3rem;"></i></div>
                <h6>Delete Key Visual Size</h6>
                <p class="text-muted mb-0">Are you sure you want to delete <strong id="delete-size-name"></strong>?</p>
                <input type="hidden" id="delete-size-id">
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
