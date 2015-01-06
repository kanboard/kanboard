<?php

namespace Core;

class FileCache extends Cache
{
    const CACHE_FOLDER = 'data/cache/';

    public function init()
    {
        if (! is_dir(self::CACHE_FOLDER)) {
            mkdir(self::CACHE_FOLDER);
        }
    }

    public function set($key, $value)
    {
        file_put_contents(self::CACHE_FOLDER.$key, json_encode($value));
    }

    public function get($key)
    {
        if (file_exists(self::CACHE_FOLDER.$key)) {
            return json_decode(file_get_contents(self::CACHE_FOLDER.$key), true);
        }

        return null;
    }

    public function flush()
    {
        foreach (glob(self::CACHE_FOLDER.'*') as $filename) {
            @unlink($filename);
        }
    }

    public function remove($key)
    {
        @unlink(self::CACHE_FOLDER.$key);
    }
}
