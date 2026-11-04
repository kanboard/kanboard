<?php

namespace Kanboard\Filter;

use Kanboard\Core\Filter\FilterInterface;
use Kanboard\Model\UserModel;

class UserNameFilter extends BaseFilter implements FilterInterface
{
    /**
     * Get search attribute
     *
     * @access public
     * @return string[]
     */
    public function getAttributes()
    {
        return array('name');
    }

    /**
     * Apply filter
     *
     * @access public
     * @return FilterInterface
     */
    public function apply()
    {
        $this->query->beginOr()
            ->ilike('username', '%'.$this->value.'%')
            ->ilike('name', '%'.$this->value.'%');

        // Email addresses are matched exactly to avoid exposing them through partial searches
        if (strpos($this->value, '@') !== false) {
            $this->query->eq('LOWER('.UserModel::TABLE.'.email)', strtolower($this->value));
        }

        $this->query->closeOr();

        return $this;
    }
}
