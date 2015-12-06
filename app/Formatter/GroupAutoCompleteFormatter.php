<?php

namespace Kanboard\Formatter;

/**
 * Autocomplete formatter for groups
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class GroupAutoCompleteFormatter implements FormatterInterface
{
    /**
     * Groups found
     *
     * @access private
     * @var array
     */
    private $groups;

    /**
     * Format groups for the ajax autocompletion
     *
     * @access public
     * @param  array $groups
     * @return GroupAutoCompleteFormatter
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * Format groups for the ajax autocompletion
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
