<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Password Reset Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class PasswordResetModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'password_reset';

    /**
     * Token duration (30 minutes)
     *
     * @var integer
     */
    const DURATION = 1800;

    /**
     * Get all tokens
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getAll($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->desc('date_creation')->limit(100)->findAll();
    }

    /**
     * Generate a new reset token for a user
     *
     * @access public
     * @param  string  $username
     * @param  integer $expiration
     * @return boolean|string
     */
    public function create($username, $expiration = 0)
    {
        $user_id = $this->db->table(UserModel::TABLE)->eq('username', $username)->neq('email', '')->notNull('email')->findOneColumn('id');

        if (! $user_id) {
            return false;
        }

        $token = $this->token->getToken();

        $result = $this->db->table(self::TABLE)->insert(array(
            'token' => $token,
            'user_id' => $user_id,
            'date_expiration' => $expiration ?: time() + self::DURATION,
            'date_creation' => time(),
            'ip' => $this->request->getIpAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'is_active' => 1,
        ));

        return $result ? $token : false;
    }

    /**
     * Get user id from the token
     *
     * @access public
     * @param  string $token
     * @return integer
     */
    public function getUserIdByToken($token)
    {
        return $this->db->table(self::TABLE)->eq('token', $token)->eq('is_active', 1)->gte('date_expiration', time())->findOneColumn('user_id');
    }

    /**
     * Disable all tokens for a user
     *
     * @access public
     * @param  integer $user_id
     * @return boolean
     */
    public function disable($user_id)
    {
        return $this->db->table(self::TABLE)->eq('user_id', $user_id)->update(array('is_active' => 0));
    }
}
