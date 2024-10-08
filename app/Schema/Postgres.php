<?php

namespace Schema;

require_once __DIR__.'/Migration.php';

use PDO;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

const VERSION = 117;

function version_117(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "comments" ADD COLUMN "visibility" VARCHAR(25) NOT NULL DEFAULT \''.Role::APP_USER."'");
}

function version_116(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN theme TEXT DEFAULT 'light' NOT NULL");
}

function version_115(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "projects" ADD COLUMN enable_global_tags BOOLEAN DEFAULT TRUE');
}

function version_114(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "swimlanes" ADD COLUMN task_limit INTEGER DEFAULT 0');
}

function version_113(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "projects" ADD COLUMN task_limit INTEGER DEFAULT 0');
}

function version_112(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "projects" ADD COLUMN per_swimlane_task_limits BOOLEAN DEFAULT FALSE');
}

function version_111(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "tags" ADD COLUMN "color_id" VARCHAR(50) DEFAULT NULL');
}

function version_110(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "project_has_categories" ADD COLUMN "color_id" VARCHAR(50) DEFAULT NULL');
}

function version_109(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "users" ALTER COLUMN "language" TYPE VARCHAR(11)');
}

function version_108(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "projects" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "projects" ALTER COLUMN "email" TYPE TEXT');
    $pdo->exec('ALTER TABLE "action_has_params" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "action_has_params" ALTER COLUMN "value" TYPE TEXT');
    $pdo->exec('ALTER TABLE "actions" ALTER COLUMN "event_name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "actions" ALTER COLUMN "action_name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "comments" ALTER COLUMN "reference" TYPE TEXT');
    $pdo->exec('ALTER TABLE "custom_filters" ALTER COLUMN "filter" TYPE TEXT');
    $pdo->exec('ALTER TABLE "custom_filters" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "groups" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "project_activities" ALTER COLUMN "event_name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "project_has_files" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "project_has_files" ALTER COLUMN "path" TYPE TEXT');
    $pdo->exec('ALTER TABLE "subtasks" ALTER COLUMN "title" TYPE TEXT');
    $pdo->exec('ALTER TABLE "swimlanes" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "task_has_external_links" ALTER COLUMN "title" TYPE TEXT');
    $pdo->exec('ALTER TABLE "task_has_external_links" ALTER COLUMN "url" TYPE TEXT');
    $pdo->exec('ALTER TABLE "task_has_files" ALTER COLUMN "name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "task_has_files" ALTER COLUMN "path" TYPE TEXT');
    $pdo->exec('ALTER TABLE "tasks" ALTER COLUMN "title" TYPE TEXT');
    $pdo->exec('ALTER TABLE "tasks" ALTER COLUMN "reference" TYPE TEXT');
    $pdo->exec('ALTER TABLE "user_has_unread_notifications" ALTER COLUMN "event_name" TYPE TEXT');
    $pdo->exec('ALTER TABLE "users" ALTER COLUMN "username" TYPE TEXT');
    $pdo->exec('ALTER TABLE "users" ALTER COLUMN "filter" TYPE TEXT');
}

function version_107(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "users" ADD COLUMN filter VARCHAR(255) DEFAULT NULL');
}

function version_106(PDO $pdo)
{
    $pdo->exec("CREATE TABLE sessions (
        id TEXT PRIMARY KEY,
        expire_at INTEGER NOT NULL,
        data TEXT DEFAULT ''
    )");
}

function version_105(PDO $pdo)
{
    $pdo->exec('CREATE TABLE predefined_task_descriptions (
        id SERIAL PRIMARY KEY,
        project_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT NOT NULL,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
    )');
}

function version_104(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects DROP COLUMN is_everybody_allowed');
}

function version_103(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN predefined_email_subjects TEXT');
}

function version_102(PDO $pdo)
{
    $pdo->exec('ALTER TABLE column_has_move_restrictions ADD COLUMN only_assigned BOOLEAN DEFAULT FALSE');
}

function version_101(PDO $pdo)
{
    migrate_default_swimlane($pdo);

    $pdo->exec('ALTER TABLE "projects" DROP COLUMN "default_swimlane"');
    $pdo->exec('ALTER TABLE "projects" DROP COLUMN "show_default_swimlane"');
    $pdo->exec('ALTER TABLE "tasks" ALTER COLUMN "swimlane_id" SET NOT NULL');
    $pdo->exec('ALTER TABLE "tasks" ALTER COLUMN "swimlane_id" DROP DEFAULT');
    $pdo->exec('ALTER TABLE "tasks" ADD FOREIGN KEY (swimlane_id) REFERENCES swimlanes ON DELETE CASCADE');
}

