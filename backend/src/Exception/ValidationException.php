<?php

declare(strict_types=1);

namespace App\Exception;

use App\Exception;
use Psr\Log\LogLevel;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class ValidationException extends Exception
{
    private ConstraintViolationListInterface $detailedErrors;

    public function __construct(
        string $message = 'Validation error.',
        int $code = 0,
        Throwable $previous = null,
        string $loggerLevel = LogLevel::INFO
    ) {
        parent::__construct($message, $code, $previous, $loggerLevel);
    }

    public function getDetailedErrors(): ConstraintViolationListInterface
    {
        return $this->detailedErrors;
    }

    public function setDetailedErrors(ConstraintViolationListInterface $detailedErrors): void
    {
        $this->detailedErrors = $detailedErrors;
    }

    public static function fromValidationErrors(ConstraintViolationListInterface $detailedErrors): self
    {
        $exception = new self($detailedErrors->__toString());
        $exception->setDetailedErrors($detailedErrors);
        return $exception;
    }
}
