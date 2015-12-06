<?php if (! empty($roles[$role])): ?>
    <ul class="no-bullet">
    <?php foreach ($roles[$role] as $user_id => $user_name): ?>
        <li><?= $this->url->link($this->e($user_name), 'projectuser', 'opens', array('user_id' => $user_id)) ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>