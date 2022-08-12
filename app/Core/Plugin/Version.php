<?php

namespace Kanboard\Core\Plugin;

/**
 * Class Version
 *
 * @package Kanboard\Core\Plugin
 * @author  Frederic Guillot
 */
class Version
{
    /**
     * Check plugin version compatibility with application version
     *
     * @param  string $pluginCompatibleVersion
     * @param  string $appVersion
     * @return bool
     */
    public static function isCompatible($pluginCompatibleVersion, $appVersion = APP_VERSION)
    {
        if (strpos($appVersion, 'master') !== false || strpos($appVersion, 'main') !== false) {
            return true;
        }

        $appVersion = str_replace('v', '', $appVersion);
        $pluginCompatibleVersion = str_replace('v', '', $pluginCompatibleVersion);

        foreach (array('>=', '>', '<=', '<') as $operator) {
            if (strpos($pluginCompatibleVersion, $operator) === 0) {
                $pluginVersion = substr($pluginCompatibleVersion, strlen($operator));
                return version_compare($appVersion, $pluginVersion, $operator);
            }
        }

        return $pluginCompatibleVersion === $appVersion;
    }
}
