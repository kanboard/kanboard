<div class="page-header">
    <h2><?= t('Link labels') ?></h2>
</div>
<?php if (! empty($links)): ?>
<table>
    <tr>
        <th class="column-70"><?= t('Link labels') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($links as $link): ?>
    <tr>
        <td>
            <strong><?= t($link['label']) ?></strong>

            <?php if (! empty($link['opposite_label'])): ?>
                | <?= t($link['opposite_label']) ?>
            <?php endif ?>
        </td>
        <td>
            <ul>
                <?= $this->a(t('Edit'), 'link', 'edit', array('link_id' => $link['id'])) ?>
                <?= t('or') ?>
                <?= $this->a(t('Remove'), 'link', 'confirm', array('link_id' => $link['id'])) ?>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php else: ?>
    <?= t('There is no link.') ?>
<?php endif ?>

<?= $this->render('link/create', array('values' => $values, 'errors' => $errors)) ?>