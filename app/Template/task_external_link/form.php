<?= $this->form->csrf() ?>
<?= $this->form->hidden('link_type-'.$values['index'], $values) ?>

<?= $this->form->label(t('URL'), 'url') ?>
<?= $this->form->text('url-'.$values['index'], $values, $errors, array('required')) ?>

<?= $this->form->label(t('Title'), 'title') ?>
<?= $this->form->text('title-'.$values['index'], $values, $errors, array('required')) ?>

<?= $this->form->label(t('Dependency'), 'dependency') ?>
<?= $this->form->select('dependency-'.$values['index'], $dependencies, $values, $errors) ?>
