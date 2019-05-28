<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Interfaces;

interface ExpirableTokenInterface
{
    public function isExpired(): bool;
}