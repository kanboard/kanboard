<?php

namespace Schema;

require_once __DIR__.'/Migration.php';

use PDO;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

const VERSION = 137;

function version_137(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `projects` ADD COLUMN `enable_global_tags` TINYINT(1) DEFAULT 1 NOT NULL');
}

function version_136(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `swimlanes` ADD COLUMN `task_limit` INT DEFAULT 0');
}

function version_135(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `projects` ADD COLUMN `task_limit` INT DEFAULT 0');
}

function version_134(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `projects` ADD COLUMN `per_swimlane_task_limits` INT DEFAULT 0 NOT NULL');
}

function version_133(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `tags` ADD COLUMN `color_id` VARCHAR(50) DEFAULT NULL');
}

function version_132(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `project_has_categories` ADD COLUMN `color_id` VARCHAR(50) DEFAULT NULL');
}

function version_131(PDO $pdo)
{
    $pdo->exec("ALTER TABLE `users` MODIFY `language` VARCHAR(11) DEFAULT NULL");
}

/*

This migration convert table encoding to utf8mb4.
You should also convert the database encoding:

ALTER DATABASE kanboard CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

You might need to run:

REPAIR TABLE table_name;
OPTIMIZE TABLE table_name;

The max length for Mysql 5.6 is 191 for varchar unique keys in utf8mb4

*/
function version_130(PDO $pdo)
{
    $pdo->exec("ALTER TABLE `swimlanes` MODIFY `name` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `users` MODIFY `username` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `groups` MODIFY `name` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `links` MODIFY `label` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `tags` MODIFY `name` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `sessions` MODIFY `id` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `project_role_has_restrictions` MODIFY `rule` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `project_has_roles` MODIFY `role` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `project_has_categories` MODIFY `name` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `invites` MODIFY `email` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `invites` MODIFY `token` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `groups` MODIFY `name` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `columns` MODIFY `title` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `column_has_restrictions` MODIFY `rule` VARCHAR(191) NOT NULL");
    $pdo->exec("ALTER TABLE `comments` MODIFY `reference` VARCHAR(191) DEFAULT ''");
    $pdo->exec("ALTER TABLE `tasks` MODIFY `reference` VARCHAR(191) DEFAULT ''");

    $tables = [
        'action_has_params',
        'actions',
        'column_has_move_restrictions',
        'column_has_restrictions',
        'columns',
        'comments',
        'currencies',
        'custom_filters',
        'group_has_users',
        'groups',
        'invites',
        'last_logins',
        'links',
        'password_reset',
        'plugin_schema_versions',
        'predefined_task_descriptions',
        'project_activities',
        'project_daily_column_stats',
        'project_daily_stats',
        'project_has_categories',
        'project_has_files',
        'project_has_groups',
        'project_has_metadata',
        'project_has_notification_types',
        'project_has_roles',
        'project_has_users',
        'project_role_has_restrictions',
        'projects',
        'remember_me',
        'sessions',
        'settings',
        'subtask_time_tracking',
        'subtasks',
        'swimlanes',
        'tags',
        'task_has_external_links',
        'task_has_files',
        'task_has_links',
        'task_has_metadata',
        'task_has_tags',
        'tasks',
        'transitions',
        'user_has_metadata',
        'user_has_notification_types',
        'user_has_notifications',
        'user_has_unread_notifications',
        'users',
    ];

    foreach ($tables as $table) {
        $pdo->exec('ALTER TABLE `'.$table.'` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }
}

function version_129(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `projects` MODIFY `name` TEXT NOT NULL');
    $pdo->exec('ALTER TABLE `projects` MODIFY `email` TEXT');
    $pdo->exec('ALTER TABLE `action_has_params` MODIFY `name` TEXT NOT NULL');
    $pdo->exec('ALTER TABLE `action_has_params` MODIFY `value` TEXT NOT NULL');
    $pdo->exec('ALTER TABLE `actions` MODIFY `event_name` TEXT NOT NULL');
    $pdo->exec('ALTER TABLE `actions` MODIFY `action_name` TEXT NOT NULL');
    $pdo->exec("ALTER TABLE `comments` MODIFY `reference` VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE `custom_filters` MODIFY `filter` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `custom_filters` MODIFY `name` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `groups` MODIFY `name` VARCHAR(255) NOT NULL");
    $pdo->exec("ALTER TABLE `project_activities` MODIFY `event_name` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `project_has_files` MODIFY `name` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `project_has_files` MODIFY `path` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `subtasks` MODIFY `title` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `swimlanes` MODIFY `name` VARCHAR(255) NOT NULL");
    $pdo->exec("ALTER TABLE `task_has_external_links` MODIFY `title` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `task_has_external_links` MODIFY `url` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `task_has_files` MODIFY `name` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `task_has_files` MODIFY `path` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `tasks` MODIFY `title` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `tasks` MODIFY `reference` VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE `user_has_unread_notifications` MODIFY `event_name` TEXT NOT NULL");
    $pdo->exec("ALTER TABLE `users` MODIFY `username` VARCHAR(255) NOT NULL");
    $pdo->exec("ALTER TABLE `users` MODIFY `filter` TEXT");
}

function version_128(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `users` ADD COLUMN `filter` VARCHAR(255) DEFAULT NULL');
}

function version_127(PDO $pdo)
{
    $pdo->exec("CREATE TABLE sessions (
        id VARCHAR(255) NOT NULL,
        expire_at INT NOT NULL,
        data LONGTEXT,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8");
}

function version_126(PDO $pdo)
{
    $pdo->exec('CREATE TABLE predefined_task_descriptions (
        id INT NOT NULL AUTO_INCREMENT,
        project_id INT NOT NULL,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8');
}

function version_125(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects DROP COLUMN is_everybody_allowed');
}

function version_124(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN predefined_email_subjects TEXT');
}

function version_123(PDO $pdo)
{
    $pdo->exec('ALTER TABLE column_has_move_restrictions ADD COLUMN only_assigned TINYINT(1) DEFAULT 0');
}

function version_122(PDO $pdo)
{
    migrate_default_swimlane($pdo);

    $pdo->exec('ALTER TABLE `projects` DROP COLUMN `default_swimlane`');
    $pdo->exec('ALTER TABLE `projects` DROP COLUMN `show_default_swimlane`');
    $pdo->exec('ALTER TABLE `tasks` MODIFY `swimlane_id` INT(11) NOT NULL;');
    $pdo->exec('ALTER TABLE tasks ADD CONSTRAINT tasks_swimlane_ibfk_1 FOREIGN KEY (swimlane_id) REFERENCES swimlanes(id) ON DELETE CASCADE');
}

function version_121(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN email VARCHAR(255)');
}

function version_120(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE invites (
            email VARCHAR(255) NOT NULL,
            project_id INTEGER NOT NULL,
            token VARCHAR(255) NOT NULL,
            PRIMARY KEY(email, token)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("DELETE FROM settings WHERE `option`='application_datetime_format'");
}

function version_119(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `comments` ADD COLUMN `date_modification` BIGINT(20)');
    $pdo->exec('UPDATE `comments` SET `date_modification` = `date_creation` WHERE `date_modification` IS NULL');
}

function version_118(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `users` ADD COLUMN `api_access_token` VARCHAR(255) DEFAULT NULL');
}

function version_117(PDO $pdo)
{
    $pdo->exec("ALTER TABLE `settings` MODIFY `value` TEXT");
}

function version_116(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN external_provider VARCHAR(255)");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN external_uri VARCHAR(255)");
}

function version_115(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE column_has_restrictions (
            restriction_id INT NOT NULL AUTO_INCREMENT,
            project_id INT NOT NULL,
            role_id INT NOT NULL,
            column_id INT NOT NULL,
            rule VARCHAR(255) NOT NULL,
            UNIQUE(role_id, column_id, rule),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE,
            PRIMARY KEY(restriction_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_114(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_role_has_restrictions (
            restriction_id INT NOT NULL AUTO_INCREMENT,
            project_id INT NOT NULL,
            role_id INT NOT NULL,
            rule VARCHAR(255) NOT NULL,
            UNIQUE(role_id, rule),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE,
            PRIMARY KEY(restriction_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_113(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_roles (
            role_id INT NOT NULL AUTO_INCREMENT,
            `role` VARCHAR(255) NOT NULL,
            project_id INT NOT NULL,
            UNIQUE(project_id, `role`),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            PRIMARY KEY(role_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE column_has_move_restrictions (
            restriction_id INT NOT NULL AUTO_INCREMENT,
            project_id INT NOT NULL,
            role_id INT NOT NULL,
            src_column_id INT NOT NULL,
            dst_column_id INT NOT NULL,
            UNIQUE(role_id, src_column_id, dst_column_id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE,
            FOREIGN KEY(src_column_id) REFERENCES columns(id) ON DELETE CASCADE,
            FOREIGN KEY(dst_column_id) REFERENCES columns(id) ON DELETE CASCADE,
            PRIMARY KEY(restriction_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("ALTER TABLE `project_has_users` MODIFY `role` VARCHAR(255) NOT NULL");
    $pdo->exec("ALTER TABLE `project_has_groups` MODIFY `role` VARCHAR(255) NOT NULL");
}

function version_112(PDO $pdo)
{
    $pdo->exec('ALTER TABLE columns ADD COLUMN hide_in_dashboard INT DEFAULT 0 NOT NULL');
}

function version_111(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE tags (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            project_id INT NOT NULL,
            UNIQUE(project_id, name),
            PRIMARY KEY(id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE task_has_tags (
            task_id INT NOT NULL,
            tag_id INT NOT NULL,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(tag_id) REFERENCES tags(id) ON DELETE CASCADE,
            UNIQUE(tag_id, task_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_110(PDO $pdo)
{
    $pdo->exec("ALTER TABLE user_has_notifications DROP FOREIGN KEY `user_has_notifications_ibfk_1`");
    $pdo->exec("ALTER TABLE user_has_notifications DROP FOREIGN KEY `user_has_notifications_ibfk_2`");
    $pdo->exec("DROP INDEX `project_id` ON user_has_notifications");
    $pdo->exec("ALTER TABLE user_has_notifications DROP KEY `user_id`");
    $pdo->exec("CREATE UNIQUE INDEX `user_has_notifications_unique_idx` ON `user_has_notifications` (`user_id`, `project_id`)");
    $pdo->exec("ALTER TABLE user_has_notifications ADD CONSTRAINT user_has_notifications_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
    $pdo->exec("ALTER TABLE user_has_notifications ADD CONSTRAINT user_has_notifications_ibfk_2 FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE");
}

function version_109(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255)");
}

function version_108(PDO $pdo)
{
    $pdo->exec("ALTER TABLE user_has_metadata ADD COLUMN changed_by INT DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE user_has_metadata ADD COLUMN changed_on INT DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE project_has_metadata ADD COLUMN changed_by INT DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE project_has_metadata ADD COLUMN changed_on INT DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE task_has_metadata ADD COLUMN changed_by INT DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE task_has_metadata ADD COLUMN changed_on INT DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE settings ADD COLUMN changed_by INT DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE settings ADD COLUMN changed_on INT DEFAULT 0 NOT NULL");
}

function version_107(PDO $pdo)
{
    $pdo->exec("UPDATE project_activities SET event_name='task.file.create' WHERE event_name='file.create'");
}

function version_106(PDO $pdo)
{
    $pdo->exec('RENAME TABLE files TO task_has_files');

    $pdo->exec("
        CREATE TABLE project_has_files (
            `id` INT NOT NULL AUTO_INCREMENT,
            `project_id` INT NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `path` VARCHAR(255) NOT NULL,
            `is_image` TINYINT(1) DEFAULT 0,
            `size` INT DEFAULT 0 NOT NULL,
            `user_id` INT DEFAULT 0 NOT NULL,
            `date` INT DEFAULT 0 NOT NULL,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            PRIMARY KEY(id)
        )  ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_105(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1");
}

function version_104(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_external_links (
            id INT NOT NULL AUTO_INCREMENT,
            link_type VARCHAR(100) NOT NULL,
            dependency VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            date_creation INT NOT NULL,
            date_modification INT NOT NULL,
            task_id INT NOT NULL,
            creator_id INT DEFAULT 0,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            PRIMARY KEY(id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_103(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_default INT DEFAULT 0");
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_start INT DEFAULT 0");
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_end INT DEFAULT 3");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN priority INT DEFAULT 0");
}

function version_102(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN owner_id INT DEFAULT 0");
}

function version_101(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE password_reset (
            token VARCHAR(80) PRIMARY KEY,
            user_id INT NOT NULL,
            date_expiration INT NOT NULL,
            date_creation INT NOT NULL,
            ip VARCHAR(45) NOT NULL,
            user_agent VARCHAR(255) NOT NULL,
            is_active TINYINT(1) NOT NULL,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("INSERT INTO settings VALUES ('password_reset', '1')");
}

function version_100(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `actions` MODIFY `action_name` VARCHAR(255)');
}

function version_99(PDO $pdo)
{
    $rq = $pdo->prepare('SELECT * FROM actions');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC) ?: array();

    $rq = $pdo->prepare('UPDATE actions SET action_name=? WHERE id=?');

    foreach ($rows as $row) {
        if ($row['action_name'] === 'TaskAssignCurrentUser' && $row['event_name'] === 'task.move.column') {
            $row['action_name'] = '\Kanboard\Action\TaskAssignCurrentUserColumn';
        } elseif ($row['action_name'] === 'TaskClose' && $row['event_name'] === 'task.move.column') {
            $row['action_name'] = '\Kanboard\Action\TaskCloseColumn';
        } elseif ($row['action_name'] === 'TaskLogMoveAnotherColumn') {
            $row['action_name'] = '\Kanboard\Action\CommentCreationMoveTaskColumn';
        } elseif ($row['action_name'][0] !== '\\') {
            $row['action_name'] = '\Kanboard\Action\\'.$row['action_name'];
        }

        $rq->execute(array($row['action_name'], $row['id']));
    }
}

function version_98(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `users` MODIFY `language` VARCHAR(5)');
}

function version_97(PDO $pdo)
{
    $pdo->exec("ALTER TABLE `users` ADD COLUMN `role` VARCHAR(25) NOT NULL DEFAULT '".Role::APP_USER."'");

    $rq = $pdo->prepare('SELECT * FROM `users`');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC) ?: array();

    $rq = $pdo->prepare('UPDATE `users` SET `role`=? WHERE `id`=?');

    foreach ($rows as $row) {
        $role = Role::APP_USER;

        if ($row['is_admin'] == 1) {
            $role = Role::APP_ADMIN;
        } else if ($row['is_project_admin']) {
            $role = Role::APP_MANAGER;
        }

        $rq->execute(array($role, $row['id']));
    }

    $pdo->exec('ALTER TABLE `users` DROP COLUMN `is_admin`');
    $pdo->exec('ALTER TABLE `users` DROP COLUMN `is_project_admin`');
}

function version_96(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_groups (
            `group_id` INT NOT NULL,
            `project_id` INT NOT NULL,
            `role` VARCHAR(25) NOT NULL,
            FOREIGN KEY(group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(group_id, project_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("ALTER TABLE `project_has_users` ADD COLUMN `role` VARCHAR(25) NOT NULL DEFAULT '".Role::PROJECT_VIEWER."'");

    $rq = $pdo->prepare('SELECT * FROM project_has_users');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC) ?: array();

    $rq = $pdo->prepare('UPDATE `project_has_users` SET `role`=? WHERE `id`=?');

    foreach ($rows as $row) {
        $rq->execute(array(
            $row['is_owner'] == 1 ? Role::PROJECT_MANAGER : Role::PROJECT_MEMBER,
            $row['id'],
        ));
    }

    $pdo->exec('ALTER TABLE `project_has_users` DROP COLUMN `is_owner`');
    $pdo->exec('ALTER TABLE `project_has_users` DROP COLUMN `id`');
}

function version_95(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE `groups` (
            id INT NOT NULL AUTO_INCREMENT,
            external_id VARCHAR(255) DEFAULT '',
            name VARCHAR(100) NOT NULL UNIQUE,
            PRIMARY KEY(id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE group_has_users (
            group_id INT NOT NULL,
            user_id INT NOT NULL,
            FOREIGN KEY(group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(group_id, user_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_94(PDO $pdo)
{
    $pdo->exec('ALTER TABLE `projects` DROP INDEX `name`');
    $pdo->exec('ALTER TABLE `projects` DROP INDEX `name_2`');
}

function version_93(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE user_has_metadata (
            user_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(user_id, name)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE project_has_metadata (
            project_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, name)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE task_has_metadata (
            task_id INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            UNIQUE(task_id, name)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("DROP TABLE project_integrations");

    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_server'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_domain'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_username'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_password'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_nickname'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_jabber_room'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_hipchat'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_hipchat_api_url'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_hipchat_room_id'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_hipchat_room_token'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_slack_webhook'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_slack_webhook_url'");
    $pdo->exec("DELETE FROM settings WHERE `option`='integration_slack_webhook_channel'");
}

function version_92(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_notification_types (
            id INT NOT NULL AUTO_INCREMENT,
            project_id INT NOT NULL,
            notification_type VARCHAR(50) NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, notification_type)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_91(PDO $pdo)
{
    $pdo->exec("ALTER TABLE custom_filters ADD COLUMN `append` TINYINT(1) DEFAULT 0");
}

function version_90(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks MODIFY date_due BIGINT");
    $pdo->exec("ALTER TABLE tasks MODIFY date_creation BIGINT");
    $pdo->exec("ALTER TABLE tasks MODIFY date_completed BIGINT");
    $pdo->exec("ALTER TABLE tasks MODIFY date_started BIGINT");
    $pdo->exec("ALTER TABLE tasks MODIFY date_moved BIGINT");
    $pdo->exec("ALTER TABLE comments MODIFY date_creation BIGINT");
    $pdo->exec("ALTER TABLE last_logins MODIFY date_creation BIGINT");
    $pdo->exec("ALTER TABLE project_activities MODIFY date_creation BIGINT");
    $pdo->exec("ALTER TABLE projects MODIFY last_modified BIGINT");
    $pdo->exec("ALTER TABLE remember_me MODIFY date_creation BIGINT");
    $pdo->exec('ALTER TABLE files MODIFY `date` BIGINT');
    $pdo->exec('ALTER TABLE transitions MODIFY `date` BIGINT');
    $pdo->exec('ALTER TABLE subtask_time_tracking MODIFY `start` BIGINT');
    $pdo->exec('ALTER TABLE subtask_time_tracking MODIFY `end` BIGINT');
    $pdo->exec('ALTER TABLE users MODIFY `lock_expiration_date` BIGINT');
}

function version_89(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE user_has_unread_notifications (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            date_creation BIGINT NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            event_data TEXT NOT NULL,
            PRIMARY KEY(id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE user_has_notification_types (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            notification_type VARCHAR(50),
            PRIMARY KEY(id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('CREATE UNIQUE INDEX user_has_notification_types_user_idx ON user_has_notification_types(user_id, notification_type)');

    // Migrate people who have notification enabled before
    $rq = $pdo->prepare('SELECT id FROM users WHERE notifications_enabled=1');
    $rq->execute();
    $user_ids = $rq->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($user_ids as $user_id) {
        $rq = $pdo->prepare('INSERT INTO user_has_notification_types (user_id, notification_type) VALUES (?, ?)');
        $rq->execute(array($user_id, 'email'));
    }
}

function version_88(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE custom_filters (
            id INT NOT NULL AUTO_INCREMENT,
            filter VARCHAR(100) NOT NULL,
            project_id INT NOT NULL,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            is_shared TINYINT(1) DEFAULT 0,
            PRIMARY KEY(id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_87(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE plugin_schema_versions (
            plugin VARCHAR(80) NOT NULL,
            version INT NOT NULL DEFAULT 0,
            PRIMARY KEY(plugin)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_86(PDO $pdo)
{
    $pdo->exec("ALTER TABLE swimlanes ADD COLUMN description TEXT");
}

function version_85(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN gitlab_id INT");
}

function version_84(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN start_date VARCHAR(10) DEFAULT ''");
    $pdo->exec("ALTER TABLE projects ADD COLUMN end_date VARCHAR(10) DEFAULT ''");
}

function version_83(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_project_admin INT DEFAULT 0");
}

function version_82(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN nb_failed_login INT DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN lock_expiration_date INT DEFAULT 0");
}

function version_81(PDO $pdo)
{
    $pdo->exec("INSERT INTO settings VALUES ('subtask_time_tracking', '1')");
    $pdo->exec("INSERT INTO settings VALUES ('cfd_include_closed_tasks', '1')");
}

function version_80(PDO $pdo)
{
    $pdo->exec("INSERT INTO settings VALUES ('default_color', 'yellow')");
}

function version_79(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_daily_stats (
            id INT NOT NULL AUTO_INCREMENT,
            day CHAR(10) NOT NULL,
            project_id INT NOT NULL,
            avg_lead_time INT NOT NULL DEFAULT 0,
            avg_cycle_time INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('CREATE UNIQUE INDEX project_daily_stats_idx ON project_daily_stats(day, project_id)');

    $pdo->exec('RENAME TABLE project_daily_summaries TO project_daily_column_stats');
}

function version_78(PDO $pdo)
{
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN slack_webhook_channel VARCHAR(255) DEFAULT ''");
    $pdo->exec("INSERT INTO settings VALUES ('integration_slack_webhook_channel', '')");
}

function version_77(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users DROP COLUMN `default_project_id`');
}

function version_76(PDO $pdo)
{
    $pdo->exec("DELETE FROM `settings` WHERE `option`='subtask_time_tracking'");
}

function version_75(PDO $pdo)
{
    $pdo->exec('ALTER TABLE comments DROP FOREIGN KEY comments_ibfk_2');
    $pdo->exec('ALTER TABLE comments MODIFY task_id INT NOT NULL');
    $pdo->exec('ALTER TABLE comments CHANGE COLUMN `user_id` `user_id` INT DEFAULT 0');
    $pdo->exec('ALTER TABLE comments CHANGE COLUMN `date` `date_creation` INT NOT NULL');
}

function version_74(PDO $pdo)
{
    $pdo->exec('ALTER TABLE project_has_categories MODIFY project_id INT NOT NULL');
    $pdo->exec('ALTER TABLE project_has_categories MODIFY name VARCHAR(255) NOT NULL');

    $pdo->exec('ALTER TABLE actions MODIFY project_id INT NOT NULL');
    $pdo->exec('ALTER TABLE actions MODIFY event_name VARCHAR(50) NOT NULL');
    $pdo->exec('ALTER TABLE actions MODIFY action_name VARCHAR(50) NOT NULL');

    $pdo->exec('ALTER TABLE action_has_params MODIFY action_id INT NOT NULL');
    $pdo->exec('ALTER TABLE action_has_params MODIFY name VARCHAR(50) NOT NULL');
    $pdo->exec('ALTER TABLE action_has_params MODIFY value VARCHAR(50) NOT NULL');

    $pdo->exec('ALTER TABLE files MODIFY name VARCHAR(255) NOT NULL');
    $pdo->exec('ALTER TABLE files MODIFY task_id INT NOT NULL');

    $pdo->exec('ALTER TABLE subtasks MODIFY title VARCHAR(255) NOT NULL');

    $pdo->exec('ALTER TABLE tasks MODIFY project_id INT NOT NULL');
    $pdo->exec('ALTER TABLE tasks MODIFY column_id INT NOT NULL');

    $pdo->exec('ALTER TABLE columns MODIFY title VARCHAR(255) NOT NULL');
    $pdo->exec('ALTER TABLE columns MODIFY project_id INT NOT NULL');

    $pdo->exec('ALTER TABLE project_has_users MODIFY project_id INT NOT NULL');
    $pdo->exec('ALTER TABLE project_has_users MODIFY user_id INT NOT NULL');

    $pdo->exec('ALTER TABLE projects MODIFY name VARCHAR(255) NOT NULL UNIQUE');

    $pdo->exec('ALTER TABLE users MODIFY username VARCHAR(50) NOT NULL');

    $pdo->exec('ALTER TABLE user_has_notifications MODIFY project_id INT NOT NULL');
    $pdo->exec('ALTER TABLE user_has_notifications MODIFY user_id INT NOT NULL');
}

function version_73(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN notifications_filter INT DEFAULT 4");
}

function version_72(PDO $pdo)
{
    $pdo->exec('ALTER TABLE files MODIFY name VARCHAR(255)');
}

function version_71(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO `settings` VALUES (?, ?)');
    $rq->execute(array('webhook_url', ''));

    $pdo->exec("DELETE FROM `settings` WHERE `option`='webhook_url_task_creation'");
    $pdo->exec("DELETE FROM `settings` WHERE `option`='webhook_url_task_modification'");
}

function version_70(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN token VARCHAR(255) DEFAULT ''");
}

function version_69(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('calendar_user_subtasks_time_tracking', 0));
    $rq->execute(array('calendar_user_tasks', 'date_started'));
    $rq->execute(array('calendar_project_tasks', 'date_started'));

    $pdo->exec("DELETE FROM `settings` WHERE `option`='subtask_forecast'");
}

function version_68(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_jabber', '0'));
    $rq->execute(array('integration_jabber_server', ''));
    $rq->execute(array('integration_jabber_domain', ''));
    $rq->execute(array('integration_jabber_username', ''));
    $rq->execute(array('integration_jabber_password', ''));
    $rq->execute(array('integration_jabber_nickname', 'kanboard'));
    $rq->execute(array('integration_jabber_room', ''));

    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber INTEGER DEFAULT '0'");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_server VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_domain VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_username VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_password VARCHAR(255) DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_nickname VARCHAR(255) DEFAULT 'kanboard'");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_room VARCHAR(255) DEFAULT ''");
}

function version_67(PDO $pdo)
{
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_status INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_trigger INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_factor INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_timeframe INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_basedate INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_parent INTEGER');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_child INTEGER');
}

function version_66(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN identifier VARCHAR(50) DEFAULT ''");
}

function version_65(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_integrations (
            `id` INT NOT NULL AUTO_INCREMENT,
            `project_id` INT NOT NULL UNIQUE,
            `hipchat` TINYINT(1) DEFAULT 0,
            `hipchat_api_url` VARCHAR(255) DEFAULT 'https://api.hipchat.com',
            `hipchat_room_id` VARCHAR(255),
            `hipchat_room_token` VARCHAR(255),
            `slack` TINYINT(1) DEFAULT 0,
            `slack_webhook_url` VARCHAR(255),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            PRIMARY KEY(id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_64(PDO $pdo)
{
    $pdo->exec('ALTER TABLE project_daily_summaries ADD COLUMN score INT NOT NULL DEFAULT 0');
}

function version_63(PDO $pdo)
{
    $pdo->exec('ALTER TABLE project_has_categories ADD COLUMN description TEXT');
}

function version_62(PDO $pdo)
{
    $pdo->exec('ALTER TABLE files ADD COLUMN `date` INT NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN `user_id` INT NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN `size` INT NOT NULL DEFAULT 0');
}

function version_61(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_activated TINYINT(1) DEFAULT 0');
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_secret CHAR(16)');
}

function version_60(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_gravatar', '0'));
}

function version_59(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_hipchat', '0'));
    $rq->execute(array('integration_hipchat_api_url', 'https://api.hipchat.com'));
    $rq->execute(array('integration_hipchat_room_id', ''));
    $rq->execute(array('integration_hipchat_room_token', ''));
}

function version_58(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_slack_webhook', '0'));
    $rq->execute(array('integration_slack_webhook_url', ''));
}

function version_57(PDO $pdo)
{
    $pdo->exec('CREATE TABLE currencies (`currency` CHAR(3) NOT NULL UNIQUE, `rate` FLOAT DEFAULT 0) ENGINE=InnoDB CHARSET=utf8');

    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_currency', 'USD'));
}

function version_56(PDO $pdo)
{
    $pdo->exec('CREATE TABLE transitions (
        `id` INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `project_id` INT NOT NULL,
        `task_id` INT NOT NULL,
        `src_column_id` INT NOT NULL,
        `dst_column_id` INT NOT NULL,
        `date` INT NOT NULL,
        `time_spent` INT DEFAULT 0,
        FOREIGN KEY(src_column_id) REFERENCES columns(id) ON DELETE CASCADE,
        FOREIGN KEY(dst_column_id) REFERENCES columns(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8');

    $pdo->exec("CREATE INDEX transitions_task_index ON transitions(task_id)");
    $pdo->exec("CREATE INDEX transitions_project_index ON transitions(project_id)");
    $pdo->exec("CREATE INDEX transitions_user_index ON transitions(user_id)");
}

function version_55(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_forecast', '0'));
}

function version_54(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_stylesheet', ''));
}

function version_53(PDO $pdo)
{
    $pdo->exec("ALTER TABLE subtask_time_tracking ADD COLUMN time_spent FLOAT DEFAULT 0");
}

function version_49(PDO $pdo)
{
    $pdo->exec('ALTER TABLE subtasks ADD COLUMN position INTEGER DEFAULT 1');

    $task_id = 0;
    $position = 1;
    $urq = $pdo->prepare('UPDATE subtasks SET position=? WHERE id=?');

    $rq = $pdo->prepare('SELECT * FROM subtasks ORDER BY task_id, id ASC');
    $rq->execute();

    foreach ($rq->fetchAll(PDO::FETCH_ASSOC) as $subtask) {
        if ($task_id != $subtask['task_id']) {
            $position = 1;
            $task_id = $subtask['task_id'];
        }

        $urq->execute(array($position, $subtask['id']));
        $position++;
    }
}

function version_48(PDO $pdo)
{
    $pdo->exec('RENAME TABLE task_has_files TO files');
    $pdo->exec('RENAME TABLE task_has_subtasks TO subtasks');
}

function version_47(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN description TEXT');
}

function version_46(PDO $pdo)
{
    $pdo->exec("CREATE TABLE links (
        id INT NOT NULL AUTO_INCREMENT,
        label VARCHAR(255) NOT NULL,
        opposite_id INT DEFAULT 0,
        PRIMARY KEY(id),
        UNIQUE(label)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE TABLE task_has_links (
        id INT NOT NULL AUTO_INCREMENT,
        link_id INT NOT NULL,
        task_id INT NOT NULL,
        opposite_task_id INT NOT NULL,
        FOREIGN KEY(link_id) REFERENCES links(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY(opposite_task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        PRIMARY KEY(id)
    ) ENGINE=InnoDB CHARSET=utf8");

    $pdo->exec("CREATE INDEX task_has_links_task_index ON task_has_links(task_id)");
    $pdo->exec("CREATE UNIQUE INDEX task_has_links_unique ON task_has_links(link_id, task_id, opposite_task_id)");

    $rq = $pdo->prepare('INSERT INTO links (label, opposite_id) VALUES (?, ?)');
    
    # ID cannot be known at time of record creation so we have to update it after the fact
    # On MariaDB clusters auto-increment size is normally != 1, so relying on increments of 1 would break
    $arq = $pdo->prepare('UPDATE links SET opposite_id=? WHERE label=?');

    $rq->execute(array('relates to', 0));

    $rq->execute(array('blocks', 0));
    $rq->execute(array('is blocked by', get_last_insert_id($pdo)));
    $arq->execute(array(get_last_insert_id($pdo), 'blocks'));

    $rq->execute(array('duplicates', 0));
    $rq->execute(array('is duplicated by', get_last_insert_id($pdo)));
    $arq->execute(array(get_last_insert_id($pdo), 'duplicates'));

    $rq->execute(array('is a parent of', 0));
    $rq->execute(array('is a child of', get_last_insert_id($pdo)));
    $arq->execute(array(get_last_insert_id($pdo), 'is a parent of'));

    $rq->execute(array('is a milestone of', 0));
    $rq->execute(array('targets milestone', get_last_insert_id($pdo)));
    $arq->execute(array(get_last_insert_id($pdo), 'is a milestone of'));

    $rq->execute(array('is fixed by', 0));
    $rq->execute(array('fixes', get_last_insert_id($pdo)));
    $arq->execute(array(get_last_insert_id($pdo), 'is fixed by'));
}

function version_45(PDO $pdo)
{
    $pdo->exec('ALTER TABLE tasks ADD COLUMN date_moved INT DEFAULT 0');

    /* Update tasks.date_moved from project_activities table if tasks.date_moved = null or 0.
     * We take max project_activities.date_creation where event_name in task.create','task.move.column
     * since creation date is always less than task moves
     */
    $pdo->exec("UPDATE tasks
                SET date_moved = (
                    SELECT md
                    FROM (
                        SELECT task_id, max(date_creation) md
                        FROM project_activities
                        WHERE event_name IN ('task.create', 'task.move.column')
                        GROUP BY task_id
                    ) src
                    WHERE id = src.task_id
                )
                WHERE (date_moved IS NULL OR date_moved = 0) AND id IN (
                    SELECT task_id
                    FROM (
                        SELECT task_id, max(date_creation) md
                        FROM project_activities
                        WHERE event_name IN ('task.create', 'task.move.column')
                        GROUP BY task_id
                    ) src
                )");

    // If there is no activities for some tasks use the date_creation
    $pdo->exec("UPDATE tasks SET date_moved = date_creation WHERE date_moved IS NULL OR date_moved = 0");
}

function version_44(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN disable_login_form TINYINT(1) DEFAULT 0');
}

function version_43(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_restriction', '0'));
    $rq->execute(array('subtask_time_tracking', '0'));

    $pdo->exec("
        CREATE TABLE subtask_time_tracking (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            subtask_id INT NOT NULL,
            start INT DEFAULT 0,
            end INT DEFAULT 0,
            PRIMARY KEY(id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(subtask_id) REFERENCES task_has_subtasks(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_42(PDO $pdo)
{
    $pdo->exec('ALTER TABLE columns ADD COLUMN description TEXT');
}

function version_41(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN timezone VARCHAR(50)');
    $pdo->exec('ALTER TABLE users ADD COLUMN language CHAR(5)');
}

function version_40(PDO $pdo)
{
    // Avoid some full table scans
    $pdo->exec('CREATE INDEX users_admin_idx ON users(is_admin)');
    $pdo->exec('CREATE INDEX columns_project_idx ON columns(project_id)');
    $pdo->exec('CREATE INDEX tasks_project_idx ON tasks(project_id)');
    $pdo->exec('CREATE INDEX swimlanes_project_idx ON swimlanes(project_id)');
    $pdo->exec('CREATE INDEX categories_project_idx ON project_has_categories(project_id)');
    $pdo->exec('CREATE INDEX subtasks_task_idx ON task_has_subtasks(task_id)');
    $pdo->exec('CREATE INDEX files_task_idx ON task_has_files(task_id)');
    $pdo->exec('CREATE INDEX comments_task_idx ON comments(task_id)');

    // Set the ownership for all private projects
    $rq = $pdo->prepare('SELECT id FROM projects WHERE is_private=1');
    $rq->execute();
    $project_ids = $rq->fetchAll(PDO::FETCH_COLUMN, 0);

    $rq = $pdo->prepare('UPDATE project_has_users SET is_owner=1 WHERE project_id=?');

    foreach ($project_ids as $project_id) {
        $rq->execute(array($project_id));
    }
}

function version_39(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('project_categories', ''));
}

function version_38(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE swimlanes (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(200) NOT NULL,
            position INT DEFAULT 1,
            is_active INT DEFAULT 1,
            project_id INT,
            PRIMARY KEY(id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE (name, project_id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('ALTER TABLE tasks ADD COLUMN swimlane_id INT DEFAULT 0');
    $pdo->exec("ALTER TABLE projects ADD COLUMN default_swimlane VARCHAR(200) DEFAULT 'Default swimlane'");
    $pdo->exec("ALTER TABLE projects ADD COLUMN show_default_swimlane INT DEFAULT 1");
}

function version_37(PDO $pdo)
{
    $pdo->exec("ALTER TABLE project_has_users ADD COLUMN is_owner TINYINT(1) DEFAULT '0'");
}

function version_36(PDO $pdo)
{
    $pdo->exec('ALTER TABLE tasks MODIFY title VARCHAR(255) NOT NULL');
}

function version_35(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_daily_summaries (
            id INT NOT NULL AUTO_INCREMENT,
            day CHAR(10) NOT NULL,
            project_id INT NOT NULL,
            column_id INT NOT NULL,
            total INT NOT NULL DEFAULT 0,
            PRIMARY KEY(id),
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('CREATE UNIQUE INDEX project_daily_column_stats_idx ON project_daily_summaries(day, project_id, column_id)');
}

function version_34(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_everybody_allowed TINYINT(1) DEFAULT '0'");
}

function version_33(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_activities (
            id INT NOT NULL AUTO_INCREMENT,
            date_creation INT NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            creator_id INT,
            project_id INT,
            task_id INT,
            data TEXT,
            PRIMARY KEY(id),
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec('DROP TABLE task_has_events');
    $pdo->exec('DROP TABLE comment_has_events');
    $pdo->exec('DROP TABLE subtask_has_events');
}

function version_32(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_started INTEGER");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_spent FLOAT DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_estimated FLOAT DEFAULT 0");

    $pdo->exec("ALTER TABLE task_has_subtasks MODIFY time_estimated FLOAT");
    $pdo->exec("ALTER TABLE task_has_subtasks MODIFY time_spent FLOAT");
}

function version_31(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_private TINYINT(1) DEFAULT '0'");
}

function version_30(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_date_format', 'm/d/Y'));
}

function version_29(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE settings (
            `option` VARCHAR(100) PRIMARY KEY,
            `value` VARCHAR(255) DEFAULT ''
        )
    ");

    // Migrate old config parameters
    $rq = $pdo->prepare('SELECT * FROM config');
    $rq->execute();
    $parameters = $rq->fetch(PDO::FETCH_ASSOC);

    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('board_highlight_period', defined('RECENT_TASK_PERIOD') ? RECENT_TASK_PERIOD : 48*60*60));
    $rq->execute(array('board_public_refresh_interval', defined('BOARD_PUBLIC_CHECK_INTERVAL') ? BOARD_PUBLIC_CHECK_INTERVAL : 60));
    $rq->execute(array('board_private_refresh_interval', defined('BOARD_CHECK_INTERVAL') ? BOARD_CHECK_INTERVAL : 10));
    $rq->execute(array('board_columns', $parameters['default_columns']));
    $rq->execute(array('webhook_url_task_creation', $parameters['webhooks_url_task_creation']));
    $rq->execute(array('webhook_url_task_modification', $parameters['webhooks_url_task_modification']));
    $rq->execute(array('webhook_token', $parameters['webhooks_token']));
    $rq->execute(array('api_token', $parameters['api_token']));
    $rq->execute(array('application_language', $parameters['language']));
    $rq->execute(array('application_timezone', $parameters['timezone']));
    $rq->execute(array('application_url', defined('KANBOARD_URL') ? KANBOARD_URL : ''));

    $pdo->exec('DROP TABLE config');
}

function version_28(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN reference VARCHAR(50) DEFAULT ''");
    $pdo->exec("ALTER TABLE comments ADD COLUMN reference VARCHAR(50) DEFAULT ''");

    $pdo->exec('CREATE INDEX tasks_reference_idx ON tasks(reference)');
    $pdo->exec('CREATE INDEX comments_reference_idx ON comments(reference)');
}

function version_27(PDO $pdo)
{
    $pdo->exec('CREATE UNIQUE INDEX users_username_idx ON users(username)');
}

function version_26(PDO $pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN default_columns VARCHAR(255) DEFAULT ''");
}

function version_25(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_events (
            id INT NOT NULL AUTO_INCREMENT,
            date_creation INT NOT NULL,
            event_name TEXT NOT NULL,
            creator_id INT,
            project_id INT,
            task_id INT,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE subtask_has_events (
            id INT NOT NULL AUTO_INCREMENT,
            date_creation INT NOT NULL,
            event_name TEXT NOT NULL,
            creator_id INT,
            project_id INT,
            subtask_id INT,
            task_id INT,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(subtask_id) REFERENCES task_has_subtasks(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE comment_has_events (
            id INT NOT NULL AUTO_INCREMENT,
            date_creation INT NOT NULL,
            event_name TEXT NOT NULL,
            creator_id INT,
            project_id INT,
            comment_id INT,
            task_id INT,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(comment_id) REFERENCES comments(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");
}

function version_24(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_public TINYINT(1) DEFAULT '0'");
}

function version_23(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN notifications_enabled TINYINT(1) DEFAULT '0'");

    $pdo->exec("
        CREATE TABLE user_has_notifications (
            user_id INT,
            project_id INT,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, user_id)
        );
    ");
}

function version_22(PDO $pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_modification VARCHAR(255)");
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_creation VARCHAR(255)");
}

function version_21(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN creator_id INTEGER DEFAULT '0'");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_modification INTEGER DEFAULT '0'");
}

function version_20(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN github_id VARCHAR(30)");
}

function version_19(PDO $pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN api_token VARCHAR(255) DEFAULT ''");
    $pdo->exec("UPDATE config SET api_token='".Token::getToken()."'");
}

function version_18(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_subtasks (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255),
            status INT DEFAULT 0,
            time_estimated INT DEFAULT 0,
            time_spent INT DEFAULT 0,
            task_id INT NOT NULL,
            user_id INT,
            PRIMARY KEY (id),
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_17(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_files (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(50),
            path VARCHAR(255),
            is_image TINYINT(1) DEFAULT 0,
            task_id INT,
            PRIMARY KEY (id),
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_16(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_categories (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(255),
            project_id INT,
            PRIMARY KEY (id),
            UNIQUE KEY `idx_project_category` (project_id, name),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8"
    );

    $pdo->exec("ALTER TABLE tasks ADD COLUMN category_id INT DEFAULT 0");
}

function version_15(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN last_modified INT DEFAULT 0");
}

function version_14(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN name VARCHAR(255)");
    $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255)");
    $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(30)");
}

function version_13(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_ldap_user TINYINT(1) DEFAULT 0");
}

function version_12(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE remember_me (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT,
            ip VARCHAR(45),
            user_agent VARCHAR(255),
            token VARCHAR(255),
            sequence VARCHAR(255),
            expiration INT,
            date_creation INT,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8"
    );

    $pdo->exec("
        CREATE TABLE last_logins (
            id INT NOT NULL AUTO_INCREMENT,
            auth_type VARCHAR(25),
            user_id INT,
            ip VARCHAR(45),
            user_agent VARCHAR(255),
            date_creation INT,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            PRIMARY KEY (id),
            INDEX (user_id)
        ) ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_1(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE config (
            language CHAR(5) DEFAULT 'en_US',
            webhooks_token VARCHAR(255) DEFAULT '',
            timezone VARCHAR(50) DEFAULT 'UTC'
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE users (
            id INT NOT NULL AUTO_INCREMENT,
            username VARCHAR(50),
            password VARCHAR(255),
            is_admin TINYINT DEFAULT 0,
            default_project_id INT DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE projects (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(50) UNIQUE,
            is_active TINYINT DEFAULT 1,
            token VARCHAR(255),
            PRIMARY KEY (id)
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE project_has_users (
            id INT NOT NULL AUTO_INCREMENT,
            project_id INT,
            user_id INT,
            PRIMARY KEY (id),
            UNIQUE KEY `idx_project_user` (project_id, user_id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE columns (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255),
            position INT NOT NULL,
            project_id INT NOT NULL,
            task_limit INT DEFAULT '0',
            UNIQUE KEY `idx_title_project` (title, project_id),
            PRIMARY KEY (id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE tasks (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255),
            description TEXT,
            date_creation INT,
            date_completed INT,
            date_due INT,
            color_id VARCHAR(50),
            project_id INT,
            column_id INT,
            owner_id INT DEFAULT '0',
            position INT,
            score INT,
            is_active TINYINT DEFAULT 1,
            PRIMARY KEY (id),
            INDEX `idx_task_active` (is_active),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE comments (
            id INT NOT NULL AUTO_INCREMENT,
            task_id INT,
            user_id INT,
            `date` INT,
            comment TEXT,
            PRIMARY KEY (id),
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE actions (
            id INT NOT NULL AUTO_INCREMENT,
            project_id INT,
            event_name VARCHAR(50),
            action_name VARCHAR(50),
            PRIMARY KEY (id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        CREATE TABLE action_has_params (
            id INT NOT NULL AUTO_INCREMENT,
            action_id INT,
            name VARCHAR(50),
            value VARCHAR(50),
            PRIMARY KEY (id),
            FOREIGN KEY(action_id) REFERENCES actions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8
    ");

    $pdo->exec("
        INSERT INTO users
        (username, password, is_admin)
        VALUES ('admin', '".\password_hash('admin', PASSWORD_BCRYPT)."', '1')
    ");

    $pdo->exec("
        INSERT INTO config
        (webhooks_token)
        VALUES ('".Token::getToken()."')
    ");
}
