<p><strong><?= t('Welcome to Kanboard %s', $user['name'] ?: $user['username']) ?></strong></p>

<p><?= t('An account has been created for you on Kanboard by "%s".', $invitor['name'] ?: $invitor['username']) ?></p>

<p><?= t('Your login is "%s".', $user['username']) ?></p>
<p><?= t('Choose your password by clicking on this link: %s', $this->url->to('PasswordReset', 'change', array('token' => $token), '', true)) ?></p>

<hr>
Kanboard