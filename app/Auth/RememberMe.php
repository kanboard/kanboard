<?php

namespace Auth;

use Core\Security;
use Core\Tool;

/**
 * RememberMe model
 *
 * @package  auth
 * @author   Frederic Guillot
 */
class RememberMe extends Base
{
    /**
     * Backend name
     *
     * @var string
     */
    const AUTH_NAME = 'RememberMe';

    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'remember_me';

    /**
     * Cookie name
     *
     * @var string
     */
    const COOKIE_NAME = '__R';

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
     * Authenticate the user with the cookie
     *
     * @access public
     * @return bool
     */
    public function authenticate()
    {
        $credentials = $this->readCookie();

        if ($credentials !== false) {

            $record = $this->find($credentials['token'], $credentials['sequence']);

            if ($record) {

                // Update the sequence
                $this->writeCookie(
                    $record['token'],
                    $this->update($record['token'], $record['sequence']),
                    $record['expiration']
                );

                // Create the session
                $this->user->updateSession($this->user->getById($record['user_id']));
                $this->acl->isRememberMe(true);

                // Update last login infos
                $this->lastLogin->create(
                    self::AUTH_NAME,
                    $this->acl->getUserId(),
                    $this->user->getIpAddress(),
                    $this->user->getUserAgent()
                );

                return true;
            }
        }

        return false;
    }

    /**
     * Update the database and the cookie with a new sequence
     *
     * @access public
     */
    public function refresh()
    {
        $credentials = $this->readCookie();

        if ($credentials !== false) {

            $record = $this->find($credentials['token'], $credentials['sequence']);

            if ($record) {

                // Update the sequence
                $this->writeCookie(
                    $record['token'],
                    $this->update($record['token'], $record['sequence']),
                    $record['expiration']
                );
            }
        }
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
     * Remove the current RememberMe session and the cookie
     *
     * @access public
     * @param  integer  $user_id  User id
     */
    public function destroy($user_id)
    {
        $credentials = $this->readCookie();

        if ($credentials !== false) {

            $this->deleteCookie();

            $this->db
                 ->table(self::TABLE)
                 ->eq('user_id', $user_id)
                 ->eq('token', $credentials['token'])
                 ->remove();
        }
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
        $token = hash('sha256', $user_id.$user_agent.$ip.Security::generateToken());
        $sequence = Security::generateToken();
        $expiration = time() + self::EXPIRATION;

        $this->cleanup($user_id);

        $this->db
             ->table(self::TABLE)
             ->insert(array(
                'user_id' => $user_id,
                'ip' => $ip,
                'user_agent' => $user_agent,
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
     * @param  string   $sequence     Sequence token
     * @return string
     */
    public function update($token, $sequence)
    {
        $new_sequence = Security::generateToken();

        $this->db
             ->table(self::TABLE)
             ->eq('token', $token)
             ->eq('sequence', $sequence)
             ->update(array('sequence' => $new_sequence));

        return $new_sequence;
    }

    /**
     * Encode the cookie
     *
     * @access public
     * @param  string   $token        Session token
     * @param  string   $sequence     Sequence token
     * @return string
     */
    public function encodeCookie($token, $sequence)
    {
        return implode('|', array($token, $sequence));
    }

    /**
     * Decode the value of a cookie
     *
     * @access public
     * @param  string   $value    Raw cookie data
     * @return array
     */
    public function decodeCookie($value)
    {
        list($token, $sequence) = explode('|', $value);

        return array(
            'token' => $token,
            'sequence' => $sequence,
        );
    }

    /**
     * Return true if the current user has a RememberMe cookie
     *
     * @access public
     * @return bool
     */
    public function hasCookie()
    {
        return ! empty($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Write and encode the cookie
     *
     * @access public
     * @param  string   $token        Session token
     * @param  string   $sequence     Sequence token
     * @param  string   $expiration   Cookie expiration
     */
    public function writeCookie($token, $sequence, $expiration)
    {
        setcookie(
            self::COOKIE_NAME,
            $this->encodeCookie($token, $sequence),
            $expiration,
            BASE_URL_DIRECTORY,
            null,
            Tool::isHTTPS(),
            true
        );
    }

    /**
     * Read and decode the cookie
     *
     * @access public
     * @return mixed
     */
    public function readCookie()
    {
        if (empty($_COOKIE[self::COOKIE_NAME])) {
            return false;
        }

        return $this->decodeCookie($_COOKIE[self::COOKIE_NAME]);
    }

    /**
     * Remove the cookie
     *
     * @access public
     */
    public function deleteCookie()
    {
        setcookie(
            self::COOKIE_NAME,
            '',
            time() - 3600,
            BASE_URL_DIRECTORY,
            null,
            Tool::isHTTPS(),
            true
        );
    }
}
