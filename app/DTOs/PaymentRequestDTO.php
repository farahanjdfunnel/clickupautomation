<?php

namespace App\DTOs;

class PaymentRequestDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $referenceId,
        public readonly string $transactionType,
        public readonly float $amount,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly string $type,
        public readonly ?string $publishableKey = null,
        public readonly ?string $cardToken = null,
        public readonly ?string $url = null,
        public readonly ?string $payment_type = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            referenceId: $data['ref_id'],
            transactionType: $data['transaction_type'],
            amount: $data['amount'],
            customerName: $data['customer_name'],
            customerEmail: $data['customer_email'],
            type: $data['type'],
            publishableKey: $data['publishableKey'] ?? null,
            cardToken: $data['card_token'] ?? null,
            url: $data['url'] ?? null,
            payment_type : $data['payment_type']?? 'external-payment'
        );
    }
}
