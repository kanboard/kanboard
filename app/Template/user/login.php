<div class="form-login">

    <?php if (isset($errors['login'])): ?>
        <p class="alert alert-error"><?= Helper\escape($errors['login']) ?></p>
    <?php endif ?>

    <form method="post" action="<?= Helper\u('user', 'check', array('redirect_query' => urlencode($redirect_query))) ?>">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_label(t('Username'), 'username') ?>
        <?= Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= Helper\form_label(t('Password'), 'password') ?>
        <?= Helper\form_password('password', $values, $errors, array('required')) ?>

        <?= Helper\form_checkbox('remember_me', t('Remember Me'), 1) ?><br/>

        <?php if (GOOGLE_AUTH): ?>
            <?= Helper\a(t('Login with my Google Account'), 'user', 'google') ?>
        <?php endif ?>

        <?php if (GITHUB_AUTH): ?>
            <?= Helper\a(t('Login with my GitHub Account'), 'user', 'gitHub') ?>
        <?php endif ?>

        <div class="form-actions">
            <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
        </div>
    </form>

</div>