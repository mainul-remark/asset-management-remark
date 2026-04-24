{{-- Create / Edit Modal --}}
<div class="modal fade" id="sizeModal" tabindex="-1" aria-labelledby="sizeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="sizeModalLabel">Add Key Visual Size</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sizeForm">
                @csrf
                <input type="hidden" id="size_id" value="">

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-medium">
                            Size Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="e.g. Homepage Banner, Billboard, Social Square">
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    {{-- Dimensions --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-medium mb-0">
                                Dimensions <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-primary-transparent text-primary fw-normal fs-11" id="dim-preview">-- x --</span>
                        </div>

                        <div class="input-group has-validation">
                            <span class="input-group-text text-muted" style="min-width:3rem;">
                                <i class="ri-expand-width-line me-1 fs-13"></i>W
                            </span>
                            <input type="number" class="form-control" id="width" name="width"
                                min="1" step="1" placeholder="1920">
                            <span class="input-group-text px-2 fw-semibold text-muted">&times;</span>
                            <span class="input-group-text text-muted" style="min-width:3rem;">
                                <i class="ri-expand-height-line me-1 fs-13"></i>H
                            </span>
                            <input type="number" class="form-control" id="height" name="height"
                                min="1" step="1" placeholder="1080">
                            <select class="form-select" id="unit_name" name="unit_name" style="max-width:80px;flex:0 0 80px;">
                                <option value="px">px</option>
                                <option value="in">in</option>
                                <option value="ft">ft</option>
                                <option value="cm">cm</option>
                                <option value="mm">mm</option>
                                <option value="m">m</option>
                                <option value="yd">yd</option>
                            </select>
                        </div>
                        <div class="d-flex gap-3 mt-1">
                            <div class="flex-fill">
                                <div class="invalid-feedback d-block" id="error-width" style="min-height:1rem;"></div>
                            </div>
                            <div class="flex-fill">
                                <div class="invalid-feedback d-block" id="error-height" style="min-height:1rem;"></div>
                            </div>
                            <div style="width:80px; flex:0 0 80px;">
                                <div class="invalid-feedback d-block" id="error-unit_name" style="min-height:1rem;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="d-flex justify-content-between align-items-center py-2 px-3 rounded-2 bg-light">
                        <div>
                            <div class="fw-medium fs-14">Status</div>
                            <div class="text-muted fs-12">Enable or disable this size definition</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="status_toggle" style="width:2.5rem;height:1.3rem;">
                            <input type="hidden" id="status" name="status" value="1">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="btn-spinner"></span>
                        <span class="btn-text">Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="modal-title fw-semibold mb-1" id="view-name-title">...</h6>
                    <span id="view-status-badge"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">

                {{-- Visual dimension block --}}
                <div class="d-flex align-items-center justify-content-center gap-4 py-3 mb-3 rounded-2" style="background:var(--default-border,#f0f1f5)">
                    <div class="text-center">
                        <div class="fs-11 text-muted text-uppercase fw-semibold mb-1">Width</div>
                        <div class="fs-22 fw-bold text-primary lh-1" id="view-width">-</div>
                    </div>
                    <div class="text-muted fw-semibold fs-18">&times;</div>
                    <div class="text-center">
                        <div class="fs-11 text-muted text-uppercase fw-semibold mb-1">Height</div>
                        <div class="fs-22 fw-bold text-primary lh-1" id="view-height">-</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-11 text-muted text-uppercase fw-semibold mb-1">Unit</div>
                        <div class="fs-16 fw-semibold text-dark lh-1" id="view-unit">-</div>
                    </div>
                </div>

                {{-- Ratio + metadata --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="text-muted fs-12">Aspect ratio:</span>
                    <span class="badge bg-secondary-transparent text-secondary fw-medium" id="view-ratio">-</span>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light">
                            <div class="fs-11 text-muted mb-1">Created</div>
                            <div class="fw-medium fs-13" id="view-created">-</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded-2 bg-light">
                            <div class="fs-11 text-muted mb-1">Updated</div>
                            <div class="fw-medium fs-13" id="view-updated">-</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-4 pb-2">
                <div class="mb-3">
                    <span class="avatar avatar-xl bg-danger-transparent rounded-circle">
                        <i class="ri-delete-bin-line text-danger fs-24"></i>
                    </span>
                </div>
                <h6 class="fw-semibold">Delete Size?</h6>
                <p class="text-muted fs-13 mb-0">
                    You're about to permanently delete <strong id="delete-size-name" class="text-dark"></strong>.
                    This action cannot be undone.
                </p>
                <input type="hidden" id="delete-size-id">
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4 gap-2">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                    <span class="spinner-border spinner-border-sm d-none me-1"></span>
                    <span class="btn-text">Delete</span>
                </button>
            </div>
        </div>
    </div>
</div>
