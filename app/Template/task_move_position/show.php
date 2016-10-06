<div class="page-header">
    <h2><?= t('Move task to another position on the board') ?></h2>
</div>

<script type="x/template" id="template-task-move-position">
    <?= $this->form->label(t('Swimlane'), 'swimlane') ?>
    <select v-model="swimlaneId" @change="onChangeSwimlane()" id="form-swimlane">
        <option v-for="swimlane in board" v-bind:value="swimlane.id">
            {{ swimlane.name }}
        </option>
    </select>

    <div v-if="columns.length > 0">
        <?= $this->form->label(t('Column'), 'column') ?>
        <select v-model="columnId" @change="onChangeColumn()" id="form-column">
            <option v-for="column in columns" v-bind:value="column.id">
                {{ column.title }}
            </option>
        </select>
    </div>

    <div v-if="tasks.length > 0">
        <?= $this->form->label(t('Position'), 'position') ?>
        <select v-model="position" id="form-position">
            <option v-for="task in tasks" v-bind:value="task.position">#{{ task.id }} - {{ task.title }}</option>
        </select>
        <label><input type="radio" value="before" v-model="positionChoice"><?= t('Insert before this task') ?></label>
        <label><input type="radio" value="after" v-model="positionChoice"><?= t('Insert after this task') ?></label>
    </div>

    <submit-cancel
        label-button="<?= t('Save') ?>"
        label-or="<?= t('or') ?>"
        label-cancel="<?= t('cancel') ?>"
        :callback="onSubmit">
    </submit-cancel>
</script>

<task-move-position
    save-url="<?= $this->url->href('TaskMovePositionController', 'save', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>"
    :board='<?= json_encode($board, JSON_HEX_APOS) ?>'
></task-move-position>
