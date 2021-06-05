<?php

namespace Kanboard\Controller;

use Kanboard\Core\ObjectStorage\ObjectStorageException;
use Kanboard\Core\Thumbnail;

/**
 * Avatar File Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class AvatarFileController extends BaseController
{
    /**
     * Display avatar page
     */
    public function show()
    {
        $user = $this->getUser();

        $this->response->html($this->helper->layout->user('avatar_file/show', array(
            'user' => $user,
        )));
    }

    /**
     * Upload Avatar
     */
    public function upload()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();

        if (! $this->request->getFileInfo('avatar')['name']) {
            $this->flash->failure(t('You must select a file to upload as your avatar!'));
        } elseif (! $this->avatarFileModel->isAvatarImage($this->request->getFileInfo('avatar')['name'])) {
            $this->flash->failure(t('The file you uploaded is not a valid image! (Only *.gif, *.jpg, *.jpeg and *.png are allowed!)'));
        } else {
            if (! $this->avatarFileModel->uploadImageFile($user['id'], $this->request->getFileInfo('avatar'))) {
                $this->flash->failure(t('Unable to upload files, check the permissions of your data folder.'));
            }
        }

        $this->renderResponse($user['id']);
    }

    /**
     * Remove Avatar image
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $user = $this->getUser();
        $this->avatarFileModel->remove($user['id']);
        $this->userSession->refresh($user['id']);
        $this->renderResponse($user['id']);
    }

    /**
     * Show Avatar image (public)
     */
    public function image()
    {
        $user_id = $this->request->getIntegerParam('user_id');
        $size = $this->request->getStringParam('size', 48);
        $hash = $this->request->getStringParam('hash');

        if ($size > 100) {
            $this->response->status(400);
            return;
        }

        $filename = $this->avatarFileModel->getFilename($user_id);
        $etag = md5($filename.$size);

        if ($hash !== $etag) {
            $this->response->status(404);
            return;
        }

        $this->response->withCache(365 * 86400, $etag);
        $this->response->withContentType('image/png');

        if ($this->request->getHeader('If-None-Match') !== '"'.$etag.'"') {
            $this->response->send();
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

    protected function renderResponse($userId)
    {
        if ($this->request->isAjax()) {
            $this->show();
        } else {
            $this->response->redirect($this->helper->url->to('AvatarFileController', 'show', array('user_id' => $userId)));
        }
    }
}
