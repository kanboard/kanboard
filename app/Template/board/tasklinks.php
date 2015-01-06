<section class="tooltip-tasklinks">
<div>
<ul>
<?php
$previous_link = null;
foreach ($links as $link): ?>
    <?php if (null == $previous_link || $previous_link != $link['name']): ?>
        <?php if (null != $previous_link): ?>
            </ul>
        </li>
        <?php endif ?>
        <?php $previous_link = $link['name']; ?>
        <li><?= $this->e($link['name']) ?>
            <ul>
    <?php endif ?>
                <li<?php if (0 == $link['task_inverse_is_active']): ?> class="task-closed"><?php endif ?>>
                    <?= $this->e($link['task_inverse_category']) ?>
                    <?= $this->a('#'.$this->e($link['task_inverse_id']).' - '.trim($this->e($link['task_inverse_name'])),
                        'task', 
                        'show', 
                        array('task_id' => $link['task_inverse_id'], 'project_id' => $task['project_id'])) ?>
                </li>
<?php endforeach ?>
            </ul>
        </li>
</ul>
</div>
</section>
