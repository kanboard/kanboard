<?php

namespace Kanboard\Formatter;

use Kanboard\Core\Filter\FormatterInterface;
use Kanboard\Core\Group\GroupProviderInterface;
use PicoDb\Table;

/**
 * Auto-complete formatter for groups
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class GroupAutoCompleteFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Groups found
     *
     * @access protected
     * @var GroupProviderInterface[]
     */
    protected $groups;

    /**
     * Set groups
     *
     * @access public
     * @param  GroupProviderInterface[] $groups
     * @return $this
     */
    public function withGroups(array $groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * Format groups for the ajax auto-completion
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $result = array();

        foreach ($this->groups as $group) {
            $result[] = array(
                'id' => $group->getInternalId(),
                'external_id' => $group->getExternalId(),
                'value' => $group->getName(),
                'label' => $group->getName(),
            );
        }

        return $result;
    }
}
