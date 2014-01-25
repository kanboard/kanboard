<?php

class Session
{
    const SESSION_LIFETIME = 2678400;

    public function open($base_path = '/')
    {
        session_set_cookie_params(
            self::SESSION_LIFETIME,
            $base_path,
            null,
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            true
        );

        session_start();
    }

    public function close()
    {
        session_destroy();
    }

    public function flash($message)
    {
        $_SESSION['flash_message'] = $message;
    }

    public function flashError($message)
    {
        $_SESSION['flash_error_message'] = $message;
    }
}
