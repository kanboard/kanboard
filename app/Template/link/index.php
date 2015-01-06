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
        <td><?= $this->e($link['name']) ?> | <?= $this->e($link['name_inverse']) ?></td>
        <td>
            <ul>
                <li>
                    <?= $this->a(t('Edit'), 'link', 'edit', array('project_id' => $project['id'], 'link_id' => $link['id'])) ?>
                </li>
                <li>
                    <?= $this->a(t('Remove'), 'link', 'confirm', array('project_id' => $project['id'], 'link_id' => $link['id'])) ?>
                </li>
            </ul>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<h3><?= t('Add a new link') ?></h3>
<form method="post" action="<?= $this->u('link', 'save', array('project_id' => $project['id'])) ?>" autocomplete="off">
	<div class="alert alert-info">
		<strong><?= t('Example:') ?></strong>
		<i><?= t('#9 Precedes #10') ?></i>
		<?= t('and therefore') ?>
		<i><?= t('#10 Follows #9') ?></i>
	</div>
    <?= $this->formCsrf() ?>
    <?= $this->formHidden('project_id', $values) ?>

    <?= $this->formLabel(t('Link Label'), 'name') ?>
    <?= $this->formText('name', $values, $errors, array('required', 'autofocus', 'placeholder="Precedes"')) ?>

    <?= $this->formLabel(t('Link Inverse Label'), 'name_inverse') ?>
    <?= $this->formText('name_inverse', $values, $errors, array('required', 'placeholder="Follows"')) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue"/>
    </div>
</form>