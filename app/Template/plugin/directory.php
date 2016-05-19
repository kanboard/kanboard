<div class="page-header">
    <h2><?= t('Plugin Directory') ?></h2>
</div>

<?php if (empty($plugins)): ?>
    <p class="alert"><?= t('There is no plugin available.') ?></p>
<?php else: ?>
    <table class="table-stripped">
        <tr>
            <th class="column-20"><?= t('Name') ?></th>
            <th class="column-20"><?= t('Author') ?></th>
            <th class="column-10"><?= t('Version') ?></th>
            <th><?= t('Description') ?></th>
            <th><?= t('Action') ?></th>
        </tr>

        <?php foreach ($plugins as $plugin): ?>
            <tr>
                <td>
                    <a href="<?= $plugin['homepage'] ?>" target="_blank" rel="noreferrer"><?= $this->text->e($plugin['title']) ?></a>
                </td>
                <td><?= $this->text->e($plugin['author']) ?></td>
                <td><?= $this->text->e($plugin['version']) ?></td>
                <td><?= $this->text->e($plugin['description']) ?></td>
                <td>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
