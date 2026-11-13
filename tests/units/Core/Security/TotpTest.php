<?php

namespace KanboardTests\units\Core\Security;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Totp;

class TotpTest extends Base
{
    const RFC_SECRET = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

    public function testHotpWithRfc4226Vectors()
    {
        $totp = new Totp;
        $expected = array('755224', '287082', '359152', '969429', '338314', '254676', '287922', '162583', '399871', '520489');

        foreach ($expected as $counter => $code) {
            $this->assertSame($code, $totp->hotp('12345678901234567890', $counter));
        }
    }

    public function testVerifyWithRfc6238Vectors()
    {
        $totp = new Totp;
        $vectors = array(
            59 => '287082',
            1111111109 => '081804',
            1111111111 => '050471',
            1234567890 => '005924',
            2000000000 => '279037',
            20000000000 => '353130',
        );

        foreach ($vectors as $timestamp => $code) {
            $this->assertTrue($totp->verify(self::RFC_SECRET, $code, $timestamp, 0));
        }
    }

    public function testVerifyTimeWindow()
    {
        $totp = new Totp;
        $timestamp = 1234567890;
        $code = $totp->hotp($totp->base32Decode(self::RFC_SECRET), intdiv($timestamp, Totp::PERIOD));

        $this->assertTrue($totp->verify(self::RFC_SECRET, $code, $timestamp));
        $this->assertTrue($totp->verify(self::RFC_SECRET, $code, $timestamp - 30));
        $this->assertTrue($totp->verify(self::RFC_SECRET, $code, $timestamp + 30));
        $this->assertFalse($totp->verify(self::RFC_SECRET, $code, $timestamp - 60));
        $this->assertFalse($totp->verify(self::RFC_SECRET, $code, $timestamp + 60));
        $this->assertFalse($totp->verify(self::RFC_SECRET, $code, $timestamp + 30, 0));
    }

    public function testVerifyRejectsInvalidCodes()
    {
        $totp = new Totp;
        $secret = $totp->generateSecret();

        $this->assertFalse($totp->verify($secret, ''));
        $this->assertFalse($totp->verify($secret, '1234'));
        $this->assertFalse($totp->verify($secret, 'abcdef'));
    }

    public function testBase32Decode()
    {
        $totp = new Totp;

        $this->assertSame('12345678901234567890', $totp->base32Decode(self::RFC_SECRET));
        $this->assertSame('12345678901234567890', $totp->base32Decode('gezd gnbv gy3t qojq gezd gnbv gy3t qojq'));
        $this->assertSame('foobar', $totp->base32Decode('MZXW6YTBOI======'));
        $this->assertSame('', $totp->base32Decode(''));
    }

    public function testGenerateSecret()
    {
        $totp = new Totp;
        $secret = $totp->generateSecret();

        $this->assertMatchesRegularExpression('/^[A-Z2-7]{16}$/', $secret);
        $this->assertNotSame($secret, $totp->generateSecret());
        $this->assertSame(10, strlen($totp->base32Decode($secret)));
    }

    public function testGetKeyUri()
    {
        $totp = new Totp;

        $this->assertSame('otpauth://totp/me?secret=mySecret&issuer=Kanboard', $totp->getKeyUri('me', 'mySecret', 'Kanboard'));
        $this->assertSame('otpauth://totp/John%20Doe%3Aa@b.c?secret=ABC&issuer=My%20Kanboard', $totp->getKeyUri('John Doe:a@b.c', 'ABC', 'My Kanboard'));
    }
}