function version_100(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "projects" ADD COLUMN email VARCHAR(255)');
}

function version_99(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE invites (
            email VARCHAR(255) NOT NULL,
            project_id INTEGER NOT NULL,
            token VARCHAR(255) NOT NULL,
            PRIMARY KEY(email, token)
        )
    ");

    $pdo->exec("DELETE FROM settings WHERE \"option\"='application_datetime_format'");
}

function version_98(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "comments" ADD COLUMN date_modification BIGINT');
    $pdo->exec('UPDATE "comments" SET date_modification = date_creation WHERE date_modification IS NULL');
}

function version_97(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "users" ADD COLUMN api_access_token VARCHAR(255) DEFAULT NULL');
}

function version_96(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "settings" ALTER COLUMN "value" TYPE TEXT');
}

function version_95(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN external_provider VARCHAR(255)");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN external_uri VARCHAR(255)");
}

function version_94(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE column_has_restrictions (
            restriction_id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            column_id INTEGER NOT NULL,
            rule VARCHAR(255) NOT NULL,
            UNIQUE(role_id, column_id, rule),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE
        )
    ");
}

function version_93(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_role_has_restrictions (
            restriction_id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            rule VARCHAR(255) NOT NULL,
            UNIQUE(role_id, rule),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE
        )
    ");
}

