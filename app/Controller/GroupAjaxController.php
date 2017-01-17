<?php

namespace Kanboard\Controller;

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
        $groups = $this->groupManager->find($search);
        $this->response->json($this->groupAutoCompleteFormatter->withGroups($groups)->format());
    }
}
