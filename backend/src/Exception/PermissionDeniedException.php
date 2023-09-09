<?php

declare(strict_types=1);

namespace App\Exception;

use App\Exception;
use Psr\Log\LogLevel;
use Throwable;

class PermissionDeniedException extends Exception
{
    public function __construct(
        string $message = 'Permission denied.',
        int $code = 403,
        Throwable $previous = null,
        string $loggerLevel = LogLevel::DEBUG
    ) {
        parent::__construct($message, $code, $previous, $loggerLevel);
    }
}
