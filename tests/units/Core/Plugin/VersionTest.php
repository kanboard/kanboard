<?php

use Kanboard\Core\Plugin\Version;

require_once __DIR__.'/../../Base.php';

class VersionTest extends Base
{
    public function testIsCompatible()
    {
        $this->assertFalse(Version::isCompatible('1.0.29', '1.0.28'));
        $this->assertTrue(Version::isCompatible('1.0.28', '1.0.28'));
        $this->assertTrue(Version::isCompatible('1.0.28', 'master.1234'));
        $this->assertTrue(Version::isCompatible('>=1.0.32', 'master'));
        $this->assertTrue(Version::isCompatible('>=1.0.32', '1.0.32'));
        $this->assertTrue(Version::isCompatible('>=1.0.32', '1.0.33'));
        $this->assertTrue(Version::isCompatible('>1.0.32', '1.0.33'));
        $this->assertFalse(Version::isCompatible('>1.0.32', '1.0.32'));
        $this->assertTrue(Version::isCompatible('1.0.32', 'v1.0.32'));
        $this->assertTrue(Version::isCompatible('>=v1.0.32', 'v1.0.32'));
        $this->assertTrue(Version::isCompatible('<=v1.0.36', 'v1.0.36'));
        $this->assertFalse(Version::isCompatible('<1.0.36', 'v1.0.36'));
        $this->assertTrue(Version::isCompatible('<1.0.40', '1.0.36'));
        $this->assertTrue(Version::isCompatible('<=1.0.40', '1.0.36'));
        $this->assertFalse(Version::isCompatible('<1.0.40', '1.0.40'));
        $this->assertFalse(Version::isCompatible('1.0.40', 'v1.0.36'));
        $this->assertTrue(Version::isCompatible('<1.1.0', 'v1.0.36'));
    }
}
