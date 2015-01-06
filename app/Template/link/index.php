<div class="page-header">
    <h2><?= $title ?></h2>
</div>

<?php if (! empty($links)): ?>
<table>
    <tr>
        <th><?= t('Link Labels') ?></th>
        <th><?= t('Actions') ?></th>
    </tr>
    <?php foreach ($links as $link): ?>
    <tr>
        <td><?= Helper\escape($link['name']) ?> | <?= Helper\escape($link['name_inverse']) ?></td>
        <td>
            <ul>
                <li>
                    <?= Helper\a(t('Edit'), 'link', 'edit', array('project_id' => $project['id'], 'link_id' => $link['id'])) ?>
                </li>
                <li>
                    <?= Helper\a(t('Remove'), 'link', 'confirm', array('project_id' => $project['id'], 'link_id' => $link['id'])) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<h3><?= t('Add a new link') ?></h3>
<form method="post" action="<?= Helper\u('link', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
	<div class="alert alert-info">
		<strong><?= t('Example:') ?></strong>
		<i><?= t('#9 Precedes #10') ?></i>
		<?= t('and therefore') ?>
		<i><?= t('#10 Follows #9') ?></i>
	</div>
    <?= Helper\form_csrf() ?>
    <?= Helper\form_hidden('project_id', $values) ?>

    <?= Helper\form_label(t('Link Label'), 'name') ?>
    <?= Helper\form_text('name', $values, $errors, array('required', 'autofocus', 'placeholder="Precedes"')) ?>

    <?= Helper\form_label(t('Link Inverse Label'), 'name_inverse') ?>
    <?= Helper\form_text('name_inverse', $values, $errors, array('required', 'placeholder="Follows"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>