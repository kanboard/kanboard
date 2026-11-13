<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\Captcha;

/**
 * Captcha Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class CaptchaController extends BaseController
{
    /**
     * Display captcha image
     *
     * @access public
     */
    public function image()
    {
        $captcha = new Captcha;
        $phrase = $captcha->generatePhrase();
        session_set('captcha', $phrase);

        $this->response
            ->withContentType('image/jpeg')
            ->withoutCache()
            ->withBody($captcha->render($phrase))
            ->send();
    }
}
