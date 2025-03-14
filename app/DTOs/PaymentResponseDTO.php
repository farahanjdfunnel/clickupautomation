<?php

namespace App\DTOs;

class PaymentResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $reference = null,
        public readonly ?string $error = null,
        public readonly ?object $result = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'reference' => $this->reference,
            'error' => $this->error,
            'result' => $this->result,
        ];
    }
}
