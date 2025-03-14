<?php

namespace App\DTOs;

class SpinPaymentRequestDTO extends PaymentRequestDTO
{
    public function __construct(
        int $userId,
        string $referenceId,
        string $transactionType,
        float $amount,
        string $customerName,
        string $customerEmail,
        string $type,
        string $url,
        public readonly string $tpn,
        public readonly string $authKey,
        public readonly ?float $tipAmount = null,
        public readonly string $paymentType = 'Credit',
        public readonly string $printReceipt = 'No',
        public readonly string $getReceipt = 'No',
        public readonly ?string $merchantNumber = null,
        public readonly bool $captureSignature = false,
        public readonly bool $getExtendedData = true,
        public readonly ?string $externalReceipt = '',
        public readonly ?string $spinProxyTimeout = null,
    ) {
        parent::__construct(
            userId: $userId,
            referenceId: $referenceId,
            transactionType: $transactionType,
            amount: $amount,
            customerName: $customerName,
            customerEmail: $customerEmail,
            type: $type,
            url : $url
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            referenceId: $data['ref_id'],
            transactionType: $data['transaction_type'],
            amount: $data['amount'],
            customerName: $data['customer_name'],
            customerEmail: $data['customer_email'],
            type: 'spin',
            tpn: $data['tpn'],
            authKey: $data['auth_key'],
            tipAmount: $data['tip_amount'] ?? null,
            merchantNumber: $data['merchant_number'] ?? null,
            captureSignature: $data['capture_signature'] ?? false,
            getExtendedData: $data['get_extended_data'] ?? true,
            externalReceipt: $data['external_receipt'] ?? '',
            spinProxyTimeout: $data['spin_proxy_timeout'] ?? null,
            url: $data['url'] ?? null
        );
    }
}