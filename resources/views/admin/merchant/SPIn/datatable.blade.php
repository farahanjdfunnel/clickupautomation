<script>
    var SPIn_table = null;
    var checkedStatuses = {};
    (function($) {
        "use strict";

        $(function() {
            if ($.fn.DataTable.isDataTable('#SPIn-table')) {
                $('#SPIn-table').DataTable().destroy();
            }

            SPIn_table = $('#SPIn-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: ({
                    url: "{{ route('admin.merchant.spin.table-data')}}?location_id={{ $location_id }}",
                    method: "POST",
                    data: function(d) {
                        d._token = '{{ csrf_token() }}'
                    },
                    error: function(request, status, error) {
                        console.log(request.responseText);
                    }
                }),
                "columns": [{
                        data: "id",
                        name: "id"
                    },
                    {
                        data: "tpn",
                        name: "tpn"
                    },
                    {
                        data: "auth_key",
                        name: "auth_key"
                    },
                     {
                        data: "environment",
                        name: "environment"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ],
                responsive: true,
                "bStateSave": true,
                "bAutoWidth": false,
                "ordering": false,
                "searching": true,
                "language": {
                    "decimal": "",
                    "emptyTable": "@lang('translation.no_data_found')",
                    "info": "@lang('translation.showing')" + " _START_ " + "@lang('translation.to')" + " _END_ " +
                        "@lang('translation.of')" +
                        " _TOTAL_ " + "@lang('translation.entries')",
                    "infoEmpty": "@lang('translation.showing_0_to_0_of_0_entries')",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "@lang('translation.show')" + " _MENU_ " + "@lang('translation.entries')",
                    "loadingRecords": "@lang('translation.loading')",
                    "processing": "@lang('translation.processing')",
                    "search": "@lang('translation.search')",
                    "zeroRecords": "@lang('translation.no_matching_records_found')",
                    "paginate": {
                        "first": "@lang('translation.first')",
                        "last": "@lang('translation.last')",
                        "previous": "<i class='ti-angle-left'></i>",
                        "next": "<i class='ti-angle-right'></i>"
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    // Assign a unique ID to each row based on the device's ID
                    $(row).attr('id', 'terminal-' + data.id);
                },
                drawCallback: function(settings) {
                    $(".dataTables_paginate > .pagination").addClass("pagination-bordered");
                    // Once the table is drawn, let's check the status of each device
                    var rows = settings.json.data; // Get the device data from the table

                    // Loop through each row in the table
                    // Once the table is drawn, let's check the status of each device
                    var rows = settings.json.data; // Get the device data from the table

                    // Loop through each row in the table
                    rows.forEach(function(terminal) {
                        var tpn = terminal.tpn; // Extract the tpn from the row
                        var authKey = terminal.auth_key; // Extract the authKey from the row
                        var environment = terminal.environment; 
                        if (checkedStatuses[terminal.id]) {
                            return; // Skip if the status is already checked
                        }
                        // Make an AJAX request to check the status of the device
                        $.ajax({
                            url: '/crm-payment-ipospays/spin-terminal-status?environment='+environment, // Use the provided URL
                            method: 'GET',
                            data: {
                                tpn: tpn, // Send the tpn parameter
                                authKey: authKey // Send the authKey parameter
                            },
                            success: function(response) {
                                var status = response.data.TerminalStatus||'Offline';
                                checkedStatuses[terminal.id] = true;
                                var row = SPIn_table.row('#terminal-' + terminal.id); // Assuming you have an ID for each row
                                row.cell(':eq(3)').data(status).draw(); // Update status column (4th column)

                                // Optionally, you could change the status color
                                if (status === 'Online') {
                                    //row.cell(':eq(3)').node().style.backgroundColor = '#4CAF50'; // Green for online
                                } else {
                                    // row.cell(':eq(3)').node().style.backgroundColor = '#F44336'; // Red for offline
                                }
                            },
                            error: function() {
                                console.log("Error checking device status");
                            }
                        });
                    });
                }
            });
        });

    })(jQuery);
</script>