<?php

namespace Schema;

use Core\Security;

const VERSION = 23;

function version_23($pdo)
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

function version_22($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_modification VARCHAR(255)");
    $pdo->exec("ALTER TABLE config ADD COLUMN webhooks_url_task_creation VARCHAR(255)");
}

function version_21($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN creator_id INTEGER DEFAULT '0'");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_modification INTEGER DEFAULT '0'");
}

function version_20($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN github_id VARCHAR(30)");
}

function version_19($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN api_token VARCHAR(255) DEFAULT '".Security::generateToken()."'");
}

function version_18($pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_subtasks (
            id INT NOT NULL AUTO_INCREMENT,
            title VARCHAR(255),
            status INT DEFAULT 0,
            time_estimated INT DEFAULT 0,
            time_spent INT DEFAULT 0,
            task_id INT,
            user_id INT,
            PRIMARY KEY (id),
            FOREIGN KEY(task_id) REFERENCES tasks(id) ON DELETE CASCADE
        ) ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_17($pdo)
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

function version_16($pdo)
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

function version_15($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN last_modified INT DEFAULT 0");
}

function version_14($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN name VARCHAR(255)");
    $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255)");
    $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(30)");
}

function version_13($pdo)
{
    $pdo->exec("ALTER TABLE users ADD COLUMN is_ldap_user TINYINT(1) DEFAULT 0");
}

function version_12($pdo)
{
    $pdo->exec("
        CREATE TABLE remember_me (
            id INT NOT NULL AUTO_INCREMENT,
            user_id INT,
            ip VARCHAR(40),
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
            ip VARCHAR(40),
            user_agent VARCHAR(255),
            date_creation INT,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            PRIMARY KEY (id),
            INDEX (user_id)
        ) ENGINE=InnoDB CHARSET=utf8"
    );
}

function version_11($pdo)
{
}

function version_10($pdo)
{
}

function version_9($pdo)
{
}

function version_8($pdo)
{
}

function version_7($pdo)
{
}

function version_6($pdo)
{
}

function version_5($pdo)
{
}

function version_4($pdo)
{
}

function version_3($pdo)
{
}

function version_2($pdo)
{
}

function version_1($pdo)
{
    $pdo->exec("
        CREATE TABLE config (
            language CHAR(5) DEFAULT 'en_US',
            webhooks_token VARCHAR(255),
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
            date INT,
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
        VALUES ('".Security::generateToken()."')
    ");
}
