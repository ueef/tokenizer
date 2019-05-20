<?php
declare(strict_types=1);

namespace Ueef\Tokenizer;

use Throwable;
use Ueef\Encoder\Interfaces\EncoderInterface;
use Ueef\Encrypter\Interfaces\EncrypterInterface;
use Ueef\Hasher\Interfaces\HasherInterface;
use Ueef\Tokenizer\Exceptions\ExpiredTokenException;
use Ueef\Tokenizer\Exceptions\InvalidTokenException;
use Ueef\Tokenizer\Interfaces\TokenInterface;
use Ueef\Tokenizer\Interfaces\TokenizerInterface;

class SignedCypherTokenizer implements TokenizerInterface
{
    /** @var TokenInterface */
    private $proto;

    /** @var HasherInterface */
    private $hasher;

    /** @var EncoderInterface */
    private $encoder;

    /** @var EncrypterInterface */
    private $encrypter;


    public function __construct(TokenInterface $proto, HasherInterface $hasher, EncoderInterface $encoder, EncrypterInterface $encrypter)
    {
        $this->proto = $proto;
        $this->hasher = $hasher;
        $this->encoder = $encoder;
        $this->encrypter = $encrypter;
    }

    public function build(TokenInterface $token): string
    {
        $token = $this->encoder->encode($token->pack());
        $hash = $this->hasher->hash($token);

        return $this->encrypter->encrypt($hash . ':' . $token);
    }

    public function parse(string $token): TokenInterface
    {
        $token = $this->decrypt($token);
        $token = $this->verify($token);
        $token = $this->decode($token);
        $token = $this->unpack($token);

        if ($token->isExpired()) {
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

    private function unpack(array $token): TokenInterface
    {
        try {
            return $this->proto->unpack($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException('token is invalid', $e);
        }
    }
}