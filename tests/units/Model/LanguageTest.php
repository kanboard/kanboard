<?php

use Kanboard\Model\Language;

require_once __DIR__.'/../Base.php';

class LanguageTest extends Base
{
    public function testGetCodes()
    {
        $codes = Language::getCodes();
        $this->assertContains('fr_FR', $codes);
        $this->assertContains('en_US', $codes);
    }

    public function testFindCode()
    {
        $this->assertSame('', Language::findCode('xx-XX'));
        $this->assertSame('fr_FR', Language::findCode('fr-FR'));
        $this->assertSame('en_US', Language::findCode('en-US'));
    }

    public function testGetJsLanguage()
    {
        $languageModel = new Language($this->container);
        $this->assertEquals('en', $languageModel->getJsLanguageCode());

        $this->container['sessionStorage']->user = array('language' => 'fr_FR');
        $this->assertEquals('fr', $languageModel->getJsLanguageCode());

        $this->container['sessionStorage']->user = array('language' => 'xx_XX');
        $this->assertEquals('en', $languageModel->getJsLanguageCode());
    }

    public function testGetCurrentLanguage()
    {
        $languageModel = new Language($this->container);
        $this->assertEquals('en_US', $languageModel->getCurrentLanguage());

        $this->container['sessionStorage']->user = array('language' => 'fr_FR');
        $this->assertEquals('fr_FR', $languageModel->getCurrentLanguage());

        $this->container['sessionStorage']->user = array('language' => 'xx_XX');
        $this->assertEquals('xx_XX', $languageModel->getCurrentLanguage());
    }

    public function testGetLanguages()
    {
        $languageModel = new Language($this->container);
        $this->assertNotEmpty($languageModel->getLanguages());
        $this->assertArrayHasKey('fr_FR', $languageModel->getLanguages());
        $this->assertContains('Français', $languageModel->getLanguages());
        $this->assertArrayNotHasKey('', $languageModel->getLanguages());

        $this->assertArrayHasKey('', $languageModel->getLanguages(true));
        $this->assertContains('Application default', $languageModel->getLanguages(true));
    }
}
