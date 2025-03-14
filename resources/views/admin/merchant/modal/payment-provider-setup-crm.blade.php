<!-- Edit Modal -->
<div class="modal" id="paymentProviderModal" tabindex="-1" aria-labelledby="paymentProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <input type="hidden" name="location_id" id="locationId" value="">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentProviderModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to create a payment provider for this sub-account?</p>
                <div class="d-flex align-items-center">
                    <span class="badge bg-info me-2">Sub-Account ID:</span>
                    <span id="subAccountId" class="fw-bold"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmBtn" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>
