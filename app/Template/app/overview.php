<?= $this->render('app/projects', array('paginator' => $project_paginator)) ?>
<?= $this->render('app/tasks', array('paginator' => $task_paginator)) ?>
<?= $this->render('app/subtasks', array('paginator' => $subtask_paginator)) ?>