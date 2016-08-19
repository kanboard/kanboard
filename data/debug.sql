SELECT count(*) AS _count,
       subtask_time_tracking.is_billable AS `subtask_time_tracking.is_billable`,
       subtask_time_tracking.subtask_id AS `subtask_time_tracking.subtask_id`,
       subtasks.task_id AS `subtasks.task_id`,
       tasks.project_id AS `tasks.project_id`,
       sum(subtask_time_tracking.time_spent)
FROM "subtask_time_tracking"
LEFT JOIN "subtasks" ON "subtasks"."id"="subtask_time_tracking"."subtask_id"
LEFT JOIN "tasks" ON "tasks"."id"="subtasks"."task_id"
LEFT JOIN "users" ON "users"."id"="subtask_time_tracking"."user_id"
LEFT JOIN "projects" ON "projects"."id"="tasks"."project_id"
WHERE subtask_time_tracking.start >= 1467379708
    AND subtask_time_tracking.start <= 1469971708
GROUP BY subtask_time_tracking.is_billable,
         subtask_time_tracking.subtask_id,
         subtasks.task_id,
         tasks.project_id
ORDER BY tasks.project_id ASC,
         subtasks.task_id ASC,
         subtask_time_tracking.subtask_id ASC,
         subtask_time_tracking.is_billable ASC,
         subtask_time_tracking.start ASC
