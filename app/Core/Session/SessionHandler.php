<?php

namespace Kanboard\Core\Session;

use PicoDb\Database;
use SessionHandlerInterface;

/**
 * Class SessionHandler
 *
 * @package Kanboard\Core\Session
 */
class SessionHandler implements SessionHandlerInterface
{
    const TABLE = 'sessions';

    /**
     * @var Database
     */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    #[\ReturnTypeWillChange]
    public function close()
    {
        return true;
    }

    #[\ReturnTypeWillChange]
    public function destroy($sessionID)
    {
        return $this->db->table(self::TABLE)->eq('id', $sessionID)->remove();
    }

    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        return $this->db->table(self::TABLE)->lt('expire_at', time())->remove();
    }

    #[\ReturnTypeWillChange]
    public function open($savePath, $name)
    {
        return true;
    }

    #[\ReturnTypeWillChange]
    public function read($sessionID)
    {
        $result = $this->db->table(self::TABLE)->eq('id', $sessionID)->gt('expire_at', time())->findOneColumn('data');

        // Note: Returning false display an error message and write() is never called
        // preventing new sessions to be created when calling session_start()
        if (empty($result)) {
            return '';
        }

        // Sanitize session data to prevent object deserialization attacks (CWE-502).
        // Using allowed_classes: false converts any serialized objects to harmless
        // __PHP_Incomplete_Class instances, preventing exploitation via gadget chains.
        $sanitized = @unserialize($result, ['allowed_classes' => false]);

        // unserialize() returns false both on failure AND when the data legitimately
        // represents boolean false (serialized as 'b:0;'). Check the raw string to
        // distinguish a real deserialization error from a valid false value.
        if ($sanitized === false && $result !== 'b:0;') {
            // Data could not be unserialized (e.g. legacy format after handler change);
            // discard it so a fresh session is created.
            return '';
        }

        return serialize($sanitized);
    }

    #[\ReturnTypeWillChange]
    public function write($sessionID, $data)
    {
        if (SESSION_DURATION > 0) {
            $lifetime = time() + SESSION_DURATION;
        } else {
            $lifetime = time() + (ini_get('session.gc_maxlifetime') ?: 1440);
        }

        $this->db->startTransaction();

        if ($this->db->table(self::TABLE)->eq('id', $sessionID)->exists()) {
            $this->db->table(self::TABLE)->eq('id', $sessionID)->update([
                'expire_at' => $lifetime,
                'data'      => $data,
            ]);
        } else {
            $this->db->table(self::TABLE)->insert([
                'id'        => $sessionID,
                'expire_at' => $lifetime,
                'data'      => $data,
            ]);
        }

        $this->db->closeTransaction();

        return true;
    }
}
