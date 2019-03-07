<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Interfaces;

use Ueef\Packable\Interfaces\PackableInterface;

interface TokenInterface extends PackableInterface
{
    public function unpack(array $values): TokenInterface;
    public function isExpired(): bool;
}