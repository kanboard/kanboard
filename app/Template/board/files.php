<section>
    <?php foreach ($files as $file): ?>
		<i class="fa fa-file-o fa-fw"></i>

        <?= $this->a(
            $this->e($file['name']),
            'file',
            'download',
            array('file_id' => $file['id'], 'task_id' => $file['task_id'])
        ) ?>

        <br/>
    <?php endforeach ?>
</section>
