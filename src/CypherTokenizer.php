<?php
declare(strict_types=1);

namespace Ueef\Tokenizer;

use Throwable;
use Ueef\Encoder\Interfaces\EncoderInterface;
use Ueef\Encrypter\Interfaces\EncrypterInterface;
use Ueef\Tokenizer\Exceptions\ExpiredTokenException;
use Ueef\Tokenizer\Exceptions\InvalidTokenException;
use Ueef\Tokenizer\Interfaces\TokenInterface;
use Ueef\Tokenizer\Interfaces\TokenizerInterface;

class CypherTokenizer implements TokenizerInterface
{
    /** @var TokenInterface */
    private $proto;

    /** @var EncoderInterface */
    private $encoder;

    /** @var EncrypterInterface */
    private $encrypter;


    public function __construct(TokenInterface $proto, EncoderInterface $encoder, EncrypterInterface $encrypter)
    {
        $this->proto = $proto;
        $this->encoder = $encoder;
        $this->encrypter = $encrypter;
    }

    public function build(TokenInterface $token): string
    {
        if ($token instanceof $this->proto) {
            return $this->encrypter->encrypt($this->encoder->encode($token->pack()));
        }

        throw new InvalidTokenException("token must be instance of " . get_class($this->proto));
    }

    public function parse(string $token): TokenInterface
    {
        try {
            $token = $this->encrypter->decrypt($token);
            $token = $this->encoder->decode($token);
            $token = $this->proto->unpack($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException("token is invalid", 0, $e);
        }

        if ($token->isExpired()) {
            throw new ExpiredTokenException("token is expired");
        }

        return $token;
    }
}