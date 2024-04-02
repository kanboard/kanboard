<details class="accordion-section" open>
    <summary class="accordion-title"><?= t('Project expenses') ?></summary>
    <div class="accordion-content">
        <?php if ($this->user->hasProjectAccess('ProjectEditController', 'show', $project['id'])): ?>
            <div class="buttons-header">
                <?= $this->modal->mediumButton('edit', t('Set project expenses threshold'), 'ProjectEditController', 'show', array('project_id' => $project['id'])) ?>
            </div>
        <?php endif; ?>

        <div class="panel">
            <?php
            $projectExpensesThreshold = 0;
            if (isset($project['project_expenses_threshold'])):
            if (!empty($tasks)):

            $currentExpenses = 0;
            $projectExpensesThreshold = $project['project_expenses_threshold'];
            ?>
            <table class="table-striped table-scrolling">
                <tr>
                    <th style="text-align: center; background-color: #d2d2d2">
                        <?= t('Task name') ?>
                    </th>
                    <th style="text-align: center; background-color: #d2d2d2">
                        <?= t('Task expense') ?>
                    </th>
                    <th style="text-align: center; background-color: #d2d2d2">
                        <?= t('Task expenses percentage') ?>
                    </th>
                    <th style="text-align: center; background-color: #d2d2d2">
                        <?= t('Date modification') ?>
                    </th>
                    <th style="text-align: center; background-color: #d2d2d2">
                        <?= t('In column') ?>
                    </th>
                </tr>
                <?php foreach ($tasks as $task):
                    $taskExpense = (float)$task['taskExpenses'];

                    $currentExpenses += $taskExpense;
                    ?>
                    <tr>
                        <td>
                            <a href="<?= $this->url->href('TaskViewController', 'show', ['task_id' => $task['taskId']], false, '', true) ?>"
                               title="<?= $this->text->e($task['taskName']) ?>">
                                <?= $this->text->e($task['taskName']) ?>
                            </a>
                        </td>
                        <td style="text-align: right; ">
                            <?= $this->text->e(number_format($taskExpense, 2)) . ' ' . $referenceCurrency ?>
                        </td>
                        <td style="text-align: right;">
                            <?php
                            $taskExpansesPercent = round($taskExpense * 100 / $project['project_expenses_threshold'], 2);

                            echo $this->text->e($taskExpansesPercent) . '%';
                            ?>
                        </td>
                        <td style="text-align: right;">
                            <?= $this->dt->datetime($task['dateModification']) ?>
                        </td>
                        <td style="text-align: center;">
                            <?= $this->text->e($task['columnTitle']) ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
            <ul>
                <li><?= t('Project expenses threshold') ?>
                    <strong><?= $this->text->e(number_format($project['project_expenses_threshold'], 2)) . ' ' . $referenceCurrency ?></strong>
                </li>
                <li><?= t('Project current expenses') ?>
                    <strong><?= $this->text->e(number_format($currentExpenses, 2)) . ' ' . $referenceCurrency ?></strong>
                </li>
                <li><?= t('Percent of project expenses') ?>
                    <?php
                    $expansesPercent = round($currentExpenses * 100 / $project['project_expenses_threshold'], 2);
                    $barExpensesPercent = ($expansesPercent <= 100) ? $expansesPercent : 100;
                    ?>
                    <strong><?= $this->text->e($expansesPercent) . '%' ?></strong>

                    <div style="border-style: solid; border-width: 1px;">
                        <div style="background-color: #4d90fe; color: #fff; height:24px; width:<?= $barExpensesPercent . '%' ?>">
                            <?= $expansesPercent . '%' ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    <?php else: ?>
        <span>
                <?= t('None project tasks have expense set') ?>
            </span>
    <?php endif; ?>
    <?php else: ?>
        <span>
                <?= t('Project expanses threshold is required') ?>
            </span>
    <?php endif; ?>
    </div>
</details>
