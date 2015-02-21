<?php foreach (tags_csv2array($this->e($task['tags'])) as $tag): ?>
    <span class="tag <?=str_replace(array(' ','.'), array('',''), $tag) ?>"><?= $tag ?></span>
<?php endforeach; ?>
