<?php

namespace App\Contracts;

use App\DTOs\PaymentRequestDTO;
use App\DTOs\PaymentResponseDTO;

interface PaymentProcessorInterface
{
    public function processPayment(PaymentRequestDTO $paymentRequest): PaymentResponseDTO;
}
