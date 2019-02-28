<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Interfaces;

use Ueef\Packable\Interfaces\PackableInterface;

interface TokenInterface extends PackableInterface
{
    /**
     * @return TokenInterface
     */
    public function unpack(array $values);
    public function isExpired(): bool;
}