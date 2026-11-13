<?php

namespace KanboardTests\units\Core\Security;

use KanboardTests\units\Base;
use Kanboard\Core\Security\Captcha;

class CaptchaTest extends Base
{
    public function testGeneratePhrase()
    {
        $captcha = new Captcha;

        $phrase = $captcha->generatePhrase();
        $this->assertSame(5, strlen($phrase));
        $this->assertSame(strlen($phrase), strspn($phrase, Captcha::CHARSET));

        $this->assertSame(8, strlen($captcha->generatePhrase(8)));
        $this->assertNotSame($captcha->generatePhrase(32), $captcha->generatePhrase(32));
    }

    public function testComparePhrases()
    {
        $captcha = new Captcha;

        $this->assertTrue($captcha->comparePhrases('abc12', 'abc12'));
        $this->assertTrue($captcha->comparePhrases('AbC', 'abc'));
        $this->assertTrue($captcha->comparePhrases('abc', 'ABC'));
        $this->assertTrue($captcha->comparePhrases('Oz', '0z'));
        $this->assertTrue($captcha->comparePhrases('l1', '1l'));

        $this->assertFalse($captcha->comparePhrases('abc', 'abd'));
        $this->assertFalse($captcha->comparePhrases('abc', 'abcd'));
        $this->assertFalse($captcha->comparePhrases('abc', ''));
        $this->assertFalse($captcha->comparePhrases('', ''));
    }

    public function testRender()
    {
        $captcha = new Captcha;
        $data = $captcha->render($captcha->generatePhrase());

        $this->assertStringStartsWith("\xFF\xD8", $data);

        $info = getimagesizefromstring($data);
        $this->assertSame(150, $info[0]);
        $this->assertSame(40, $info[1]);
        $this->assertSame('image/jpeg', $info['mime']);
    }

    public function testRenderWithCustomSize()
    {
        $captcha = new Captcha(200, 60);
        $info = getimagesizefromstring($captcha->render('abcde'));

        $this->assertSame(200, $info[0]);
        $this->assertSame(60, $info[1]);
    }

    public function testRenderWithEmptyPhrase()
    {
        $info = getimagesizefromstring((new Captcha)->render(''));

        $this->assertSame(150, $info[0]);
        $this->assertSame(40, $info[1]);
    }
}
