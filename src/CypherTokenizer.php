<?php
declare(strict_types=1);

namespace Ueef\Tokenizer;

use Throwable;
use Ueef\Encoder\Interfaces\EncoderInterface;
use Ueef\Encrypter\Interfaces\EncrypterInterface;
use Ueef\Packer\Interfaces\PackerInterface;
use Ueef\Tokenizer\Exceptions\ExpiredTokenException;
use Ueef\Tokenizer\Exceptions\InvalidTokenException;
use Ueef\Tokenizer\Interfaces\ExpirableTokenInterface;
use Ueef\Tokenizer\Interfaces\TokenInterface;
use Ueef\Tokenizer\Interfaces\TokenizerInterface;

/**
 * @deprecated
 */
class CypherTokenizer implements TokenizerInterface
{
    /** @var PackerInterface */
    private $packer;

    /** @var EncoderInterface */
    private $encoder;

    /** @var EncrypterInterface */
    private $encrypter;


    public function __construct(PackerInterface $packer, EncoderInterface $encoder, EncrypterInterface $encrypter)
    {
        $this->packer = $packer;
        $this->encoder = $encoder;
        $this->encrypter = $encrypter;
    }

    public function build(TokenInterface $token): string
    {
        return $this->encrypter->encrypt($this->encoder->encode($this->packer->pack($token)));
    }

    public function parse(string $token): TokenInterface
    {
        try {
            $token = $this->encrypter->decrypt($token);
            $token = $this->encoder->decode($token);
            $token = $this->packer->unpack($token);
        } catch (Throwable $e) {
            throw new InvalidTokenException('token is invalid', $e);
        }

        if (!$token instanceof TokenInterface) {
            throw new InvalidTokenException('token is invalid');
        }

        if ($token instanceof ExpirableTokenInterface && $token->isExpired()) {
            throw new ExpiredTokenException('token is expired');
        }

        return $token;
    }
}