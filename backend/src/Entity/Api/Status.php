<?php

declare(strict_types=1);

namespace App\Entity\Api;

class Status
{
    public bool $success;

    public string $message;

    public string $formatted_message;

    public function __construct(
        bool $success = true,
        string $message = 'Changes saved successfully.',
        ?string $formattedMessage = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->formatted_message = $formattedMessage ?? $message;
    }

    public static function success(): self
    {
        return new self(true, 'Changes saved successfully.');
    }

    public static function created(): self
    {
        return new self(true, 'Record created successfully.');
    }

    public static function updated(): self
    {
        return new self(true, 'Record updated successfully.');
    }

    public static function deleted(): self
    {
        return new self(true, 'Record deleted successfully.');
    }
}
