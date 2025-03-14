<html <head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Custom iPOSpays Page</title>
    <link rel="stylesheet" href="{{ asset('assets/css/crm-payment/payment-loader.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body style="margin:0 !important">
    <h2 class="d-flex justify-content-center">Please Wait ...</h2>
    </div>
    <div class="loader d-none">
        @include('paymentprovider.loader')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        (() => {
            let ConnectedToNoomerik = false;
            window.addEventListener('load', function() {
                window.parent.postMessage({
                    message: 'REQUEST_USER_DATA'
                }, '*');
            });
            async function verifySSOToken(ssoToken) {
                try {
                    const res = await fetch('/decrypt-sso', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ssoToken,
                            type: 'customPage'
                        })
                    });
                    const data = await res.json();
                    if (data.status) {
                        window.location.href = "/";
                    } else {
                        $('#app').show();
                        alert(data.message || 'Token verification failed.');
                    }
                } catch (error) {
                    console.error("Error during token verification:", error);
                    alert('An error occurred while processing your request. Please try again later.');
                }
            }

            function toggleLoader(show) {
                const loaderElement = document.querySelector('.loader');
                if (show) {
                    loaderElement.classList.remove('d-none'); // Show the loader
                } else {
                    loaderElement.classList.add('d-none'); // Hide the loader
                }
            }
            window.addEventListener('message', async function(event) {
                const data = event.data;
                console.log(event);
                // Handle loader control
                if (data.message === 'REQUEST_USER_DATA_RESPONSE') {
                    toggleLoader(true);
                    await verifySSOToken(data.payload);
                    toggleLoader(false);
                }
            });
        })()
    </script>
</body>

</html>
