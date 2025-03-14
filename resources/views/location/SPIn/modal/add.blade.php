<!-- Edit Modal -->
<div class="modal" id="addModal" tabindex="-1" aria-labelledby="addModalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add-spin-form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Add Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tpn" class="form-label">TPN</label>
                        <input type="text" class="form-control" id="addTpn" name="tpn" required>
                    </div>
                    <div class="mb-3">
                        <label for="auth_key" class="form-label">Auth Key</label>
                        <input type="text" class="form-control" id="addAuthKey" name="auth_key" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>