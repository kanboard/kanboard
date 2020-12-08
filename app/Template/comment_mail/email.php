<?= $this->text->markdown($email['comment'], true) ?>

<?= $this->render('notification/footer', array('task' => $task)) ?>
