<?php

namespace Kanboard\Controller;

use Kanboard\Formatter\GroupAutoCompleteFormatter;

/**
 * Group Ajax Controller
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class GroupAjaxController extends BaseController
{
    /**
     * Group auto-completion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $formatter = new GroupAutoCompleteFormatter($this->groupManager->find($search));
        $this->response->json($formatter->format());
    }
}
