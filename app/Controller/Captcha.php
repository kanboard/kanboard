<?php

namespace Kanboard\Controller;

use Gregwar\Captcha\CaptchaBuilder;

/**
 * Captcha Controller
 *
 * @package controller
 * @author  Frederic Guillot
 */
class Captcha extends Base
{
    /**
     * Display captcha image
     *
     * @access public
     */
    public function image()
    {
        $this->response->contentType('image/jpeg');

        $builder = new CaptchaBuilder;
        $builder->build();
        $this->sessionStorage->captcha = $builder->getPhrase();
        $builder->output();
    }
}
