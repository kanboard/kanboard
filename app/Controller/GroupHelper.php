<?php

namespace Kanboard\Controller;

/**
 * Group Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class GroupHelper extends Base
{
    /**
     * Group autocompletion (Ajax)
     *
     * @access public
     */
    public function autocomplete()
    {
        $search = $this->request->getStringParam('term');
        $groups = $this->groupManager->find($search);
        $this->response->json($this->groupAutoCompleteFormatter->setGroups($groups)->format());
    }
}
