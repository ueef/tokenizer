<?php
declare(strict_types=1);

namespace Ueef\Encoder\Encoders;

use Ueef\Encoder\Exceptions\EncoderException;
use Ueef\Encoder\Interfaces\EncoderInterface;


class JsonEncoder implements EncoderInterface
{
    /** @var integer */
    private $encode_options;


    public function __construct(int $encodeOptions = 0)
    {
        $this->encode_options = $encodeOptions;
    }

    public function encode($message): string
    {
        $message = json_encode($message, $this->encode_options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new EncoderException('Json encoding error: ' . json_last_error_msg(), json_last_error());
        }

        return $message;
    }

    public function decode(string $message)
    {
        $message = json_decode($message, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new EncoderException('Json decoding error: ' . json_last_error_msg(), json_last_error());
        }

        return $message;
    }
}