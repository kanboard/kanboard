<?php if (isset($show_title)): ?>
    <div class="task-show-title color-<?= $task['color_id'] ?>">
        <h2><?= $this->text->e($task['title']) ?></h2>
    </div>
<?php endif ?>

<div class="page-header">
    <h2><?= t('Internal links') ?></h2>
</div>

<div id="link">

    <?= $this->render('tasklink/table', array('links' => $links, 'task' => $task, 'project' => $project, 'editable' => $editable, 'is_public' => $is_public)) ?>

    <?php if ($editable && isset($link_label_list)): ?>
        <form action="<?= $this->url->href('tasklink', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post" autocomplete="off">
            <?= $this->form->csrf() ?>
            <?= $this->form->hidden('task_id', array('task_id' => $task['id'])) ?>
            <?= $this->form->hidden('opposite_task_id', array()) ?>
            <?= $this->form->select('link_id', $link_label_list, array(), array()) ?>
            <?= $this->form->text(
                'title',
                array(),
                array(),
                array(
                    'required',
                    'placeholder="'.t('Start to type task title...').'"',
                    'title="'.t('Start to type task title...').'"',
                    'data-dst-field="opposite_task_id"',
                    'data-search-url="'.$this->url->href('TaskHelper', 'autocomplete', array('exclude_task_id' => $task['id'])).'"',
                ),
                'autocomplete') ?>
            <input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
        </form>
    <?php endif ?>

</div>
