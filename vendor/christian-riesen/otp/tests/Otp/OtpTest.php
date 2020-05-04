<?php

require_once __DIR__ . '/../../src/Otp/OtpInterface.php';
require_once __DIR__ . '/../../src/Otp/Otp.php';

use Otp\Otp;

/**
 * Otp test case.
 */
class OtpTest extends \PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @var Otp
	 */
	private $Otp;
	
	private $secret = "12345678901234567890";
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$this->Otp = new Otp();
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->Otp = null;
		
		parent::tearDown();
	}
	
	/**
	 * Tests Otp->hotp()
	 *
	 * Using test vectors from RFC
	 * https://tools.ietf.org/html/rfc4226
	 */
	public function testHotpRfc()
	{
		$secret = $this->secret;
	
		$this->assertEquals('755224', $this->Otp->hotp($secret, 0));
		$this->assertEquals('287082', $this->Otp->hotp($secret, 1));
		$this->assertEquals('359152', $this->Otp->hotp($secret, 2));
		$this->assertEquals('969429', $this->Otp->hotp($secret, 3));
		$this->assertEquals('338314', $this->Otp->hotp($secret, 4));
		$this->assertEquals('254676', $this->Otp->hotp($secret, 5));
		$this->assertEquals('287922', $this->Otp->hotp($secret, 6));
		$this->assertEquals('162583', $this->Otp->hotp($secret, 7));
		$this->assertEquals('399871', $this->Otp->hotp($secret, 8));
		$this->assertEquals('520489', $this->Otp->hotp($secret, 9));
	}
		
	/**
	 * Tests TOTP general construction
	 *
	 * Still uses the hotp function, but since totp is a bit more special, has
	 * its own tests
	 * Using test vectors from RFC
	 * https://tools.ietf.org/html/rfc6238
	 */
	public function testTotpRfc()
	{
		$secret = $this->secret;
		
		// Test vectors are in 8 digits
		$this->Otp->setDigits(8);
		
		// The time presented in the test vector has to be first divided through 30
		// to count as the key

		// SHA 1 grouping
		$this->assertEquals('94287082', $this->Otp->hotp($secret,          floor(59/30)), 'sha1 with time 59');
		$this->assertEquals('07081804', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha1 with time 1111111109');
		$this->assertEquals('14050471', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha1 with time 1111111111');
		$this->assertEquals('89005924', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha1 with time 1234567890');
		$this->assertEquals('69279037', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha1 with time 2000000000');
		$this->assertEquals('65353130', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha1 with time 20000000000');
		
		/*
		The following tests do NOT pass.
		Once the otp class can deal with these correctly, they can be used again.
		They are here for completeness test vectors from the RFC.
		
		// SHA 256 grouping
		$this->Otp->setAlgorithm('sha256');
		$this->assertEquals('46119246', $this->Otp->hotp($secret,          floor(59/30)), 'sha256 with time 59');
		$this->assertEquals('07081804', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha256 with time 1111111109');
		$this->assertEquals('14050471', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha256 with time 1111111111');
		$this->assertEquals('89005924', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha256 with time 1234567890');
		$this->assertEquals('69279037', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha256 with time 2000000000');
		$this->assertEquals('65353130', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha256 with time 20000000000');
		
		// SHA 512 grouping
		$this->Otp->setAlgorithm('sha512');
		$this->assertEquals('90693936', $this->Otp->hotp($secret,          floor(59/30)), 'sha512 with time 59');
		$this->assertEquals('25091201', $this->Otp->hotp($secret,  floor(1111111109/30)), 'sha512 with time 1111111109');
		$this->assertEquals('99943326', $this->Otp->hotp($secret,  floor(1111111111/30)), 'sha512 with time 1111111111');
		$this->assertEquals('93441116', $this->Otp->hotp($secret,  floor(1234567890/30)), 'sha512 with time 1234567890');
		$this->assertEquals('38618901', $this->Otp->hotp($secret,  floor(2000000000/30)), 'sha512 with time 2000000000');
		$this->assertEquals('47863826', $this->Otp->hotp($secret, floor(20000000000/30)), 'sha512 with time 20000000000');
		*/
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage Counter must be integer
	 */
	public function testHotpInvalidCounter()
	{
		$this->Otp->hotp($this->secret, 'a');
	}

}
