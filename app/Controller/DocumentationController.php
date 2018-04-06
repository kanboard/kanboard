<?php

namespace Kanboard\Controller;

use Parsedown;

/**
 * Documentation Viewer
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class DocumentationController extends BaseController
{
    public function shortcuts()
    {
        $this->response->html($this->template->render('config/keyboard_shortcuts'));
    }
}
