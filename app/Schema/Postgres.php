<?php

namespace Schema;

use PDO;
use Core\Security;

const VERSION = 18;

function version_18($pdo)
{
    $pdo->exec("ALTER TABLE project_has_users ADD COLUMN is_owner BOOLEAN DEFAULT '0'");
}

function version_17($pdo)
{
    $pdo->exec('ALTER TABLE tasks ALTER COLUMN title SET NOT NULL');
}

function version_16($pdo)
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

function version_15($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_everybody_allowed BOOLEAN DEFAULT '0'");
}

function version_14($pdo)
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

function version_13($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_started INTEGER");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_spent FLOAT DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_estimated FLOAT DEFAULT 0");

    $pdo->exec("ALTER TABLE task_has_subtasks ALTER COLUMN time_estimated TYPE FLOAT");
    $pdo->exec("ALTER TABLE task_has_subtasks ALTER COLUMN time_spent TYPE FLOAT");
}

function version_12($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_private BOOLEAN DEFAULT '0'");
}

function version_11($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_date_format', 'm/d/Y'));
}

function version_10($pdo)
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

function version_9($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN reference VARCHAR(50) DEFAULT ''");
    $pdo->exec("ALTER TABLE comments ADD COLUMN reference VARCHAR(50) DEFAULT ''");

    $pdo->exec('CREATE INDEX tasks_reference_idx ON tasks(reference)');
    $pdo->exec('CREATE INDEX comments_reference_idx ON comments(reference)');
}

function version_8($pdo)
{
    $pdo->exec('CREATE UNIQUE INDEX users_username_idx ON users(username)');
}

function version_7($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN default_columns VARCHAR(255) DEFAULT ''");
}

function version_6($pdo)
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

function version_5($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN is_public BOOLEAN DEFAULT '0'");
}

function version_4($pdo)
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

function version_3($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_modification VARCHAR(255)");
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_creation VARCHAR(255)");
}

function version_2($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN creator_id INTEGER DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_modification INTEGER DEFAULT 0");
}

function version_1($pdo)
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
            ip VARCHAR(40),
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
            ip VARCHAR(40),
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
        VALUES ('".Security::generateToken()."', '".Security::generateToken()."')
    ");
}
