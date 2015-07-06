<div class="form-login">

    <?php if (isset($errors['login'])): ?>
        <p class="alert alert-error"><?= $this->e($errors['login']) ?></p>
    <?php endif ?>

    <form method="post" action="<?= $this->url->href('auth', 'check', array('redirect_query' => $redirect_query)) ?>">

        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Username'), 'username') ?>
        <?= $this->form->text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= $this->form->label(t('Password'), 'password') ?>
        <?= $this->form->password('password', $values, $errors, array('required')) ?>

        <?= $this->form->checkbox('remember_me', t('Remember Me'), 1, true) ?><br/>

        <?php if (GOOGLE_AUTH): ?>
            <?= $this->url->link(t('Login with my Google Account'), 'user', 'google') ?>
        <?php endif ?>

        <?php if (GITHUB_AUTH): ?>
            <?= $this->url->link(t('Login with my GitHub Account'), 'user', 'gitHub') ?>
        <?php endif ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
        </div>
    </form>

</div>