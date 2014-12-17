<?php if (! empty($links)): ?>
<aside id="links" class="task-show-section">
	<div class="page-header">
        <h2><?= t('Links') ?></h2>
    </div>
    
	<table class="link-table">
        <tr>
            <th width="80%"><?= t('Links') ?></th>
            <?php if (! isset($not_editable)): ?>
                <th><?= t('Actions') ?></th>
            <?php endif ?>
        </tr>
		<?php foreach ($links as $link): ?>
		<tr>
            <td>
			<?= Helper\escape($link['name']) ?>
			<?php if (0 == $link['task_inverse_is_active']): ?><span class="task-closed"><?php endif ?><?= Helper\a('#'.Helper\escape($link['task_inverse_id']).' '.trim(Helper\escape($link['task_inverse_name'])), 'task', '', array('task_id' => $link['task_inverse_id'], 'action' => 'show')) ?><?php if (0 == $link['task_inverse_is_active']): ?></span><?php endif ?>
			</td>
			<td>
			<?= Helper\a(t('Edit'), 'tasklink', 'edit', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
			<?= t('or') ?>
			<?= Helper\a(t('Remove'), 'tasklink', 'confirm', array('task_id' => $task['id'], 'link_id' => $link['id'])) ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
        
	<?php if (! isset($not_editable)): ?>
	<form method="post" action="<?= Helper\u('tasklink', 'save', array('task_id' => $task['id'])) ?>" autocomplete="off">
		<?= Helper\form_csrf() ?>
	    <?= Helper\form_hidden('task_id', array('task_id' => $task['id'])) ?>
	    <?= Helper\form_select('link_id', $link_list, array(), array(), 'required autofocus') ?>
	    #<?= Helper\form_numeric('task_inverse_id', array(), array(), array('required', 'placeholder="'.t('Task id').'"', 'list="task_inverse_ids"')) ?>
	    <datalist id="task_inverse_ids">
		  <select>
		  <?php foreach ($task_list as $task_inverse): ?>
		    <option value="<?= $task_inverse['id'] ?>">#<?= $task_inverse['id'].' '.$task_inverse['title'] ?></option>
		  <?php endforeach ?>
		  </select>
		</datalist>
		<input type="submit" value="<?= t('Add') ?>" class="btn btn-blue"/>
	</form>
	<?php endif ?>
</aside>
<?php endif ?>
