<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Xml;

use ZendXml\Security as XmlSecurity;
use ZendXml\Exception;
use DOMDocument;
use ReflectionMethod;
use SimpleXMLElement;

/**
 * @group ZF2015-06
 */
class MultibyteTest extends \PHPUnit_Framework_TestCase
{
    public function multibyteEncodings()
    {
        return array(
            'UTF-16LE' => array('UTF-16LE', pack('CC', 0xff, 0xfe), 3),
            'UTF-16BE' => array('UTF-16BE', pack('CC', 0xfe, 0xff), 3),
            'UTF-32LE' => array('UTF-32LE', pack('CCCC', 0xff, 0xfe, 0x00, 0x00), 4),
            'UTF-32BE' => array('UTF-32BE', pack('CCCC', 0x00, 0x00, 0xfe, 0xff), 4),
        );
    }

    public function getXmlWithXXE()
    {
        return <<<XML
<?xml version="1.0" encoding="{ENCODING}"?>
<!DOCTYPE methodCall [
  <!ENTITY pocdata SYSTEM "file:///etc/passwd">
]>
<methodCall>
    <methodName>retrieved: &pocdata;</methodName>
</methodCall>
XML;
    }

    /**
     * Invoke ZendXml\Security::heuristicScan with the provided XML.
     *
     * @param string $xml
     * @return void
     * @throws Exception\RuntimeException
     */
    public function invokeHeuristicScan($xml)
    {
        $r = new ReflectionMethod('ZendXml\Security', 'heuristicScan');
        $r->setAccessible(true);
        return $r->invoke(null, $xml);
    }

    /**
     * @dataProvider multibyteEncodings
     * @group heuristicDetection
     */
    public function testDetectsMultibyteXXEVectorsUnderFPMWithEncodedStringMissingBOM($encoding, $bom, $bomLength)
    {
        $xml = $this->getXmlWithXXE();
        $xml = str_replace('{ENCODING}', $encoding, $xml);
        $xml = iconv('UTF-8', $encoding, $xml);
        $this->assertNotSame(0, strncmp($xml, $bom, $bomLength));
        $this->setExpectedException('ZendXml\Exception\RuntimeException', 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }

    /**
     * @dataProvider multibyteEncodings
     */
    public function testDetectsMultibyteXXEVectorsUnderFPMWithEncodedStringUsingBOM($encoding, $bom)
    {
        $xml  = $this->getXmlWithXXE();
        $xml  = str_replace('{ENCODING}', $encoding, $xml);
        $orig = iconv('UTF-8', $encoding, $xml);
        $xml  = $bom . $orig;
        $this->setExpectedException('ZendXml\Exception\RuntimeException', 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }

    public function getXmlWithoutXXE()
    {
        return <<<XML
<?xml version="1.0" encoding="{ENCODING}"?>
<methodCall>
    <methodName>retrieved: &pocdata;</methodName>
</methodCall>
XML;
    }

    /**
     * @dataProvider multibyteEncodings
     */
    public function testDoesNotFlagValidMultibyteXmlAsInvalidUnderFPM($encoding)
    {
        $xml = $this->getXmlWithoutXXE();
        $xml = str_replace('{ENCODING}', $encoding, $xml);
        $xml = iconv('UTF-8', $encoding, $xml);
        try {
            $result = $this->invokeHeuristicScan($xml);
            $this->assertNull($result);
        } catch (\Exception $e) {
            $this->fail('Security scan raised exception when it should not have');
        }
    }

    /**
     * @dataProvider multibyteEncodings
     * @group mixedEncoding
     */
    public function testDetectsXXEWhenXMLDocumentEncodingDiffersFromFileEncoding($encoding, $bom)
    {
        $xml = $this->getXmlWithXXE();
        $xml = str_replace('{ENCODING}', 'UTF-8', $xml);
        $xml = iconv('UTF-8', $encoding, $xml);
        $xml = $bom . $xml;
        $this->setExpectedException('ZendXml\Exception\RuntimeException', 'ENTITY');
        $this->invokeHeuristicScan($xml);
    }
}
