<section>
    <?php foreach ($files as $file): ?>
		<i class="fa fa-file-o" style="display:inline"></i>&nbsp;<a href="?controller=file&amp;action=download&amp;file_id=<?= $file['id'] ?>&amp;task_id=<?= $task['id'] ?>"><?= Helper\escape($file['name']) ?></a>
		<br>
    <?php endforeach ?>
</section>
