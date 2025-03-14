<!-- Setup Hpp Modal -->
<div class="modal" id="hppModal" tabindex="-1" aria-labelledby="hppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="setup-hpp-form">
            @csrf
            <input type="hidden" name="location_id" id="location_id">
            <input type="hidden" name="user_id" id="user_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hppModalLabel">Hosted Payment Page Integration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tpn" class="form-label">Auth Token</label>
                        <textarea class="form-control" rows='4' id="hpp_auth_token" placeholder="Enter HPP Auth Token"
                            name="setting[hpp_auth_token]" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="auth_key" class="form-label">HPP TPN</label>
                        <input type="text" class="form-control" id="hpp_tpn"
                                                placeholder="Enter HPP TPN" name="setting[hpp_tpn]"
                                                value="" required>
                    </div>
                     <div class="mb-3">
                        <label for="environment" class="form-label">Environment</label>
                        <select class="form-select" id="environment" name="setting[environment]" required>
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
