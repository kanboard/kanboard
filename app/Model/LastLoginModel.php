<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * LastLogin model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class LastLoginModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'last_logins';

    /**
     * Number of connections to keep for history
     *
     * @var integer
     */
    const NB_LOGINS = 10;

    /**
     * Create a new record
     *
     * @access public
     * @param  string   $auth_type   Authentication method
     * @param  integer  $user_id     User id
     * @param  string   $ip          IP Address
     * @param  string   $user_agent  User Agent
     * @return boolean
     */
    public function create($auth_type, $user_id, $ip, $user_agent)
    {
        $this->cleanup($user_id);

        return $this->db
            ->table(self::TABLE)
            ->insert(array(
                'auth_type' => $auth_type,
                'user_id' => $user_id,
                'ip' => $ip,
                'user_agent' => substr($user_agent, 0, 255),
                'date_creation' => time(),
            ));
    }

    /**
     * Cleanup login history
     *
     * @access public
     * @param  integer $user_id
     */
    public function cleanup($user_id)
    {
        $connections = $this->db
                            ->table(self::TABLE)
                            ->eq('user_id', $user_id)
                            ->desc('id')
                            ->findAllByColumn('id');

        if (count($connections) >= self::NB_LOGINS) {
            $this->db->table(self::TABLE)
                ->eq('user_id', $user_id)
                ->notIn('id', array_slice($connections, 0, self::NB_LOGINS - 1))
                ->remove();
        }
    }

    /**
     * Get the last connections for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return array
     */
    public function getAll($user_id)
    {
        return $this->db
                    ->table(self::TABLE)
                    ->eq('user_id', $user_id)
                    ->desc('id')
                    ->columns('id', 'auth_type', 'ip', 'user_agent', 'date_creation')
                    ->findAll();
    }
}
