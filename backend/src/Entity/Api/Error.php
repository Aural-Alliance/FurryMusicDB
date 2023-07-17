<?php

declare(strict_types=1);

namespace App\Entity\Api;

use ReflectionClass;
use Throwable;

final class Error
{
    public int $code;

    public string $type;

    public string $message;

    public ?string $formatted_message;

    public array $extra_data;

    public bool $success;

    public function __construct(
        int $code = 500,
        string $message = 'General Error',
        ?string $formattedMessage = null,
        array $extraData = [],
        string $type = 'Error'
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->formatted_message = ($formattedMessage ?? $message);
        $this->extra_data = $extraData;
        $this->type = $type;
        $this->success = false;
    }

    public static function notFound(): self
    {
        return new self(404, 'Record not found');
    }

    public static function fromFileError(int $fileError): self
    {
        $errorMessage = match ($fileError) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive from the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory is available.',
            UPLOAD_ERR_CANT_WRITE => 'Could not write to filesystem.',
            UPLOAD_ERR_EXTENSION => 'Upload halted by a PHP extension.',
            default => 'Unspecified error.',
        };

        return new self(500, $errorMessage);
    }

    public static function fromException(Throwable $e, bool $includeTrace = false): self
    {
        $code = (int)$e->getCode();
        if (0 === $code) {
            $code = 500;
        }

        $className = (new ReflectionClass($e))->getShortName();

        $errorHeader = $className . ' at ' . $e->getFile() . ' L' . $e->getLine();
        $message = $errorHeader . ': ' . $e->getMessage();

        $messageFormatted = '<b>' . $errorHeader . ':</b> ' . $e->getMessage();
        $extraData = [];

        if ($includeTrace) {
            $extraData['trace'] = $e->getTrace();
        }

        return new self($code, $message, $messageFormatted, $extraData, $className);
    }
}
