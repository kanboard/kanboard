<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Token;

/**
 * Remember Me Model
 *
 * @package  Kanboard\Model
 * @author   Frederic Guillot
 */
class RememberMeSessionModel extends Base
{
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'remember_me';

    /**
     * Expiration (60 days)
     *
     * @var integer
     */
    const EXPIRATION = 5184000;

    /**
     * Get a remember me record
     *
     * @access public
     * @param $token
     * @param $sequence
     * @return mixed
     */
    public function find($token, $sequence)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('token', $token)
            ->eq('sequence', $sequence)
            ->gt('expiration', time())
            ->findOne();
    }

    /**
     * Get all sessions for a given user
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
            ->desc('date_creation')
            ->columns('id', 'ip', 'user_agent', 'date_creation', 'expiration')
            ->findAll();
    }

    /**
     * Create a new RememberMe session
     *
     * @access public
     * @param  integer  $user_id     User id
     * @param  string   $ip          IP Address
     * @param  string   $user_agent  User Agent
     * @return array
     */
    public function create($user_id, $ip, $user_agent)
    {
        $token = hash('sha256', $user_id.$user_agent.$ip.Token::getToken());
        $sequence = Token::getToken();
        $expiration = time() + self::EXPIRATION;

        $this->cleanup($user_id);

        $this
            ->db
            ->table(self::TABLE)
            ->insert(array(
                'user_id' => $user_id,
                'ip' => $ip,
                'user_agent' => substr($user_agent, 0, 255),
                'token' => $token,
                'sequence' => $sequence,
                'expiration' => $expiration,
                'date_creation' => time(),
            ));

        return array(
            'token' => $token,
            'sequence' => $sequence,
            'expiration' => $expiration,
        );
    }

    /**
     * Remove a session record
     *
     * @access public
     * @param  integer  $session_id   Session id
     * @return mixed
     */
    public function remove($session_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('id', $session_id)
            ->remove();
    }

    /**
     * Remove old sessions for a given user
     *
     * @access public
     * @param  integer  $user_id  User id
     * @return bool
     */
    public function cleanup($user_id)
    {
        return $this->db
            ->table(self::TABLE)
            ->eq('user_id', $user_id)
            ->lt('expiration', time())
            ->remove();
    }

    /**
     * Return a new sequence token and update the database
     *
     * @access public
     * @param  string   $token        Session token
     * @return string
     */
    public function updateSequence($token)
    {
        $sequence = Token::getToken();

        $this
            ->db
            ->table(self::TABLE)
            ->eq('token', $token)
            ->update(array('sequence' => $sequence));

        return $sequence;
    }
}
