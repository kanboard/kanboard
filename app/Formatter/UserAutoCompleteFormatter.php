<?php

namespace Kanboard\Formatter;

use Kanboard\Model\UserModel;
use Kanboard\Core\Filter\FormatterInterface;

/**
 * Auto-complete formatter for user filter
 *
 * @package  formatter
 * @author   Frederic Guillot
 */
class UserAutoCompleteFormatter extends BaseFormatter implements FormatterInterface
{
    /**
     * Format the tasks for the ajax auto-completion
     *
     * @access public
     * @return array
     */
    public function format()
    {
        $users = $this->query->columns(UserModel::TABLE.'.id', UserModel::TABLE.'.username', UserModel::TABLE.'.name')->findAll();

        foreach ($users as &$user) {
            if (empty($user['name'])) {
                $user['value'] = $user['username'].' (#'.$user['id'].')';
                $user['label'] = $user['username'];
            } else {
                $user['value'] = $user['name'].' (#'.$user['id'].')';
                $user['label'] = $user['name'].' ('.$user['username'].')';
            }
        }

        return $users;
    }
}
