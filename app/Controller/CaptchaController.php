<?php

namespace Kanboard\Controller;

use Gregwar\Captcha\CaptchaBuilder;

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
        $this->response->withContentType('image/jpeg')->send();

        $builder = new CaptchaBuilder;
        $builder->build();
        session_set('captcha', $builder->getPhrase());
        $builder->output();
    }
}
