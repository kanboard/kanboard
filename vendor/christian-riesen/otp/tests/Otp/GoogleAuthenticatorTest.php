<?php

require_once __DIR__ . '/../../src/Otp/GoogleAuthenticator.php';

use Otp\GoogleAuthenticator;

/**
 * GoogleAuthenticator test case.
 */
class GoogleAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tests getQrCodeUrl
	 */
	public function testGetQrCodeUrl()
	{
		$secret = 'MEP3EYVA6XNFNVNM'; // testing secret
		
		// Standard totp case
		$this->assertEquals(
			'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chld=M|0&chl=otpauth%3A%2F%2Ftotp%2Fuser%40host.com%3Fsecret%3DMEP3EYVA6XNFNVNM',
			GoogleAuthenticator::getQrCodeUrl('totp', 'user@host.com', $secret)
		);
		
		// hotp (include a counter)
		$this->assertEquals(
			'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chld=M|0&chl=otpauth%3A%2F%2Fhotp%2Fuser%40host.com%3Fsecret%3DMEP3EYVA6XNFNVNM%26counter%3D1234',
			GoogleAuthenticator::getQrCodeUrl('hotp', 'user@host.com', $secret, 1234)
		);
		
		// totp, this time with a parameter for chaning the size of the QR
		$this->assertEquals(
				'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chld=M|0&chl=otpauth%3A%2F%2Ftotp%2Fuser%40host.com%3Fsecret%3DMEP3EYVA6XNFNVNM',
				GoogleAuthenticator::getQrCodeUrl('totp', 'user@host.com', $secret, null, array('height' => 300, 'width' => 300))
		);
		
	}

	/**
	 * Tests getKeyUri
	 */
	public function testGetKeyUri()
	{
		$secret = 'MEP3EYVA6XNFNVNM'; // testing secret
		
		// Standard totp case
		$this->assertEquals(
			'otpauth://totp/user@host.com?secret=MEP3EYVA6XNFNVNM',
			GoogleAuthenticator::getKeyUri('totp', 'user@host.com', $secret)
		);

		// hotp (include a counter)
		$this->assertEquals(
			'otpauth://hotp/user@host.com?secret=MEP3EYVA6XNFNVNM&counter=1234',
			GoogleAuthenticator::getKeyUri('hotp', 'user@host.com', $secret, 1234)
		);

		// totp/hotp with an issuer in the label
		$this->assertEquals(
			'otpauth://hotp/issuer%3Auser@host.com?secret=MEP3EYVA6XNFNVNM&counter=1234',
			GoogleAuthenticator::getKeyUri('hotp', 'issuer:user@host.com', $secret, 1234)
		);

		// totp/hotp with an issuer and spaces in the label
		$this->assertEquals(
			'otpauth://hotp/an%20issuer%3A%20user@host.com?secret=MEP3EYVA6XNFNVNM&counter=1234',
			GoogleAuthenticator::getKeyUri('hotp', 'an issuer: user@host.com', $secret, 1234)
		);

		// totp/hotp with an issuer as option
		$this->assertEquals(
			'otpauth://hotp/an%20issuer%3Auser@host.com?secret=MEP3EYVA6XNFNVNM&counter=1234&issuer=an%20issuer',
			GoogleAuthenticator::getKeyUri('hotp', 'an issuer:user@host.com', $secret, 1234, array('issuer' => 'an issuer'))
		);
	}
	
	/**
	 * Tests generateRandom
	 */
	public function testGenerateRandom()
	{
	    // contains numbers 2-7 and letters A-Z in large letters, 16 chars long
	    $this->assertRegExp('/[2-7A-Z]{16}/', GoogleAuthenticator::generateRandom());
	
	    // Can be told to make a longer secret
	    $this->assertRegExp('/[2-7A-Z]{18}/', GoogleAuthenticator::generateRandom(18));
	}
}
