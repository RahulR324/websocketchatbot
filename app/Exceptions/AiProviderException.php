<?php

namespace App\Exceptions;

use Exception;

class AiProviderException extends Exception
{
    public function __construct(string $message = 'AI provider is unavailable.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
