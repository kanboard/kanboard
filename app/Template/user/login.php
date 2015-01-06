<div class="form-login">

    <?php if (isset($errors['login'])): ?>
        <p class="alert alert-error"><?= $this->e($errors['login']) ?></p>
    <?php endif ?>

    <form method="post" action="<?= $this->u('user', 'check', array('redirect_query' => urlencode($redirect_query))) ?>">

        <?= $this->formCsrf() ?>

        <?= $this->formLabel(t('Username'), 'username') ?>
        <?= $this->formText('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= $this->formLabel(t('Password'), 'password') ?>
        <?= $this->formPassword('password', $values, $errors, array('required')) ?>

        <?= $this->formCheckbox('remember_me', t('Remember Me'), 1) ?><br/>

        <?php if (GOOGLE_AUTH): ?>
            <?= $this->a(t('Login with my Google Account'), 'user', 'google') ?>
        <?php endif ?>

        <?php if (GITHUB_AUTH): ?>
            <?= $this->a(t('Login with my GitHub Account'), 'user', 'gitHub') ?>
        <?php endif ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
        </div>
    </form>

</div>