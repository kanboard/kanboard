<?php

namespace Kanboard\Controller;

use Kanboard\Core\ObjectStorage\ObjectStorageException;
use Kanboard\Core\Thumbnail;

/**
 * Avatar File Controller
 *
 * @package  controller
 * @author   Frederic Guillot
 */
class AvatarFile extends Base
{
    /**
     * Show Avatar image and send aggressive caching headers
     */
    public function show()
    {
        $user_id = $this->request->getIntegerParam('user_id');
        $size = $this->request->getStringParam('size', 48);
        $filename = $this->avatarFile->getFilename($user_id);
        $etag = md5($filename.$size);

        $this->response->cache(365 * 86400, $etag);
        $this->response->contentType('image/jpeg');

        if ($this->request->getHeader('If-None-Match') !== '"'.$etag.'"') {
            $this->render($filename, $size);
        } else {
            $this->response->status(304);
        }
    }

    /**
     * Render thumbnail from object storage
     *
     * @access private
     * @param  string  $filename
     * @param  integer $size
     */
    private function render($filename, $size)
    {
        try {
            $blob = $this->objectStorage->get($filename);

            Thumbnail::createFromString($blob)
                ->resize($size, $size)
                ->toOutput();
        } catch (ObjectStorageException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
