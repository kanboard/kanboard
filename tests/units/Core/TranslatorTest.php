<?php

namespace KanboardTests\units\Core;

use KanboardTests\units\Base;
use Kanboard\Core\Translator;

class TranslatorTest extends Base
{
    protected function setUp(): void
    {
        parent::setUp();
        Translator::unload();
    }

    public function testLoading()
    {
        $translator = new Translator();
        $this->assertSame('Yes', $translator->translate('Yes'));

        Translator::load('fr_FR');
        $this->assertSame('Oui', $translator->translate('Yes'));

        Translator::unload();
        $this->assertSame('Yes', $translator->translate('Yes'));

        Translator::load('de_DE', Translator::getDefaultFolder());
        $this->assertSame('Ja', $translator->translate('Yes'));
    }

    public function testNumberFormatting()
    {
        $translator = new Translator();
        $this->assertSame('1,024.42', $translator->number(1024.42));

        Translator::load('fr_FR');
        $this->assertSame('1 024,42', $translator->number(1024.42));
    }

    public function testTranslateEscaping()
    {
        $translator = new Translator();
        $this->assertSame('&lt;b&gt;', $translator->translate('<b>'));
        $this->assertSame('<b>', $translator->translateNoEscaping('<b>'));
    }

    public function testLoadingAllLanguages()
    {
        $languageCodes = array_map(function ($value) {
            return basename($value);
        }, glob(Translator::getDefaultFolder().DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR));

        foreach ($languageCodes as $languageCode) {
            $this->assertTrue(Translator::load($languageCode), "Unable to load translation for $languageCode");
        }

        Translator::unload();
    }
}
