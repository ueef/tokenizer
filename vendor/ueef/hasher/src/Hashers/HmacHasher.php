<?php
declare(strict_types=1);

namespace Ueef\Hasher\Hashers;

use Ueef\Hasher\Interfaces\HasherInterface;

class HmacHasher implements HasherInterface
{
    /** @var string */
    private $key;

    /** @var string */
    private $algorithm;

    public function __construct(string $algorithm, string $key)
    {
        $this->key = $key;
        $this->algorithm = $algorithm;
    }

    public function hash(string $string): string
    {
        return hash_hmac($this->algorithm, $string, $this->key);
    }

    public function compare(string $string, string $hash): bool
    {
        return hash_equals($this->hash($string), $hash);
    }
}