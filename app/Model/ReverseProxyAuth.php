<?php

namespace Model;

use Core\Security;

/**
 * ReverseProxyAuth model
 *
 * @package  model
 * @author   Sylvain Veyrié
 */
class ReverseProxyAuth extends Base
{
    /**
     * Authenticate the user with the HTTP header
     *
     * @access public
     * @return bool
     */
    public function authenticate()
    {
        if(isset($_SERVER[REVERSE_PROXY_USER_HEADER])) {

            $login = $_SERVER[REVERSE_PROXY_USER_HEADER];
            $userModel = new User($this->db, $this->event);
            $user = $userModel->getByUsername($login);
        
            if (! $user) {
                $this->createUser($login);
                $user = $userModel->getByUsername($login);
            }

            // Create the user session
            $userModel->updateSession($user);

            // Update login history
            $lastLogin = new LastLogin($this->db, $this->event);
            $lastLogin->create(
                LastLogin::AUTH_REVERSE_PROXY,
                $user['id'],
                $userModel->getIpAddress(),
                $userModel->getUserAgent()
            );
            return true;
        }
        return false;
    }

    private function createUser($login)
    {
        $userModel = new User($this->db, $this->event);

        $is_admin = REVERSE_PROXY_DEFAULT_ADMIN === $login;

        return $userModel->create(array(
            'email' => $login,
            'username' => $login,
            'is_admin' => $is_admin,
        ));
    }
}
