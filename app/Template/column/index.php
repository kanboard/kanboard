<div class="page-header">
    <h2><?= t('Edit the board for "%s"', $project['name']) ?></h2>
</div>

<?php if (! empty($columns)): ?>

    <?php $first_position = $columns[0]['position']; ?>
    <?php $last_position = $columns[count($columns) - 1]['position']; ?>

    <h3><?= t('Change columns') ?></h3>
    <table>
        <tr>
            <th><?= t('Column title') ?></th>
            <th><?= t('Task limit') ?></th>
            <th><?= t('Actions') ?></th>
        </tr>
        <?php foreach ($columns as $column): ?>
        <tr>
            <td class="column-60"><?= $this->e($column['title']) ?>
             <?php if (! empty($column['description'])): ?>
                <span class="tooltip" title='<?= $this->e($this->text->markdown($column['description'])) ?>'>
                    <i class="fa fa-info-circle"></i>
                </span>
            <?php endif ?>
            </td>
            <td class="column-10"><?= $this->e($column['task_limit']) ?></td>
            <td class="column-30">
                <ul>
                    <li>
                        <?= $this->url->link(t('Edit'), 'column', 'edit', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                    </li>
                    <?php if ($column['position'] != $first_position): ?>
                    <li>
                        <?= $this->url->link(t('Move Up'), 'column', 'move', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'up'), true) ?>
                    </li>
                    <?php endif ?>
                    <?php if ($column['position'] != $last_position): ?>
                    <li>
                        <?= $this->url->link(t('Move Down'), 'column', 'move', array('project_id' => $project['id'], 'column_id' => $column['id'], 'direction' => 'down'), true) ?>
                    </li>
                    <?php endif ?>
                    <li>
                        <?= $this->url->link(t('Remove'), 'column', 'confirm', array('project_id' => $project['id'], 'column_id' => $column['id'])) ?>
                    </li>
                </ul>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

<?php endif ?>

<h3><?= t('Add a new column') ?></h3>
<form method="post" action="<?= $this->url->href('column', 'create', array('project_id' => $project['id'])) ?>" autocomplete="off">

    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('project_id', $values) ?>

    <?= $this->form->label(t('Title'), 'title') ?>
    <?= $this->form->text('title', $values, $errors, array('required', 'maxlength="50"')) ?>

    <?= $this->form->label(t('Task limit'), 'task_limit') ?>
    <?= $this->form->number('task_limit', $values, $errors) ?>

    <?= $this->form->label(t('Description'), 'description') ?>

    <div class="form-tabs">
        <div class="write-area">
          <?= $this->form->textarea('description', $values, $errors) ?>
        </div>
        <div class="preview-area">
            <div class="markdown"></div>
        </div>
        <ul class="form-tabs-nav">
            <li class="form-tab form-tab-selected">
                <i class="fa fa-pencil-square-o fa-fw"></i><a id="markdown-write" href="#"><?= t('Write') ?></a>
            </li>
            <li class="form-tab">
                <a id="markdown-preview" href="#"><i class="fa fa-eye fa-fw"></i><?= t('Preview') ?></a>
            </li>
        </ul>
    </div>
    <div class="form-help"><?= $this->url->doc(t('Write your text in Markdown'), 'syntax-guide') ?></div>

    <div class="form-actions">
        <input type="submit" value="<?= t('Add this column') ?>" class="btn btn-blue"/>
    </div>
</form>