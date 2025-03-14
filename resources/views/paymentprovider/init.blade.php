<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payment Form</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/crm-payment/index.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/crm-payment/payment-loader.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ URL::asset('/assets/libs/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body style="background: transparent">
    <div class="payment-provider  w-100">
        <div class="form_detail w-100">
            <div class="full_height">

                <!-- Unified Container for Payment Methods and Device List -->
                <div id="payment-content" class="payment-content">
                    <!-- Main Payment Methods -->
                    <div id="main-payment-methods" class="payment-methods-container">
                        <!-- HPP Card -->
                        <div id="main-screen" style="display:flex;">
                            <div class="payment-method-card" id="hpp-card" onclick="selectPaymentMethod('HPP')">
                                <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="HPP"
                                    class="payment-method-icon">
                                <h4>HPP</h4>
                                <p>Secure payment via Hosted Payment Page</p>
                            </div>

                            <!-- SPIN Card -->
                            <div class="payment-method-card" id="spin-card" onclick="selectPaymentMethod('SPIN')">
                                <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="SPIN"
                                    class="payment-method-icon">
                                <h4>SPIN</h4>
                                <p>Fast and secure payment via SPIN</p>
                            </div>
                        </div>
                        <div id="side-screen" class="card" style="display:none;padding: 50px;">
                            <!-- Device List (Visible only when SPIN is selected) -->
                            <div id="device-list" class="device-list">
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- Loader (Animated) -->
            <div class="loader d-none">
                @include('paymentprovider.loader')
            </div>

            <!-- Iframe Container for HPP -->
            <div id="iframe-container">
                <iframe id="paymentIframe" class="w-100 h-100" src="" frameborder="0"></iframe>
            </div>

            <!-- Confirmation Modal -->
            <div id="confirmation-modal" class="confirmation-modal">
                <div class="modal-content">
                    <h4>Confirm Terminal Selection</h4>
                    <p id="device-confirmation-msg">Do you want to proceed with this device?</p>
                    <button onclick="confirmDeviceSelection()" class="btn-primary">Yes, Proceed</button>
                    <button onclick="closeModal()" class="btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="{{ URL::asset('assets/libs/toastr/toastr.min.js') }}"></script>
    <script>
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length, c.length); // Return the cookie value
                }
            }
            return null; // Return null if cookie not found
        }

        let payload_received = null;
        let locationId = null;
        let selectedDevice = {
            authKey: '',
            tpn: ''
        };
        // Event listener for payment initialization
        window.addEventListener('message', function(e) {
            let data = JSON.parse(e.data);
            console.log({
                data
            });
            if (data.type == 'custom_element_error_response') {
                payment_failed(data.error.description);
            }
            if (data.type == 'custom_element_success_response') {
                payment_success(data.chargeId);
            }
            if (data.type == 'payment_initiate_props') {
                payload_received = data;
                console.log(payload_received);
                locationId = data.locationId;
                // Check if SPIN is enabled (using cache or payload)
                const spinEnabled = getCookie('spin_enabled');
                if (spinEnabled === 'true') {
                    document.querySelector('#spin-card').classList.remove('d-none');
                    loadSpinTerminals(locationId);
                } else {
                    document.querySelector('#spin-card').classList.add('d-none');
                    document.querySelector('#hpp-card').classList.add('d-none');
                    selectPaymentMethod('HPP');
                }
            }
        });

        // Function to handle the payment method selection
        function selectPaymentMethod(method) {
            if (method === 'HPP') {
                toggleLoader(true);
                getHppPaymentUrl(); // Function to get HPP URL
            } else if (method === 'SPIN') {
                document.getElementById('side-screen').style.display = 'block';
                document.getElementById('main-screen').style.display = 'none'; // Hide HPP
                // handleSpinPayment(); // Function to handle SPIN payment
            }
        }

        // Fetch the HPP payment URL and load it into the iframe
        function getHppPaymentUrl() {
            const url = "{{ route('crm.payment.ipospays.HPP_payment_url') }}";
            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: payload_received,
                success: function(response) {
                    toggleLoader(false); // Hide loader after response

                    if (response.url) {
                        // Set the iframe's src attribute to the HPP URL and show the iframe
                        document.getElementById('main-payment-methods').style = "display:none";
                        document.getElementById('paymentIframe').src = response.url;
                        document.getElementById('iframe-container').style.display = 'block';
                    } else 
                    {
                        toastr.error(response.message);
                        // payment_failed(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toggleLoader(false); // Hide loader in case of error
                    console.error('Error fetching HPP URL:', error);
                     toastr.error(response.error);
                     payment_cancel();
                }
            });
        }

        // Function to handle SPIN payment (This can be updated as needed)
        function handleSpinPayment(authKey, tpn) {
            toggleLoader(true); // Hide loader in case of error
            const updatedPayload = {
                ...payload_received,
                authKey: authKey,
                tpn: tpn
            };
            const url = "{{ route('crm.payment.ipospays.spin-submit') }}";
            $.ajax({
                type: 'POST',
                url: url,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                data: updatedPayload,
                success: function(response) {
                    toggleLoader(false);
                    if (response.status)
                    {
                        payment_success(resposne.charge_id);
                    } else
                    {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toggleLoader(false);
                    console.error('Error fetching HPP URL:', error);
                    payment_cancel();
                }
            });
        }

        // Function to initialize post messages
        function init_post(data) {
            window.parent.postMessage(JSON.stringify(data), '*');
        }

        // Handle payment success
        function payment_success(chargeid) {
            init_post({
                type: 'custom_element_success_response',
                chargeId: chargeid,
            });
        }

        // Handle payment failure
        function payment_failed(errorMessage) {
            init_post({
                type: 'custom_element_error_response',
                error: {
                    description: errorMessage,
                }
            });
        }
        function payment_cancel() {
            init_post({
                type: 'custom_element_close_response',
            });
        }
        init_post({
            type: 'custom_provider_ready',
            loaded: true
        });

        // Toggle the loader visibility
        function toggleLoader(show) {
            const loaderElement = document.querySelector('.loader');
            if (show) {
                loaderElement.classList.remove('d-none'); // Show the loader
            } else {
                loaderElement.classList.add('d-none'); // Hide the loader
            }
        }

        function loadSpinTerminals(locationId) {
            const url = "{{ route('crm.payment.ipospays.fetch-spin-terminals') }}" + "?locationId=" + encodeURIComponent(
                locationId);
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    if (response && response.data && response.data.length > 0) {
                        let devicesHTML = `
                            <a  onclick="goBackToMain()" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                            <h5>Select terminal to proceed SPIN Payment</h5>
                        `;
                        response.data.forEach(terminal => {
                            devicesHTML += `
                                <div class="device-card" id="device-${terminal.id}" onclick="selectDevice('${terminal.auth_key}', '${terminal.tpn}')">
                                    <span class="device-name">${terminal.auth_key}</span>
                                    <span class="device-tpn">TPN: ${terminal.tpn}</span>
                                </div>
                            `;
                        });

                        document.querySelector('#device-list').innerHTML = devicesHTML;
                    } else {
                        document.querySelector('#device-list').innerHTML = `<a  onclick="goBackToMain()" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                        <p>No devices available for SPIN payment.</p>`;
                    }
                },
                error: function() {
                    alert("Error fetching devices. Please try again later.");
                }
            });
        }

        function selectDeviceForPayment(deviceId) {
            const deviceElement = document.querySelector(`#device-${deviceId}`);

            document.querySelectorAll('.device-card').forEach(card => card.classList.remove('selected'));
            deviceElement.classList.add('selected');

            const deviceName = deviceElement.querySelector('h5').textContent;
            const tpn = deviceElement.querySelector('p:nth-child(2)').textContent.replace('TPN: ', '');
            const authKey = deviceElement.querySelector('p:nth-child(3)').textContent.replace('Auth Key: ', '');

        }

        function goBackToMain() {
            document.getElementById('main-screen').style.display = 'flex';
            document.getElementById('side-screen').style.display = 'none';
        }

        function selectDevice(authKey, tpn) {
            selectedDevice.authKey = authKey;
            selectedDevice.tpn = tpn;
            const confirmationMsg = `Do you want to proceed with (Auth key : ${authKey}) & (TPN: ${tpn})?`;
            document.getElementById("device-confirmation-msg").textContent = confirmationMsg;
            document.getElementById("confirmation-modal").style.display = "flex";
        }

        function confirmDeviceSelection() {
            document.getElementById("confirmation-modal").style.display = "none"; // Show modal
            const authKey = selectedDevice.authKey;
            const tpn = selectedDevice.tpn;
            handleSpinPayment(authKey, tpn);
        }

        function closeModal() {
            document.getElementById("confirmation-modal").style.display = "none"; // Hide modal
        }
    </script>
</body>

</html>
