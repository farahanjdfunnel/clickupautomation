<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status</title>
    <style>
        /* Basic page styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #2c3e50;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        #status-message {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .failure {
            color: red;
        }

        #charge-id {
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container" id="payment-result">
        <div id="payment-result" style="text-align: center; padding: 20px;">
            <!-- This will show the result of payment -->
            <h2>Payment Status</h2>
            <div id="status-message"></div>
            <p id="charge-id" style="font-weight: bold;"></p>
        </div>
    </div>
    <script>
        // Function to initialize post messages
        function init_post(data) {
            window.parent.postMessage(JSON.stringify(data), '*');
        }

        // Function to handle payment success
        function payment_success(chargeid) {
            document.getElementById('status-message').innerHTML = '<span style="color: green;">Payment Successful!</span>';
            document.getElementById('charge-id').textContent = 'Charge ID: ' + chargeid;

            // Send success post message to parent
            init_post({
                type: 'custom_element_success_response',
                chargeId: chargeid,
            });
        }

        // Function to handle payment failure
        function payment_failed(errorMessage) {
            document.getElementById('status-message').innerHTML = '<span style="color: red;">Payment Failed!</span>';
            document.getElementById('charge-id').textContent = 'Error: ' + errorMessage;

            // Send failure post message to parent
            init_post({
                type: 'custom_element_error_response',
                error: {
                    description: errorMessage,
                }
            });
        }

        // Ensure the script runs after DOM content is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Replace with dynamic data passed from the Blade controller
            const chargeId = '{{ $chargeid }}'; // Replace with dynamic data from Blade controller
            const paymentStatus = '{{ $payment_status }}'; // Replace with dynamic status from Blade controller

            if (paymentStatus === 'success') {
                payment_success(chargeId);
            } else {
                payment_failed("{{ $message }}");
            }
        });
    </script>
</body>

</html>
