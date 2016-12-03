<?php

namespace Kanboard\Controller;

use Kanboard\Core\Security\Token;

/**
 * Class UserApiAccessController
 *
 * @package Kanboard\Controller
 * @author  Frederic Guillot
 */
class UserApiAccessController extends BaseController
{
    public function show()
    {
        $user = $this->getUser();

        return $this->response->html($this->helper->layout->user('user_api_access/show', array(
            'user'  => $user,
            'title' => t('API User Access'),
        )));
    }

    public function generate()
    {
        $user = $this->getUser();
        $this->checkCSRFParam();

        $this->userModel->update(array(
            'id' => $user['id'],
            'api_access_token' => Token::getToken(),
        ));

        $this->response->redirect($this->helper->url->to('UserApiAccessController', 'show', array('user_id' => $user['id'])));
    }

    public function remove()
    {
        $user = $this->getUser();
        $this->checkCSRFParam();

        $this->userModel->update(array(
            'id' => $user['id'],
            'api_access_token' => null,
        ));

        $this->response->redirect($this->helper->url->to('UserApiAccessController', 'show', array('user_id' => $user['id'])));
    }
}