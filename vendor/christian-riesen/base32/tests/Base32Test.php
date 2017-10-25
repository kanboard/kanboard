<?php

namespace Base32;

use Base32\Base32;

/**
 * Base32 test case.
 */
class Base32Test extends \PHPUnit_Framework_TestCase
{	
	/**
	 * Tests Base32->decode()
	 *
	 * Testing test vectors according to RFC 4648
	 * http://www.ietf.org/rfc/rfc4648.txt
	 */
	public function testDecode()
	{
		// RFC test vectors say that empty string returns empty string
		$this->assertEquals('', Base32::decode(''));
		
		// these strings are taken from the RFC
		$this->assertEquals('f',      Base32::decode('MY======'));
		$this->assertEquals('fo',     Base32::decode('MZXQ===='));
		$this->assertEquals('foo',    Base32::decode('MZXW6==='));
		$this->assertEquals('foob',   Base32::decode('MZXW6YQ='));
		$this->assertEquals('fooba',  Base32::decode('MZXW6YTB'));
		$this->assertEquals('foobar', Base32::decode('MZXW6YTBOI======'));

		// Decoding a string made up entirely of invalid characters
		$this->assertEquals('', Base32::decode('8908908908908908'));
	}
	
	/**
	 * Encoder tests, reverse of the decodes
	 */
	public function testEncode()
	{
		// RFC test vectors say that empty string returns empty string
		$this->assertEquals('', Base32::encode(''));
		
		// these strings are taken from the RFC
		$this->assertEquals('MY======',         Base32::encode('f'));
		$this->assertEquals('MZXQ====',         Base32::encode('fo'));
		$this->assertEquals('MZXW6===',         Base32::encode('foo'));
		$this->assertEquals('MZXW6YQ=',         Base32::encode('foob'));
		$this->assertEquals('MZXW6YTB',         Base32::encode('fooba'));
		$this->assertEquals('MZXW6YTBOI======', Base32::encode('foobar'));
	}
}
