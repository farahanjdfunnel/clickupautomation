<script>
    $(document).ready(function() {
        // Open Edit Modal and Populate Data
        $(document).on('click', '.btn-add-hpp', function() {
            var locationId = $(this).data('location-id');
            var locationName = $(this).data('location-name');
            var userId = $(this).data('id');
            // $('#location_id').val(encrypt(locationId));
            $('#user_id').val(encrypt(userId));
            $('#location_id').val(encrypt(locationId));


            const encodedSettings = $(this).data('setting');
            const settings = JSON.parse(atob(encodedSettings));
            console.log('Settings:', settings);
            $('#hpp_auth_token').val(settings.hpp_auth_token || '');
            $('#hpp_tpn').val(settings.hpp_tpn || '');
            $('#environment').val(settings.environment || '');
            $('#hppModal').modal('show');
        });

        $(document).on('click', '.btn-setup-payment-provider', function() {
            var id = $(this).data('id');
            $('#subAccountId').text(id);
            // Show confirmation dialog
            $('#paymentProviderModal').modal('show');

            // Confirm delete action
            $('#confirmBtn').off('click').on('click', function() {
                $.ajax({
                    url: "{{ route('admin.merchant.payment-provider.crm') }}",
                    method: 'Post',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr(
                        'content'),
                        'location_id': id
                    },
                    success: function(response) {
                        $('#paymentProviderModal').modal('hide');
                          toastr.success('Payment Provider Setup successfully!');
                    },
                    error: function() {
                          toastr.success('Error deleting record.');
                    }
                });
            });
        });


        $('#setup-hpp-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            var data = $(this).serialize();
            var url = '{{ route('admin.merchant.payment-provider.setup-hpp') }}';
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(response) {
                    try {
                        $('#hppModal').modal('hide');
                        $form.removeClass('was-validated');
                        toastr.success('Saved');
                        $('#merchant-table').DataTable().ajax.reload(null, false);
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
    });
</script>
