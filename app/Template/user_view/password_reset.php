<div class="page-header">
    <h2><?= t('Last Password Reset') ?></h2>
</div>

<?php if (empty($tokens)): ?>
    <p class="alert"><?= t('The password has never been reinitialized.') ?></p>
<?php else: ?>
    <table class="table-small table-fixed table-scrolling table-striped">
    <tr>
        <th class="column-20"><?= t('Creation') ?></th>
        <th class="column-20"><?= t('Expiration') ?></th>
        <th class="column-5"><?= t('Active') ?></th>
        <th class="column-15"><?= t('IP address') ?></th>
        <th><?= t('User agent') ?></th>
    </tr>
    <?php foreach ($tokens as $token): ?>
    <tr>
        <td><?= $this->dt->datetime($token['date_creation']) ?></td>
        <td><?= $this->dt->datetime($token['date_expiration']) ?></td>
        <td><?= $token['is_active'] == 0 ? t('No') : t('Yes') ?></td>
        <td><?= $this->text->e($token['ip']) ?></td>
        <td><?= $this->text->e($token['user_agent']) ?></td>
    </tr>
    <?php endforeach ?>
    </table>
<?php endif ?>
