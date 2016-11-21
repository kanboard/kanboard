<?php

require_once __DIR__.'/../Base.php';

use Kanboard\Core\Translator;

class TranslatorTest extends Base
{
    public function setUp()
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
}
