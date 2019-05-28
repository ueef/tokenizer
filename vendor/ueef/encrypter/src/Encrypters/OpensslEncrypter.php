<?php
declare(strict_types=1);

namespace Ueef\Encrypter\Encrypters;

use Ueef\Encrypter\Exceptions\InvalidEncryptedStringException;
use Ueef\Encrypter\Interfaces\EncrypterInterface;

class OpensslEncrypter implements EncrypterInterface
{
    /** @var string */
    private $key;

    /** @var string */
    private $method;

    /** @var integer */
    private $iv_length;


    public function __construct(string $key, string $method)
    {
        $this->key = $key;
        $this->method = $method;
        $this->iv_length = openssl_cipher_iv_length($method);
    }

    public function encrypt(string $value): string
    {
        $iv = openssl_random_pseudo_bytes($this->iv_length);
        $value = openssl_encrypt($value, $this->method, $this->key, OPENSSL_RAW_DATA, $iv);
        $value = $iv . $value;
        $value = base64_encode($value);
        $value = strtr($value, '+/', '-_');
        $value = rtrim($value, '=');

        return $value;
    }

    public function decrypt(string $value): string
    {
        $value = strtr($value, '-_', '+/');
        $value = base64_decode($value);
        $iv = substr($value, 0, $this->iv_length);
        $value = substr($value, $this->iv_length);
        if (false === $value) {
            throw new InvalidEncryptedStringException("encrypted string is invalid");
        }

        $value = openssl_decrypt($value, $this->method, $this->key, OPENSSL_RAW_DATA, $iv);
        if (false === $value) {
            throw new InvalidEncryptedStringException("encrypted string is invalid");
        }

        return $value;
    }
}