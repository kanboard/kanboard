<div  class="form-login">

    <div class="page-header">
        <h1><?= t('Sign in') ?></h1>
    </div>

    <?php if (isset($errors['login'])): ?>
        <p class="alert alert-error"><?= Helper\escape($errors['login']) ?></p>
    <?php endif ?>

    <form method="post" action="?controller=user&amp;action=check&amp;redirect_query=<?= urlencode($redirect_query) ?>">

        <?= Helper\form_csrf() ?>

        <?= Helper\form_label(t('Username'), 'username') ?>
        <?= Helper\form_text('username', $values, $errors, array('autofocus', 'required')) ?><br/>

        <?= Helper\form_label(t('Password'), 'password') ?>
        <?= Helper\form_password('password', $values, $errors, array('required')) ?>

        <?= Helper\form_checkbox('remember_me', t('Remember Me'), 1) ?><br/>

        <ul>
        <?php if (GOOGLE_AUTH): ?>
            <li>
                <a href="?controller=user&amp;action=google"><?= t('Login with my Google Account') ?></a>
            </li>
        <?php endif ?>

        <?php if (GITHUB_AUTH): ?>
            <li>
                <a href="?controller=user&amp;action=gitHub"><?= t('Login with my GitHub Account') ?></a>
            </li>
        <?php endif ?>
        </ul>

        <div class="form-actions">
            <input type="submit" value="<?= t('Sign in') ?>" class="btn btn-blue"/>
        </div>
    </form>

</div>