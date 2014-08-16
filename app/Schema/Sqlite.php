<?php

namespace Schema;

use Core\Security;

const VERSION = 23;

function version_23($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN notifications_enabled INTEGER DEFAULT '0'");

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

function version_22($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_modification TEXT");
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_creation TEXT");
}

function version_21($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN creator_id INTEGER DEFAULT '0'");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_modification INTEGER DEFAULT '0'");
}

function version_20($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN github_id TEXT");
}

function version_19($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN api_token TEXT DEFAULT '".Security::generateToken()."'");
}

function version_18($pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_subtasks (
            id INTEGER PRIMARY KEY,
            title TEXT COLLATE NOCASE,
            status INTEGER DEFAULT 0,
            time_estimated INTEGER DEFAULT 0,
            time_spent INTEGER DEFAULT 0,
            task_id INTEGER,
            user_id INTEGER,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )"
    );
}

function version_17($pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_files (
            id INTEGER PRIMARY KEY,
            name TEXT COLLATE NOCASE,
            path TEXT,
            is_image INTEGER DEFAULT 0,
            task_id INTEGER,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        )"
    );
}

function version_16($pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_categories (
            id INTEGER PRIMARY KEY,
            name TEXT COLLATE NOCASE,
            project_id INT,
            UNIQUE (project_id, name),
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )"
    );

    $pdo->exec("ALTER TABLE tasks ADD COLUMN category_id INTEGER DEFAULT 0");
}

function version_15($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN last_modified INTEGER DEFAULT 0");
}

function version_14($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN name TEXT");
    $pdo->exec("ALTER TABLE users ADD COLUMN email TEXT");
    $pdo->exec("ALTER TABLE users ADD COLUMN google_id TEXT");
}

function version_13($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_ldap_user INTEGER DEFAULT 0");
}

function version_12($pdo)
{
    $pdo->exec(
        'CREATE TABLE remember_me (
            id INTEGER PRIMARY KEY,
            user_id INTEGER,
            ip TEXT,
            user_agent TEXT,
            token TEXT,
            sequence TEXT,
            expiration INTEGER,
            date_creation INTEGER,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'CREATE TABLE last_logins (
            id INTEGER PRIMARY KEY,
            auth_type TEXT,
            user_id INTEGER,
            ip TEXT,
            user_agent TEXT,
            date_creation INTEGER,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec('CREATE INDEX last_logins_user_idx ON last_logins(user_id)');
}

function version_11($pdo)
{
    $pdo->exec(
        'ALTER TABLE comments RENAME TO comments_bak'
    );

    $pdo->exec(
        'CREATE TABLE comments (
            id INTEGER PRIMARY KEY,
            task_id INTEGER,
            user_id INTEGER,
            date INTEGER,
            comment TEXT,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'INSERT INTO comments SELECT * FROM comments_bak'
    );

    $pdo->exec(
        'DROP TABLE comments_bak'
    );
}

function version_10($pdo)
{
    $pdo->exec(
        'CREATE TABLE actions (
            id INTEGER PRIMARY KEY,
            project_id INTEGER,
            event_name TEXT,
            action_name TEXT,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'CREATE TABLE action_has_params (
            id INTEGER PRIMARY KEY,
            action_id INTEGER,
            name TEXT,
            value TEXT,
            FOREIGN KEY(action_id) REFERENCES actions(id) ON DELETE CASCADE
        )'
    );
}

function version_9($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_due INTEGER");
}

function version_8($pdo)
{
    $pdo->exec(
        'CREATE TABLE comments (
            id INTEGER PRIMARY KEY,
            task_id INTEGER,
            user_id INTEGER,
            date INTEGER,
            comment TEXT,
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES tasks(id) ON DELETE CASCADE
        )'
    );
}

function version_7($pdo)
{
    $pdo->exec("
        CREATE TABLE project_has_users (
            id INTEGER PRIMARY KEY,
            project_id INTEGER,
            user_id INTEGER,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(project_id, user_id)
        )
    ");
}

function version_6($pdo)
{
    $pdo->exec("ALTER TABLE columns ADD COLUMN task_limit INTEGER DEFAULT '0'");
}

function version_5($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN score INTEGER");
}

function version_4($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN timezone TEXT DEFAULT 'UTC'");
}

function version_3($pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN token TEXT');

    // For each existing project, assign a different token
    $rq = $pdo->prepare("SELECT id FROM projects WHERE token IS NULL");
    $rq->execute();
    $results = $rq->fetchAll(\PDO::FETCH_ASSOC);

    if ($results !== false) {

        foreach ($results as &$result) {
            $rq = $pdo->prepare('UPDATE projects SET token=? WHERE id=?');
            $rq->execute(array(Security::generateToken(), $result['id']));
        }
    }
}

function version_2($pdo)
{
    $pdo->exec('ALTER TABLE tasks ADD COLUMN date_completed INTEGER');
    $pdo->exec('UPDATE tasks SET date_completed=date_creation WHERE is_active=0');
}

function version_1($pdo)
{
    $pdo->exec("
        CREATE TABLE config (
            language TEXT,
            webhooks_token TEXT
        )
    ");

    $pdo->exec("
        CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            username TEXT,
            password TEXT,
            is_admin INTEGER DEFAULT 0,
            default_project_id INTEGER DEFAULT 0
        )
    ");

    $pdo->exec("
        CREATE TABLE projects (
            id INTEGER PRIMARY KEY,
            name TEXT NOCASE UNIQUE,
            is_active INTEGER DEFAULT 1
        )
    ");

    $pdo->exec("
        CREATE TABLE columns (
            id INTEGER PRIMARY KEY,
            title TEXT,
            position INTEGER,
            project_id INTEGER,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE (title, project_id)
        )
    ");

    $pdo->exec("
        CREATE TABLE tasks (
            id INTEGER PRIMARY KEY,
            title TEXT,
            description TEXT,
            date_creation INTEGER,
            color_id TEXT,
            project_id INTEGER,
            column_id INTEGER,
            owner_id INTEGER DEFAULT '0',
            position INTEGER,
            is_active INTEGER DEFAULT 1,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        INSERT INTO users
        (username, password, is_admin)
        VALUES ('admin', '".\password_hash('admin', PASSWORD_BCRYPT)."', '1')
    ");

    $pdo->exec("
        INSERT INTO config
        (language, webhooks_token)
        VALUES ('en_US', '".Security::generateToken()."')
    ");
}