function version_92(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_roles (
            role_id SERIAL PRIMARY KEY,
            role VARCHAR(255) NOT NULL,
            project_id INTEGER NOT NULL,
            UNIQUE(project_id, role),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE column_has_move_restrictions (
            restriction_id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            src_column_id INTEGER NOT NULL,
            dst_column_id INTEGER NOT NULL,
            UNIQUE(role_id, src_column_id, dst_column_id),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(role_id) REFERENCES project_has_roles(role_id) ON DELETE CASCADE,
            FOREIGN KEY(src_column_id) REFERENCES columns(id) ON DELETE CASCADE,
            FOREIGN KEY(dst_column_id) REFERENCES columns(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('ALTER TABLE "project_has_users" ALTER COLUMN "role" TYPE VARCHAR(255)');
    $pdo->exec('ALTER TABLE "project_has_groups" ALTER COLUMN "role" TYPE VARCHAR(255)');
}

function version_91(PDO $pdo)
{
    $pdo->exec("ALTER TABLE columns ADD COLUMN hide_in_dashboard BOOLEAN DEFAULT '0'");
}

function version_90(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE tags (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            project_id INTEGER NOT NULL,
            UNIQUE(project_id, name)
        )
    ");

    $pdo->exec("
        CREATE TABLE task_has_tags (
            task_id INTEGER NOT NULL,
            tag_id INTEGER NOT NULL,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(tag_id) REFERENCES tags(id) ON DELETE CASCADE,
            UNIQUE(tag_id, task_id)
        )
    ");
}

function version_89(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255)");
}

function version_88(PDO $pdo)
{
    $pdo->exec("ALTER TABLE user_has_metadata ADD COLUMN changed_by INTEGER DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE user_has_metadata ADD COLUMN changed_on INTEGER DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE project_has_metadata ADD COLUMN changed_by INTEGER DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE project_has_metadata ADD COLUMN changed_on INTEGER DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE task_has_metadata ADD COLUMN changed_by INTEGER DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE task_has_metadata ADD COLUMN changed_on INTEGER DEFAULT 0 NOT NULL");

    $pdo->exec("ALTER TABLE settings ADD COLUMN changed_by INTEGER DEFAULT 0 NOT NULL");
    $pdo->exec("ALTER TABLE settings ADD COLUMN changed_on INTEGER DEFAULT 0 NOT NULL");
}

function version_87(PDO $pdo)
{
    $pdo->exec("UPDATE project_activities SET event_name='task.file.create' WHERE event_name='file.create'");
}

function version_86(PDO $pdo)
{
    $pdo->exec('ALTER TABLE files RENAME TO task_has_files');

    $pdo->exec("
        CREATE TABLE project_has_files (
            id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL,
            name VARCHAR(255) NOT NULL,
            path VARCHAR(255) NOT NULL,
            is_image BOOLEAN DEFAULT '0',
            size INTEGER DEFAULT 0 NOT NULL,
            user_id INTEGER DEFAULT 0 NOT NULL,
            date INTEGER DEFAULT 0 NOT NULL,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )"
    );
}

function version_85(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT '1'");
}

function version_84(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_external_links (
            id SERIAL PRIMARY KEY,
            link_type VARCHAR(100) NOT NULL,
            dependency VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            date_creation INT NOT NULL,
            date_modification INT NOT NULL,
            task_id INT NOT NULL,
            creator_id INT DEFAULT 0,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )
    ");
}

function version_83(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_default INTEGER DEFAULT 0");
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_start INTEGER DEFAULT 0");
    $pdo->exec("ALTER TABLE projects ADD COLUMN priority_end INTEGER DEFAULT 3");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN priority INTEGER DEFAULT 0");
}

function version_82(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN owner_id INTEGER DEFAULT 0");
}

function version_81(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE password_reset (
            token VARCHAR(80) PRIMARY KEY,
            user_id INTEGER NOT NULL,
            date_expiration INTEGER NOT NULL,
            date_creation INTEGER NOT NULL,
            ip VARCHAR(45) NOT NULL,
            user_agent VARCHAR(255) NOT NULL,
            is_active BOOLEAN NOT NULL,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("INSERT INTO settings VALUES ('password_reset', '1')");
}

function version_80(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "actions" ALTER COLUMN "action_name" TYPE VARCHAR(255)');
}

function version_79(PDO $pdo)
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

function version_78(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "users" ALTER COLUMN "language" TYPE VARCHAR(5)');
}

function version_77(PDO $pdo)
{
    $pdo->exec('ALTER TABLE "users" ADD COLUMN "role" VARCHAR(25) NOT NULL DEFAULT \''.Role::APP_USER.'\'');

    $rq = $pdo->prepare('SELECT * FROM "users"');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC) ?: array();

    $rq = $pdo->prepare('UPDATE "users" SET "role"=? WHERE "id"=?');

    foreach ($rows as $row) {
        $role = Role::APP_USER;

        if ($row['is_admin'] == 1) {
            $role = Role::APP_ADMIN;
        } else if ($row['is_project_admin']) {
            $role = Role::APP_MANAGER;
        }

        $rq->execute(array($role, $row['id']));
    }

    $pdo->exec('ALTER TABLE users DROP COLUMN "is_admin"');
    $pdo->exec('ALTER TABLE users DROP COLUMN "is_project_admin"');
}

function version_76(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_groups (
            group_id INTEGER NOT NULL,
            project_id INTEGER NOT NULL,
            role VARCHAR(25) NOT NULL,
            FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(group_id, project_id)
        )
    ");

    $pdo->exec("ALTER TABLE project_has_users ADD COLUMN role VARCHAR(25) NOT NULL DEFAULT '".Role::PROJECT_VIEWER."'");

    $rq = $pdo->prepare('SELECT * FROM project_has_users');
    $rq->execute();
    $rows = $rq->fetchAll(PDO::FETCH_ASSOC) ?: array();

    $rq = $pdo->prepare('UPDATE project_has_users SET "role"=? WHERE "id"=?');

    foreach ($rows as $row) {
        $rq->execute(array(
            $row['is_owner'] == 1 ? Role::PROJECT_MANAGER : Role::PROJECT_MEMBER,
            $row['id'],
        ));
    }

    $pdo->exec('ALTER TABLE project_has_users DROP COLUMN "is_owner"');
    $pdo->exec('ALTER TABLE project_has_users DROP COLUMN "id"');
}

function version_75(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE groups (
            id SERIAL PRIMARY KEY,
            external_id VARCHAR(255) DEFAULT '',
            name VARCHAR(100) NOT NULL UNIQUE
        )
    ");

    $pdo->exec("
        CREATE TABLE group_has_users (
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(group_id, user_id)
        )
    ");
}

function version_74(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects DROP CONSTRAINT IF EXISTS projects_name_key');
}

function version_73(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE user_has_metadata (
            user_id INTEGER NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(user_id, name)
        )
    ");

    $pdo->exec("
        CREATE TABLE project_has_metadata (
            project_id INTEGER NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, name)
        )
    ");

    $pdo->exec("
        CREATE TABLE task_has_metadata (
            task_id INTEGER NOT NULL,
            name VARCHAR(50) NOT NULL,
            value VARCHAR(255) DEFAULT '',
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            UNIQUE(task_id, name)
        )
    ");

    $pdo->exec("DROP TABLE project_integrations");

    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_server'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_domain'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_username'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_password'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_nickname'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_jabber_room'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_hipchat'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_hipchat_api_url'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_hipchat_room_id'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_hipchat_room_token'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_slack_webhook'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_slack_webhook_url'");
    $pdo->exec("DELETE FROM settings WHERE \"option\"='integration_slack_webhook_channel'");
}

function version_72(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_notification_types (
            id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL,
            notification_type VARCHAR(50) NOT NULL,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, notification_type)
        )
    ");
}

function version_71(PDO $pdo)
{
    $pdo->exec("ALTER TABLE custom_filters ADD COLUMN \"append\" BOOLEAN DEFAULT '0'");
}

function version_70(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN date_due TYPE BIGINT");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN date_creation TYPE BIGINT");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN date_completed TYPE BIGINT");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN date_started TYPE BIGINT");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN date_moved TYPE BIGINT");
    $pdo->exec("ALTER TABLE comments ALTER COLUMN date_creation TYPE BIGINT");
    $pdo->exec("ALTER TABLE last_logins ALTER COLUMN date_creation TYPE BIGINT");
    $pdo->exec("ALTER TABLE project_activities ALTER COLUMN date_creation TYPE BIGINT");
    $pdo->exec("ALTER TABLE projects ALTER COLUMN last_modified TYPE BIGINT");
    $pdo->exec("ALTER TABLE remember_me ALTER COLUMN date_creation TYPE BIGINT");
    $pdo->exec('ALTER TABLE files ALTER COLUMN "date" TYPE BIGINT');
    $pdo->exec('ALTER TABLE transitions ALTER COLUMN "date" TYPE BIGINT');
    $pdo->exec('ALTER TABLE subtask_time_tracking ALTER COLUMN "start" TYPE BIGINT');
    $pdo->exec('ALTER TABLE subtask_time_tracking ALTER COLUMN "end" TYPE BIGINT');
    $pdo->exec('ALTER TABLE users ALTER COLUMN "lock_expiration_date" TYPE BIGINT');
}

function version_69(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE user_has_unread_notifications (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            date_creation BIGINT NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            event_data TEXT NOT NULL,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE user_has_notification_types (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            notification_type VARCHAR(50),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('CREATE UNIQUE INDEX user_has_notification_types_user_idx ON user_has_notification_types(user_id, notification_type)');

    // Migrate people who have notification enabled before
    $rq = $pdo->prepare("SELECT id FROM users WHERE notifications_enabled='1'");
    $rq->execute();
    $user_ids = $rq->fetchAll(PDO::FETCH_COLUMN, 0);

    foreach ($user_ids as $user_id) {
        $rq = $pdo->prepare('INSERT INTO user_has_notification_types (user_id, notification_type) VALUES (?, ?)');
        $rq->execute(array($user_id, 'email'));
    }
}

function version_68(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE custom_filters (
            id SERIAL PRIMARY KEY,
            filter VARCHAR(100) NOT NULL,
            project_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            name VARCHAR(100) NOT NULL,
            is_shared BOOLEAN DEFAULT '0'
        )
    ");
}

function version_67(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE plugin_schema_versions (
            plugin VARCHAR(80) NOT NULL PRIMARY KEY,
            version INTEGER NOT NULL DEFAULT 0
        )
    ");
}

function version_66(PDO $pdo)
{
    $pdo->exec("ALTER TABLE swimlanes ADD COLUMN description TEXT");
}

function version_65(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN gitlab_id INTEGER");
}

function version_64(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN start_date VARCHAR(10) DEFAULT ''");
    $pdo->exec("ALTER TABLE projects ADD COLUMN end_date VARCHAR(10) DEFAULT ''");
}

function version_63(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_project_admin BOOLEAN DEFAULT '0'");
}

function version_62(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN nb_failed_login INTEGER DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN lock_expiration_date INTEGER DEFAULT 0");
}

function version_61(PDO $pdo)
{
    $pdo->exec("INSERT INTO settings VALUES ('subtask_time_tracking', '1')");
    $pdo->exec("INSERT INTO settings VALUES ('cfd_include_closed_tasks', '1')");
}

function version_60(PDO $pdo)
{
    $pdo->exec("INSERT INTO settings VALUES ('default_color', 'yellow')");
}

function version_59(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_daily_stats (
            id SERIAL PRIMARY KEY,
            day CHAR(10) NOT NULL,
            project_id INTEGER NOT NULL,
            avg_lead_time INTEGER NOT NULL DEFAULT 0,
            avg_cycle_time INTEGER NOT NULL DEFAULT 0,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('CREATE UNIQUE INDEX project_daily_stats_idx ON project_daily_stats(day, project_id)');

    $pdo->exec('ALTER TABLE project_daily_summaries RENAME TO project_daily_column_stats');
}

function version_58(PDO $pdo)
{
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN slack_webhook_channel VARCHAR(255) DEFAULT ''");
    $pdo->exec("INSERT INTO settings VALUES ('integration_slack_webhook_channel', '')");
}

function version_57(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users DROP COLUMN "default_project_id"');
}

function version_56(PDO $pdo)
{
    $pdo->exec('DELETE FROM "settings" WHERE "option"=\'subtask_time_tracking\'');
}

function version_55(PDO $pdo)
{
    $pdo->exec('ALTER TABLE comments DROP CONSTRAINT IF EXISTS comments_user_id_fkey');
    $pdo->exec("ALTER TABLE comments ALTER COLUMN task_id SET NOT NULL");
    $pdo->exec("ALTER TABLE comments ALTER COLUMN user_id SET DEFAULT 0");
    $pdo->exec('ALTER TABLE comments RENAME COLUMN "date" TO "date_creation"');
    $pdo->exec("ALTER TABLE comments ALTER COLUMN date_creation SET NOT NULL");
}

function version_54(PDO $pdo)
{
    $pdo->exec("ALTER TABLE project_has_categories ALTER COLUMN project_id SET NOT NULL");
    $pdo->exec("ALTER TABLE project_has_categories ALTER COLUMN name SET NOT NULL");

    $pdo->exec("ALTER TABLE actions ALTER COLUMN project_id SET NOT NULL");
    $pdo->exec("ALTER TABLE actions ALTER COLUMN event_name SET NOT NULL");
    $pdo->exec("ALTER TABLE actions ALTER COLUMN action_name SET NOT NULL");

    $pdo->exec("ALTER TABLE action_has_params ALTER COLUMN action_id SET NOT NULL");
    $pdo->exec("ALTER TABLE action_has_params ALTER COLUMN name SET NOT NULL");
    $pdo->exec("ALTER TABLE action_has_params ALTER COLUMN value SET NOT NULL");

    $pdo->exec("ALTER TABLE files ALTER COLUMN name SET NOT NULL");
    $pdo->exec("ALTER TABLE files ALTER COLUMN task_id SET NOT NULL");

    $pdo->exec("ALTER TABLE subtasks ALTER COLUMN title SET NOT NULL");

    $pdo->exec("ALTER TABLE tasks ALTER COLUMN title SET NOT NULL");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN project_id SET NOT NULL");
    $pdo->exec("ALTER TABLE tasks ALTER COLUMN column_id SET NOT NULL");

    $pdo->exec("ALTER TABLE columns ALTER COLUMN title SET NOT NULL");
    $pdo->exec("ALTER TABLE columns ALTER COLUMN project_id SET NOT NULL");

    $pdo->exec("ALTER TABLE project_has_users ALTER COLUMN project_id SET NOT NULL");
    $pdo->exec("ALTER TABLE project_has_users ALTER COLUMN user_id SET NOT NULL");

    $pdo->exec("ALTER TABLE projects ALTER COLUMN name SET NOT NULL");

    $pdo->exec("ALTER TABLE users ALTER COLUMN username SET NOT NULL");

    $pdo->exec("ALTER TABLE user_has_notifications ALTER COLUMN user_id SET NOT NULL");
    $pdo->exec("ALTER TABLE user_has_notifications ALTER COLUMN user_id SET NOT NULL");
}

function version_53(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN notifications_filter INTEGER DEFAULT 4");
}

function version_52(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('webhook_url', ''));

    $pdo->exec("DELETE FROM settings WHERE option='webhook_url_task_creation'");
    $pdo->exec("DELETE FROM settings WHERE option='webhook_url_task_modification'");
}

function version_51(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN token VARCHAR(255) DEFAULT ''");
}

function version_50(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('calendar_user_subtasks_time_tracking', 0));
    $rq->execute(array('calendar_user_tasks', 'date_started'));
    $rq->execute(array('calendar_project_tasks', 'date_started'));

    $pdo->exec("DELETE FROM settings WHERE option='subtask_forecast'");
}

function version_49(PDO $pdo)
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

function version_48(PDO $pdo)
{
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_status INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_trigger INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_factor INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_timeframe INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_basedate INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_parent INTEGER');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_child INTEGER');
}

function version_47(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN identifier VARCHAR(50) DEFAULT ''");
}

function version_46(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_integrations (
            id SERIAL PRIMARY KEY,
            project_id INTEGER NOT NULL UNIQUE,
            hipchat BOOLEAN DEFAULT '0',
            hipchat_api_url VARCHAR(255) DEFAULT 'https://api.hipchat.com',
            hipchat_room_id VARCHAR(255),
            hipchat_room_token VARCHAR(255),
            slack BOOLEAN DEFAULT '0',
            slack_webhook_url VARCHAR(255),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");
}

function version_45(PDO $pdo)
{
    $pdo->exec('ALTER TABLE project_daily_summaries ADD COLUMN score INTEGER NOT NULL DEFAULT 0');
}

function version_44(PDO $pdo)
{
    $pdo->exec('ALTER TABLE project_has_categories ADD COLUMN description TEXT');
}

function version_43(PDO $pdo)
{
    $pdo->exec('ALTER TABLE files ADD COLUMN "date" INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN "user_id" INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN "size" INTEGER NOT NULL DEFAULT 0');
}

function version_42(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_activated BOOLEAN DEFAULT \'0\'');
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_secret CHAR(16)');
}

function version_41(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_gravatar', '0'));
}

function version_40(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_hipchat', '0'));
    $rq->execute(array('integration_hipchat_api_url', 'https://api.hipchat.com'));
    $rq->execute(array('integration_hipchat_room_id', ''));
    $rq->execute(array('integration_hipchat_room_token', ''));
}

function version_39(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_slack_webhook', '0'));
    $rq->execute(array('integration_slack_webhook_url', ''));
}

function version_38(PDO $pdo)
{
    $pdo->exec('CREATE TABLE currencies ("currency" CHAR(3) NOT NULL UNIQUE, "rate" REAL DEFAULT 0)');

    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_currency', 'USD'));
}

function version_37(PDO $pdo)
{
    $pdo->exec('CREATE TABLE transitions (
        "id" SERIAL PRIMARY KEY,
        "user_id" INTEGER NOT NULL,
        "project_id" INTEGER NOT NULL,
        "task_id" INTEGER NOT NULL,
        "src_column_id" INTEGER NOT NULL,
        "dst_column_id" INTEGER NOT NULL,
        "date" INTEGER NOT NULL,
        "time_spent" INTEGER DEFAULT 0,
        FOREIGN KEY(src_column_id) REFERENCES columns(id) ON DELETE CASCADE,
        FOREIGN KEY(dst_column_id) REFERENCES columns(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
    )');

    $pdo->exec("CREATE INDEX transitions_task_index ON transitions(task_id)");
    $pdo->exec("CREATE INDEX transitions_project_index ON transitions(project_id)");
    $pdo->exec("CREATE INDEX transitions_user_index ON transitions(user_id)");
}

function version_36(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_forecast', '0'));
}

function version_35(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_stylesheet', ''));
}

function version_34(PDO $pdo)
{
    $pdo->exec("ALTER TABLE subtask_time_tracking ADD COLUMN time_spent REAL DEFAULT 0");
}

function version_30(PDO $pdo)
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

function version_29(PDO $pdo)
{
    $pdo->exec('ALTER TABLE task_has_files RENAME TO files');
    $pdo->exec('ALTER TABLE task_has_subtasks RENAME TO subtasks');
}

function version_28(PDO $pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN description TEXT');
}

function version_27(PDO $pdo)
{
    $pdo->exec('CREATE TABLE links (
        "id" SERIAL PRIMARY KEY,
        "label" VARCHAR(255) NOT NULL,
        "opposite_id" INTEGER DEFAULT 0,
        UNIQUE("label")
    )');

    $pdo->exec("CREATE TABLE task_has_links (
        id SERIAL PRIMARY KEY,
        link_id INTEGER NOT NULL,
        task_id INTEGER NOT NULL,
        opposite_task_id INTEGER NOT NULL,
        FOREIGN KEY(link_id) REFERENCES links(id) ON DELETE CASCADE,
        FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY(opposite_task_id) REFERENCES tasks(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE INDEX task_has_links_task_index ON task_has_links(task_id)");
    $pdo->exec("CREATE UNIQUE INDEX task_has_links_unique ON task_has_links(link_id, task_id, opposite_task_id)");

    $rq = $pdo->prepare('INSERT INTO links (label, opposite_id) VALUES (?, ?)');
    $rq->execute(array('relates to', 0));
    $rq->execute(array('blocks', 3));
    $rq->execute(array('is blocked by', 2));
    $rq->execute(array('duplicates', 5));
    $rq->execute(array('is duplicated by', 4));
    $rq->execute(array('is a child of', 7));
    $rq->execute(array('is a parent of', 6));
    $rq->execute(array('targets milestone', 9));
    $rq->execute(array('is a milestone of', 8));
    $rq->execute(array('fixes', 11));
    $rq->execute(array('is fixed by', 10));
}

function version_26(PDO $pdo)
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

function version_25(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN disable_login_form BOOLEAN DEFAULT '0'");
}

function version_24(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_restriction', '0'));
    $rq->execute(array('subtask_time_tracking', '0'));

    $pdo->exec('
        CREATE TABLE subtask_time_tracking (
            id SERIAL PRIMARY KEY,
            "user_id" INTEGER NOT NULL,
            "subtask_id" INTEGER NOT NULL,
            "start" INTEGER DEFAULT 0,
            "end" INTEGER DEFAULT 0,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(subtask_id) REFERENCES task_has_subtasks(id) ON DELETE CASCADE
        )
    ');
}

function version_23(PDO $pdo)
{
    $pdo->exec('ALTER TABLE columns ADD COLUMN description TEXT');
}

function version_22(PDO $pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN timezone VARCHAR(50)');
    $pdo->exec('ALTER TABLE users ADD COLUMN language CHAR(5)');
}

function version_21(PDO $pdo)
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
    $rq = $pdo->prepare("SELECT id FROM projects WHERE is_private='1'");
    $rq->execute();
    $project_ids = $rq->fetchAll(PDO::FETCH_COLUMN, 0);

    $rq = $pdo->prepare("UPDATE project_has_users SET is_owner='1' WHERE project_id=?");

    foreach ($project_ids as $project_id) {
        $rq->execute(array($project_id));
    }
}

function version_20(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('project_categories', ''));
}

function version_19(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE swimlanes (
            id SERIAL PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            position INTEGER DEFAULT 1,
            is_active BOOLEAN DEFAULT '1',
            project_id INTEGER,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE (name, project_id)
        )
    ");

    $pdo->exec('ALTER TABLE tasks ADD COLUMN swimlane_id INTEGER DEFAULT 0');
    $pdo->exec("ALTER TABLE projects ADD COLUMN default_swimlane VARCHAR(200) DEFAULT 'Default swimlane'");
    $pdo->exec("ALTER TABLE projects ADD COLUMN show_default_swimlane BOOLEAN DEFAULT '1'");
}

function version_18(PDO $pdo)
{
    $pdo->exec("ALTER TABLE project_has_users ADD COLUMN is_owner BOOLEAN DEFAULT '0'");
}

function version_17(PDO $pdo)
{
    $pdo->exec('ALTER TABLE tasks ALTER COLUMN title SET NOT NULL');
}

function version_16(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_daily_summaries (
            id SERIAL PRIMARY KEY,
            day CHAR(10) NOT NULL,
            project_id INTEGER NOT NULL,
            column_id INTEGER NOT NULL,
            total INTEGER NOT NULL DEFAULT 0,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('CREATE UNIQUE INDEX project_daily_column_stats_idx ON project_daily_summaries(day, project_id, column_id)');
}

function version_15(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_everybody_allowed BOOLEAN DEFAULT '0'");
}

function version_14(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE project_activities (
            id SERIAL PRIMARY KEY,
            date_creation INTEGER NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            creator_id INTEGER,
            project_id INTEGER,
            task_id INTEGER,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('DROP TABLE task_has_events');
    $pdo->exec('DROP TABLE comment_has_events');
    $pdo->exec('DROP TABLE subtask_has_events');
}

function version_13(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_started INTEGER");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_spent FLOAT DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_estimated FLOAT DEFAULT 0");

    $pdo->exec("ALTER TABLE task_has_subtasks ALTER COLUMN time_estimated TYPE FLOAT");
    $pdo->exec("ALTER TABLE task_has_subtasks ALTER COLUMN time_spent TYPE FLOAT");
}

function version_12(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_private BOOLEAN DEFAULT '0'");
}

function version_11(PDO $pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_date_format', 'm/d/Y'));
}

function version_10(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE settings (
            option VARCHAR(100) PRIMARY KEY,
            value VARCHAR(255) DEFAULT ''
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

function version_9(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN reference VARCHAR(50) DEFAULT ''");
    $pdo->exec("ALTER TABLE comments ADD COLUMN reference VARCHAR(50) DEFAULT ''");

    $pdo->exec('CREATE INDEX tasks_reference_idx ON tasks(reference)');
    $pdo->exec('CREATE INDEX comments_reference_idx ON comments(reference)');
}

function version_8(PDO $pdo)
{
    $pdo->exec('CREATE UNIQUE INDEX users_username_idx ON users(username)');
}

function version_7(PDO $pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN default_columns VARCHAR(255) DEFAULT ''");
}

function version_6(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_events (
            id SERIAL PRIMARY KEY,
            date_creation INTEGER NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            creator_id INTEGER,
            project_id INTEGER,
            task_id INTEGER,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );
    ");

    $pdo->exec("
        CREATE TABLE subtask_has_events (
            id SERIAL PRIMARY KEY,
            date_creation INTEGER NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            creator_id INTEGER,
            project_id INTEGER,
            subtask_id INTEGER,
            task_id INTEGER,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(subtask_id) REFERENCES task_has_subtasks(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );
    ");

    $pdo->exec("
        CREATE TABLE comment_has_events (
            id SERIAL PRIMARY KEY,
            date_creation INTEGER NOT NULL,
            event_name VARCHAR(50) NOT NULL,
            creator_id INTEGER,
            project_id INTEGER,
            comment_id INTEGER,
            task_id INTEGER,
            data TEXT,
            FOREIGN KEY(creator_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(comment_id) REFERENCES comments(id) ON DELETE CASCADE,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );
    ");
}

function version_5(PDO $pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_public BOOLEAN DEFAULT '0'");
}

function version_4(PDO $pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN notifications_enabled BOOLEAN DEFAULT '0'");

    $pdo->exec("
        CREATE TABLE user_has_notifications (
            user_id INTEGER,
            project_id INTEGER,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE(project_id, user_id)
        );
    ");
}

function version_3(PDO $pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_modification VARCHAR(255)");
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_creation VARCHAR(255)");
}

function version_2(PDO $pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN creator_id INTEGER DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_modification INTEGER DEFAULT 0");
}

function version_1(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE config (
            language CHAR(5) DEFAULT 'en_US',
            webhooks_token VARCHAR(255) DEFAULT '',
            timezone VARCHAR(50) DEFAULT 'UTC',
            api_token VARCHAR(255) DEFAULT ''
        );

        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50),
            password VARCHAR(255),
            is_admin BOOLEAN DEFAULT '0',
            default_project_id INTEGER DEFAULT 0,
            is_ldap_user BOOLEAN DEFAULT '0',
            name VARCHAR(255),
            email VARCHAR(255),
            google_id VARCHAR(255),
            github_id VARCHAR(30)
        );

        CREATE TABLE remember_me (
            id SERIAL PRIMARY KEY,
            user_id INTEGER,
            ip VARCHAR(45),
            user_agent VARCHAR(255),
            token VARCHAR(255),
            sequence VARCHAR(255),
            expiration INTEGER,
            date_creation INTEGER,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE last_logins (
            id SERIAL PRIMARY KEY,
            auth_type VARCHAR(25),
            user_id INTEGER,
            ip VARCHAR(45),
            user_agent VARCHAR(255),
            date_creation INTEGER,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE projects (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) UNIQUE,
            is_active BOOLEAN DEFAULT '1',
            token VARCHAR(255),
            last_modified INTEGER DEFAULT 0
        );

        CREATE TABLE project_has_users (
            id SERIAL PRIMARY KEY,
            project_id INTEGER,
            user_id INTEGER,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(project_id, user_id)
        );

        CREATE TABLE project_has_categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255),
            project_id INTEGER,
            UNIQUE (project_id, name),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        );

        CREATE TABLE columns (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255),
            position INTEGER,
            project_id INTEGER,
            task_limit INTEGER DEFAULT 0,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE (title, project_id)
        );

        CREATE TABLE tasks (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255),
            description TEXT,
            date_creation INTEGER,
            color_id VARCHAR(255),
            project_id INTEGER,
            column_id INTEGER,
            owner_id INTEGER DEFAULT 0,
            position INTEGER,
            is_active BOOLEAN DEFAULT '1',
            date_completed INTEGER,
            score INTEGER,
            date_due INTEGER,
            category_id INTEGER DEFAULT 0,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE
        );

        CREATE TABLE task_has_subtasks (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255),
            status SMALLINT DEFAULT 0,
            time_estimated INTEGER DEFAULT 0,
            time_spent INTEGER DEFAULT 0,
            task_id INTEGER NOT NULL,
            user_id INTEGER,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );

        CREATE TABLE task_has_files (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255),
            path VARCHAR(255),
            is_image BOOLEAN DEFAULT '0',
            task_id INTEGER,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        );

        CREATE TABLE comments (
            id SERIAL PRIMARY KEY,
            task_id INTEGER,
            user_id INTEGER,
            date INTEGER,
            comment TEXT,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE actions (
            id SERIAL PRIMARY KEY,
            project_id INTEGER,
            event_name VARCHAR(50),
            action_name VARCHAR(50),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        );

        CREATE TABLE action_has_params (
            id SERIAL PRIMARY KEY,
            action_id INTEGER,
            name VARCHAR(50),
            value VARCHAR(50),
            FOREIGN KEY(action_id) REFERENCES actions(id) ON DELETE CASCADE
        );
    ");

    $pdo->exec("
        INSERT INTO users
        (username, password, is_admin)
        VALUES ('admin', '".\password_hash('admin', PASSWORD_BCRYPT)."', '1')
    ");

    $pdo->exec("
        INSERT INTO config
        (webhooks_token, api_token)
        VALUES ('".Token::getToken()."', '".Token::getToken()."')
    ");
}
