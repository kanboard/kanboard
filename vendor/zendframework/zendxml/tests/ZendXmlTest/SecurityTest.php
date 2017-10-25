<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Xml;

use ZendXml\Security as XmlSecurity;
use ZendXml\Exception;
use DOMDocument;
use SimpleXMLElement;

class SecurityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException ZendXml\Exception\RuntimeException
     */
    public function testScanForXEE()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<!DOCTYPE results [<!ENTITY harmless "completely harmless">]>
<results>
    <result>This result is &harmless;</result>
</results>
XML;

        $this->setExpectedException('ZendXml\Exception\RuntimeException');
        $result = XmlSecurity::scan($xml);
    }

    public function testScanForXXE()
    {
        $file = tempnam(sys_get_temp_dir(), 'ZendXml_Security');
        file_put_contents($file, 'This is a remote content!');
        $xml = <<<XML
<?xml version="1.0"?>
<!DOCTYPE root
[
<!ENTITY foo SYSTEM "file://$file">
]>
<results>
    <result>&foo;</result>
</results>
XML;

        try {
            $result = XmlSecurity::scan($xml);
        } catch (Exception\RuntimeException $e) {
            unlink($file);
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testScanSimpleXmlResult()
    {
        $result = XmlSecurity::scan($this->getXml());
        $this->assertTrue($result instanceof SimpleXMLElement);
        $this->assertEquals($result->result, 'test');
    }

    public function testScanDom()
    {
        $dom = new DOMDocument('1.0');
        $result = XmlSecurity::scan($this->getXml(), $dom);
        $this->assertTrue($result instanceof DOMDocument);
        $node = $result->getElementsByTagName('result')->item(0);
        $this->assertEquals($node->nodeValue, 'test');
    }

    public function testScanInvalidXml()
    {
        $xml = <<<XML
<foo>test</bar>
XML;

        $result = XmlSecurity::scan($xml);
        $this->assertFalse($result);
    }

    public function testScanInvalidXmlDom()
    {
        $xml = <<<XML
<foo>test</bar>
XML;

        $dom = new DOMDocument('1.0');
        $result = XmlSecurity::scan($xml, $dom);
        $this->assertFalse($result);
    }

    public function testScanFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'ZendXml_Security');
        file_put_contents($file, $this->getXml());

        $result = XmlSecurity::scanFile($file);
        $this->assertTrue($result instanceof SimpleXMLElement);
        $this->assertEquals($result->result, 'test');
        unlink($file);
    }

    public function testScanXmlWithDTD()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<!DOCTYPE results [
<!ELEMENT results (result+)>
<!ELEMENT result (#PCDATA)>
]>
<results>
    <result>test</result>
</results>
XML;

        $dom = new DOMDocument('1.0');
        $result = XmlSecurity::scan($xml, $dom);
        $this->assertTrue($result instanceof DOMDocument);
        $this->assertTrue($result->validate());
    }

    protected function getXml()
    {
        return <<<XML
<?xml version="1.0"?>
<results>
    <result>test</result>
</results>
XML;
    }
}
