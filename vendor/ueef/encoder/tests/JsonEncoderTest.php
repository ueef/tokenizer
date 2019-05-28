<?php
declare(strict_types=1);

namespace Ueef\Encoder\Tests;

use PHPUnit\Framework\TestCase;
use Ueef\Encoder\Encoders\JsonEncoder;
use Ueef\Encoder\Exceptions\EncoderException;

class JsonEncoderTest extends TestCase
{
    public function testEncode()
    {
        $encoder = new JsonEncoder();
        $a = [1,2,3, [1, 2, 3]];
        $this->assertEquals($encoder->encode($a), json_encode($a));
    }

    public function testDecode()
    {
        $encoder = new JsonEncoder();
        $a = $a = [1,2,3, [1, 2, 3]];
        $a = json_encode($a);
        $this->assertEquals($encoder->decode($a), json_decode($a, true));

        $this->expectException(EncoderException::class);
        $encoder->decode("");
    }
}