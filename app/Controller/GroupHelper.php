<?php

namespace Kanboard\Controller;

use Kanboard\Formatter\GroupAutoCompleteFormatter;

/**
 * Group Helper
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class GroupHelper extends Base
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
