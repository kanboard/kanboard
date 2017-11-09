<?php

namespace Kanboard\Formatter;

use Kanboard\Core\User\UserProviderInterface;
use Kanboard\Core\Filter\FormatterInterface;

/**
 * Auto-complete formatter for users
 *
 * @package  Kanboard\Formatter
 * @author   Frederic Guillot
 */
class UserAutoCompleteFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Users found
     *
     * @access protected
     * @var UserProviderInterface[]
     */
    protected $users;

    /**
     * Set users
     *
     * @access public
     * @param  UserProviderInterface[] $users
     * @return $this
     */
    public function withUsers(array $users)
    {
        $this->users = $users;
        return $this;
    }

    /**
     * Format the users for the ajax auto-completion
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $result = array();

        foreach ($this->users as $user) {
            $result[] = array(
                'id' => $user->getInternalId(),
                'username' => $user->getUsername(),
                'external_id' => $user->getExternalId(),
                'external_id_column' => $user->getExternalIdColumn(),
                'value' => $user->getName() === '' ? $user->getUsername() : $user->getName(),
                'label' => $user->getName() === '' ? $user->getUsername() : $user->getName().' ('.$user->getUsername().')',
            );
        }

        return $result;
    }
}
