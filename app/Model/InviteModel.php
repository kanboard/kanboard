<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;
use Kanboard\Core\Security\Token;

/**
 * Class InviteModel
 *
 * @package Kanboard\Model
 * @author  Frederic Guillot
 */
class InviteModel extends Base
{
    const TABLE = 'invites';

    public function createInvites(array $emails, $projectId)
    {
        $emails = array_unique($emails);
        $nb = 0;

        foreach ($emails as $email) {
            $email = trim($email);

            if (! empty($email) && $this->createInvite($email, $projectId)) {
                $nb++;
            }
        }

        return $nb;
    }

    protected function createInvite($email, $projectId)
    {
        $values = array(
            'email'      => $email,
            'project_id' => $projectId,
            'token'      => Token::getToken(),
        );

        if ($this->db->table(self::TABLE)->insert($values)) {
            $this->sendInvite($values);
            return true;
        }

        return false;
    }

    protected function sendInvite(array $values)
    {
        $this->emailClient->send(
            $values['email'],
            $values['email'],
            e('Kanboard Invitation'),
            $this->template->render('user_invite/email', array('token' => $values['token']))
        );
    }

    public function getByToken($token)
    {
        return $this->db->table(self::TABLE)
            ->eq('token', $token)
            ->findOne();
    }

    public function remove($email)
    {
        return $this->db->table(self::TABLE)
            ->eq('email', $email)
            ->remove();
    }
}
