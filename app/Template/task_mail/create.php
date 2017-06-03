<div class="page-header">
    <h2><?= $this->text->e($task['title']) ?> &gt; <?= t('Send by email') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('TaskMailController', 'send', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" autocomplete="off" class="js-task-mail-form">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Email'), 'email') ?>
    <?= $this->form->email('email', $values, $errors, array('autofocus', 'required', 'tabindex="1"')) ?>

    <?php if (! empty($members)): ?>
    <div class="dropdown">
        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><i class="fa fa-address-card-o"></i><i class="fa fa-caret-down"></i></a>
        <ul>
            <?php foreach ($members as $member): ?>
                <li data-email="<?= $this->text->e($member['email']) ?>" class="js-autocomplete-email">
                    <?= $this->text->e($this->user->getFullname($member)) ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <?php endif ?>

    <?= $this->form->label(t('Subject'), 'subject') ?>
    <?= $this->form->text('subject', $values, $errors, array('required', 'tabindex="2"')) ?>

    <?= $this->form->checkbox('add_comment', t('Log this action in a comment'), 1) ?>

    <?= $this->modal->submitButtons(array(
        'submitLabel' => t('Send by email'),
        'tabindex'    => 3,
    )) ?>
</form>
