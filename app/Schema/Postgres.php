<?php

namespace Schema;

use Core\Security;

const VERSION = 7;

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
            event_name TEXT NOT NULL,
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
            event_name TEXT NOT NULL,
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
            event_name TEXT NOT NULL,
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
