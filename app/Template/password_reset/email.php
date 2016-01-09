<p><?= t('To reset your password click on this link:') ?></p>

<p><?= $this->url->to('PasswordReset', 'change', array('token' => $token), '', true) ?></p>

<hr>
Kanboard