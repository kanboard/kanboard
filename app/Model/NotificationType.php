<?php

namespace Model;

/**
 * Notification Type model
 *
 * @package  model
 * @author   Frederic Guillot
 */
class NotificationType extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'user_has_notification_types';

    /**
     * Types
     *
     * @var string
     */
    const TYPE_WEB = 'web';
    const TYPE_EMAIL = 'email';

    /**
     * Get all notification types
     *
     * @access public
     * @return array
     */
    public function getTypes()
    {
        return array(
            self::TYPE_EMAIL => t('Email'),
            self::TYPE_WEB => t('Web'),
        );
    }

    /**
     * Get selected notification types for a given user
     *
     * @access public
     * @param integer  $user_id
     * @return array
     */
    public function getUserSelectedTypes($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->asc('notification_type')->findAllByColumn('notification_type');
    }

    /**
     * Save notification types for a given user
     *
     * @access public
     * @param  integer  $user_id
     * @param  string[] $types
     * @return boolean
     */
    public function saveUserSelectedTypes($user_id, array $types)
    {
        $results = array();
        $this->db->table(self::TABLE)->eq('user_id', $user_id)->remove();

        foreach ($types as $type) {
            $results[] = $this->db->table(self::TABLE)->insert(array('user_id' => $user_id, 'notification_type' => $type));
        }

        return ! in_array(false, $results);
    }
}
