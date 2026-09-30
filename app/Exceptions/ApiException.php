<?php

namespace App\Exceptions;

use Exception;

/**
 * Uniform, frontend-facing API error. Controllers throw this instead of
 * returning ad-hoc error shapes; the handler in bootstrap/app.php renders it
 * as { "message": ..., "code": ... } with the given HTTP status.
 */
class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
        public readonly ?string $errorCode = null,
    ) {
        parent::__construct($message);
    }
}
