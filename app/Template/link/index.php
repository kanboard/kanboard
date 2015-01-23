<div class="page-header">
    <h2><?= t('Link labels') ?></h2>
</div>

<section>
<?php if (! empty($links)): ?>
<table>
    <tr>
        <th width="70%"><?= t('Link labels') ?></th>
        <th><?= t('Project') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($links as $link): ?>
    <tr>
        <td><?= $this->e($link['name']) ?> | <?= $this->e($link['name_inverse']) ?></td>
        <td><?php if (-1 != $link['project_id']): ?>*<?php endif ?></td>
        <td>
            <ul>
                <?= $this->a(t('Edit'), 'link', 'edit', array('link_id' => $link['id'], 'project_id' => $link['project_id'])) ?>
                <?= t('or') ?>
                <?= $this->a(t('Remove'), 'link', 'confirm', array('link_id' => $link['id'], 'project_id' => $link['project_id'])) ?>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php else: ?>
    <?= t('There is no link yet.') ?>
<?php endif ?>
</section>

<?= $this->render('link/edit', array('values' => $values, 'errors' => $errors, 'project' => $project)) ?>
