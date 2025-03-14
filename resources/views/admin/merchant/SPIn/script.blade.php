<script>
    $(document).ready(function() {
        // Open Edit Modal and Populate Data
        $(document).on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            var tpn = $(this).data('tpn');
            var authKey = $(this).data('auth-key');
            var environment = $(this).data('environment');
            // Populate the form fields with the data
            $('#editId').val(id);
            $('#editTpn').val(tpn);
            $('#editAuthKey').val(authKey);
            $('#environment').val(environment);

            // Show the Edit Modal
            $('#editModal').modal('show');
        });
        $(document).on('click', '#btn-add', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const locationId = urlParams.get('id');
            $('#locationId').val(locationId);
            $('#addModal').modal('show');
        });
        // Handle Delete Button
        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data('id');

            // Show confirmation dialog
            $('#deleteModal').modal('show');

            // Confirm delete action
            $('#confirmDeleteBtn').off('click').on('click', function() {
                $.ajax({
                    url: '/delete-spin/' + id,
                    method: 'DELETE',
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        alert('Record deleted successfully!');
                        // Optionally, refresh the table or remove the row
                        $('#row_' + id).remove();
                    },
                    error: function() {
                        alert('Error deleting record.');
                    }
                });
            });
        });

        $('#edit-spin-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            var data = $(this).serialize();
            var url = '{{ route('admin.merchant.spin.update') }}';
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    try {
                        $('#editModal').modal('hide');
                        $form.removeClass('was-validated');
                        toastr.success('Saved');
                        $('#SPIn-table').DataTable().ajax.reload(null, false);
                    } catch (error) {
                        toastr.error(error);
                    }
                    console.log('Data saved successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving data:', error);
                }
            });
        });
        $('#add-spin-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            var data = $(this).serialize();
            var url = '{{ route('admin.merchant.spin.store') }}';
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    try {
                        $('#addModal').modal('hide');
                        $form.removeClass('was-validated');
                        $form[0].reset();
                        toastr.success('Saved');
                        $('#SPIn-table').DataTable().ajax.reload(null, false);
                    } catch (error) {
                        toastr.error(error);
                    }
                    console.log('Data saved successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Error saving data:', error);
                }
            });
        });
        $(document).on('click', '#delete-spin', function() {
            var id = $(this).data('id');
            var location_id = $(this).data('location-id');
            var url = $(this).data('url');
            var table_name = 'SPIn-table';
            confirmDelete(id, location_id, url, table_name);
        });

    });
</script>
