<?php
declare(strict_types=1);

namespace Ueef\Tokenizer\Interfaces;

interface TokenizerInterface
{
    public function build(object $token): string;
    public function parse(string $token): object;
}