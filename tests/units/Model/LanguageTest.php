<?php

use Kanboard\Model\LanguageModel;

require_once __DIR__.'/../Base.php';

class LanguageTest extends Base
{
    public function testGetCodes()
    {
        $codes = LanguageModel::getCodes();
        $this->assertContains('fr_FR', $codes);
        $this->assertContains('en_GB', $codes);
        $this->assertContains('en_US', $codes);
    }

    public function testFindCode()
    {
        $this->assertSame('', LanguageModel::findCode('xx-XX'));
        $this->assertSame('fr_FR', LanguageModel::findCode('fr-FR'));
        $this->assertSame('en_GB', LanguageModel::findCode('en-GB'));
        $this->assertSame('en_US', LanguageModel::findCode('en-US'));
    }

    public function testGetJsLanguage()
    {
        $languageModel = new LanguageModel($this->container);
        $this->assertEquals('en', $languageModel->getJsLanguageCode());

        $_SESSION['user'] = array('language' => 'fr_FR');
        $this->assertEquals('fr', $languageModel->getJsLanguageCode());

        $_SESSION['user'] = array('language' => 'xx_XX');
        $this->assertEquals('en', $languageModel->getJsLanguageCode());
    }

    public function testGetCurrentLanguage()
    {
        $languageModel = new LanguageModel($this->container);
        $this->assertEquals('en_US', $languageModel->getCurrentLanguage());

        $_SESSION['user'] = array('language' => 'en_GB');
        $this->assertEquals('en_GB', $languageModel->getCurrentLanguage());

        $_SESSION['user'] = array('language' => 'fr_FR');
        $this->assertEquals('fr_FR', $languageModel->getCurrentLanguage());

        $_SESSION['user'] = array('language' => 'xx_XX');
        $this->assertEquals('xx_XX', $languageModel->getCurrentLanguage());
    }

    public function testGetLanguages()
    {
        $languageModel = new LanguageModel($this->container);
        $this->assertNotEmpty($languageModel->getLanguages());
        $this->assertArrayHasKey('fr_FR', $languageModel->getLanguages());
        $this->assertContains('Français', $languageModel->getLanguages());
        $this->assertArrayNotHasKey('', $languageModel->getLanguages());

        $this->assertArrayHasKey('', $languageModel->getLanguages(true));
        $this->assertContains('Application default', $languageModel->getLanguages(true));
    }
}
