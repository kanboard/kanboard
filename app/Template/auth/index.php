<div class="form-login">

    <?php if (isset($errors['login'])): ?>
        <p class="alert alert-error"><?= $this->e($errors['login']) ?></p>
    <?php endif ?>

    <?php if (! HIDE_LOGIN_FORM): ?>
    <form method="post" action="<?= $this->url->href('auth', 'check') ?>">

        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Username'), 'username') ?>
        <?= $this->form->text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= $this->form->label(t('Password'), 'password') ?>
        <?= $this->form->password('password', $values, $errors, array('required')) ?>

        <?= $this->form->checkbox('remember_me', t('Remember Me'), 1, true) ?><br/>

        <div class="form-actions">
            <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
        </div>
    </form>
    <?php endif ?>

    <?php if (GOOGLE_AUTH || GITHUB_AUTH): ?>
    <ul class="no-bullet">
        <?php if (GOOGLE_AUTH): ?>
            <li><?= $this->url->link(t('Login with my Google Account'), 'oauth', 'google') ?></li>
        <?php endif ?>

        <?php if (GITHUB_AUTH): ?>
            <li><?= $this->url->link(t('Login with my Github Account'), 'oauth', 'gitHub') ?></li>
        <?php endif ?>
    </ul>
    <?php endif ?>

</div>