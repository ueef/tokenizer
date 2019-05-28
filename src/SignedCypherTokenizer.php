<?php
declare(strict_types=1);

namespace Ueef\Tokenizer;

use Throwable;
use Ueef\Encoder\Interfaces\EncoderInterface;
use Ueef\Encrypter\Interfaces\EncrypterInterface;
use Ueef\Hasher\Interfaces\HasherInterface;
use Ueef\Packer\Interfaces\PackerInterface;
use Ueef\Tokenizer\Exceptions\ExpiredTokenException;
use Ueef\Tokenizer\Exceptions\InvalidTokenException;
use Ueef\Tokenizer\Interfaces\TokenizerInterface;
use Ueef\Tokenizer\Interfaces\ExpirableTokenInterface;

class SignedCypherTokenizer implements TokenizerInterface
{
    /** @var PackerInterface */
    private $packer;

    /** @var HasherInterface */
    private $hasher;

    /** @var EncoderInterface */
    private $encoder;

    /** @var EncrypterInterface */
    private $encrypter;


    public function __construct(PackerInterface $packer, HasherInterface $hasher, EncoderInterface $encoder, EncrypterInterface $encrypter)
    {
        $this->packer = $packer;
        $this->hasher = $hasher;
        $this->encoder = $encoder;
        $this->encrypter = $encrypter;
    }

    public function build(object $token): string
    {
        $token = $this->packer->pack($token);
        $token = $this->encoder->encode($token);
        $token = $this->hasher->hash($token) . ':' . $token;
        $token = $this->encrypter->encrypt($token);

        return $token;
    }

    public function parse(string $token): object
    {
        $token = $this->decrypt($token);
        $token = $this->verify($token);
        $token = $this->decode($token);
        $token = $this->unpack($token);

        if ($token instanceof ExpirableTokenInterface && $token->isExpired()) {
            throw new ExpiredTokenException('token is expired');
        }

        return $token;
    }

    private function decrypt(string $token): string
    {
        try {
            return $this->encrypter->decrypt($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException('token is invalid', $e);
        }
    }

    private function verify(string $token): string
    {
        [$hash, $token] = explode(':', $token, 2) + ['', ''];
        if ($this->hasher->compare($token, $hash)) {
            return $token;
        }

        throw new InvalidTokenException('token is invalid');
    }

    private function decode(string $token): array
    {
        try {
            return $this->encoder->decode($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException('token is invalid', $e);
        }
    }

    private function unpack(array $token): object
    {
        try {
            return $this->packer->unpack($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException('token is invalid', $e);
        }
    }
}