<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Exceptions;

use Exception;
use Throwable;
use Ueef\Tokenizer\Interfaces\TokenizerExceptionInterface;

abstract class AbstractException extends Exception implements TokenizerExceptionInterface
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}