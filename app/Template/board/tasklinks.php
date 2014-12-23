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
        <li><?= Helper\escape($link['name']) ?>
            <ul>
    <?php endif ?>
                <li<?php if (0 == $link['task_inverse_is_active']): ?> class="task-closed"><?php endif ?>>
                    <?= Helper\escape($link['task_inverse_category']) ?>
                    <?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' - '.trim(Helper\escape($link['task_inverse_name'])),
                        'task', 
                        'show', 
                        array('task_id' => $link['task_inverse_id'])) ?>
                </li>
<?php endforeach ?>
            </ul>
        </li>
</ul>
</div>
</section>
