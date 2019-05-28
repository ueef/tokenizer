<?php
declare(strict_types=1);

namespace Ueef\Encoder\Tests {

    use PHPUnit\Framework\TestCase;
    use Ueef\Encrypter\Encrypters\OpensslEncrypter;

    class OpensslEncrypterTest extends TestCase
    {
        public function test()
        {
            $encrypter = new OpensslEncrypter(random_bytes(16), 'AES-256-CBC');
            $payload = random_bytes(4);

            $a = $encrypter->encrypt($payload);
            $b = $encrypter->decrypt($a);


            $this->assertEquals($payload, $b);
        }

        public function testIncorrectValue()
        {
            $encrypter = new OpensslEncrypter(random_bytes(16), 'AES-256-CBC');
            $this->assertEquals($encrypter->decrypt(random_bytes(256)), '');
            $this->assertEquals($encrypter->decrypt(''), '');
        }
    }
}