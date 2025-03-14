<!-- Edit Modal -->
<div class="modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="edit-spin-form">
            @csrf
            <input type="hidden" name="id" id="editId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tpn" class="form-label">TPN</label>
                        <input type="text" class="form-control" id="editTpn" name="tpn" required>
                    </div>
                    <div class="mb-3">
                        <label for="auth_key" class="form-label">Auth Key</label>
                        <input type="text" class="form-control" id="editAuthKey" name="auth_key" required>
                    </div>
                     <div class="mb-3">
                        <label for="environment" class="form-label">Environment</label>
                        <select class="form-select" id="environment" name="environment" required>
                            <option value="sandbox">Sandbox</option>
                            <option value="live">Live</option>
                        </select>
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