<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Interfaces;

interface TokenizerInterface
{
    public function build(TokenInterface $token): string;
    public function parse(string $token): TokenInterface;
}