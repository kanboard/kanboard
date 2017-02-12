<?php

namespace Kanboard\ServiceProvider;

use Kanboard\Core\ObjectStorage\FileStorage;
use LogicException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ObjectStorageProvider
 *
 * @package Kanboard\ServiceProvider
 * @author  Frederic Guillot
 */
class ObjectStorageProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['objectStorage'] = function () {
            if (file_exists(FILES_DIR)) {
                if (! is_writable(FILES_DIR)) {
                    $stat = stat(FILES_DIR);

                    throw new LogicException(sprintf(
                        'The folder to store uploaded files is not writeable by your webserver user (file=%s; mode=%o; uid=%d; gid=%d)',
                        FILES_DIR,
                        $stat['mode'],
                        $stat['uid'],
                        $stat['gid']
                    ));
                }
            } elseif (! @mkdir(FILES_DIR)) {
                $folder = dirname(FILES_DIR);
                $stat = stat($folder);

                throw new LogicException(sprintf(
                    'Unable to create folder to store uploaded files, check the permissions of the parent directory (file=%s; mode=%o; uid=%d; gid=%d)',
                    $folder,
                    $stat['mode'],
                    $stat['uid'],
                    $stat['gid']
                ));
            }

            return new FileStorage(FILES_DIR);
        };

        return $container;
    }
}