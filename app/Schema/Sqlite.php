<?php

namespace Schema;

use Core\Security;
use PDO;
use Model\Link;

const VERSION = 67;

function version_67($pdo)
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
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_server TEXT DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_domain TEXT DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_username TEXT DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_password TEXT DEFAULT ''");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_nickname TEXT DEFAULT 'kanboard'");
    $pdo->exec("ALTER TABLE project_integrations ADD COLUMN jabber_room TEXT DEFAULT ''");
}

function version_66($pdo)
{
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_status INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_trigger INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_factor INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_timeframe INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_basedate INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_parent INTEGER');
    $pdo->exec('ALTER TABLE tasks ADD COLUMN recurrence_child INTEGER');
}

function version_65($pdo)
{
    $pdo->exec("ALTER TABLE projects ADD COLUMN identifier TEXT DEFAULT ''");
}

function version_64($pdo)
{
    $pdo->exec("
        CREATE TABLE project_integrations (
            id INTEGER PRIMARY KEY,
            project_id INTEGER NOT NULL UNIQUE,
            hipchat INTEGER DEFAULT 0,
            hipchat_api_url TEXT DEFAULT 'https://api.hipchat.com',
            hipchat_room_id TEXT,
            hipchat_room_token TEXT,
            slack INTEGER DEFAULT 0,
            slack_webhook_url TEXT,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");
}

function version_63($pdo)
{
    $pdo->exec('ALTER TABLE project_daily_summaries ADD COLUMN score INTEGER NOT NULL DEFAULT 0');
}

function version_62($pdo)
{
    $pdo->exec('ALTER TABLE project_has_categories ADD COLUMN description TEXT');
}

function version_61($pdo)
{
    $pdo->exec('ALTER TABLE files ADD COLUMN "date" INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN "user_id" INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('ALTER TABLE files ADD COLUMN "size" INTEGER NOT NULL DEFAULT 0');
}

function version_60($pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_activated INTEGER DEFAULT 0');
    $pdo->exec('ALTER TABLE users ADD COLUMN twofactor_secret TEXT');
}

function version_59($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_gravatar', '0'));
}

function version_58($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_hipchat', '0'));
    $rq->execute(array('integration_hipchat_api_url', 'https://api.hipchat.com'));
    $rq->execute(array('integration_hipchat_room_id', ''));
    $rq->execute(array('integration_hipchat_room_token', ''));
}

function version_57($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('integration_slack_webhook', '0'));
    $rq->execute(array('integration_slack_webhook_url', ''));
}

function version_56($pdo)
{
    $pdo->exec('CREATE TABLE currencies ("currency" TEXT NOT NULL UNIQUE, "rate" REAL DEFAULT 0)');

    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_currency', 'USD'));
}

function version_55($pdo)
{
    $pdo->exec('CREATE TABLE transitions (
        "id" INTEGER PRIMARY KEY,
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

function version_54($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_forecast', '0'));
}

function version_53($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_stylesheet', ''));
}

function version_52($pdo)
{
    $pdo->exec("ALTER TABLE subtask_time_tracking ADD COLUMN time_spent REAL DEFAULT 0");
}

function version_51($pdo)
{
    $pdo->exec('CREATE TABLE budget_lines (
        "id" INTEGER PRIMARY KEY,
        "project_id" INTEGER NOT NULL,
        "amount" REAL NOT NULL,
        "date" TEXT NOT NULL,
        "comment" TEXT,
        FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
    )');
}

function version_50($pdo)
{
    $pdo->exec('CREATE TABLE timetable_day (
        "id" INTEGER PRIMARY KEY,
        "user_id" INTEGER NOT NULL,
        "start" TEXT NOT NULL,
        "end" TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE timetable_week (
        "id" INTEGER PRIMARY KEY,
        "user_id" INTEGER NOT NULL,
        "day" INTEGER NOT NULL,
        "start" TEXT NOT NULL,
        "end" TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE timetable_off (
        "id" INTEGER PRIMARY KEY,
        "user_id" INTEGER NOT NULL,
        "date" TEXT NOT NULL,
        "all_day" INTEGER DEFAULT 0,
        "start" TEXT DEFAULT 0,
        "end" TEXT DEFAULT 0,
        "comment" TEXT,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');

    $pdo->exec('CREATE TABLE timetable_extra (
        "id" INTEGER PRIMARY KEY,
        "user_id" INTEGER NOT NULL,
        "date" TEXT NOT NULL,
        "all_day" INTEGER DEFAULT 0,
        "start" TEXT DEFAULT 0,
        "end" TEXT DEFAULT 0,
        "comment" TEXT,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )');
}

function version_49($pdo)
{
    $pdo->exec("CREATE TABLE hourly_rates (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        rate REAL DEFAULT 0,
        date_effective INTEGER NOT NULL,
        currency TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
}

function version_48($pdo)
{
    $pdo->exec('ALTER TABLE subtasks ADD COLUMN position INTEGER DEFAULT 1');

    // Migrate all subtasks position

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

function version_47($pdo)
{
    $pdo->exec('ALTER TABLE task_has_files RENAME TO files');
    $pdo->exec('ALTER TABLE task_has_subtasks RENAME TO subtasks');
}

function version_46($pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN description TEXT');
}

function version_45($pdo)
{
    $pdo->exec("CREATE TABLE links (
        id INTEGER PRIMARY KEY,
        label TEXT NOT NULL,
        opposite_id INTEGER DEFAULT 0,
        UNIQUE(label)
    )");

    $pdo->exec("CREATE TABLE task_has_links (
        id INTEGER PRIMARY KEY,
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

function version_44($pdo)
{
	$pdo->exec('ALTER TABLE tasks ADD COLUMN date_moved INTEGER DEFAULT 0');

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

function version_43($pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN disable_login_form INTEGER DEFAULT 0');
}

function version_42($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('subtask_restriction', '0'));
    $rq->execute(array('subtask_time_tracking', '0'));

    $pdo->exec("
        CREATE TABLE subtask_time_tracking (
            id INTEGER PRIMARY KEY,
            user_id INTEGER NOT NULL,
            subtask_id INTEGER NOT NULL,
            start INTEGER DEFAULT 0,
            end INTEGER DEFAULT 0,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(subtask_id) REFERENCES task_has_subtasks(id) ON DELETE CASCADE
        )
    ");
}

function version_41($pdo)
{
    $pdo->exec('ALTER TABLE columns ADD COLUMN description TEXT');
}

function version_40($pdo)
{
    $pdo->exec('ALTER TABLE users ADD COLUMN timezone TEXT');
    $pdo->exec('ALTER TABLE users ADD COLUMN language TEXT');
}

function version_39($pdo)
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

function version_38($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('project_categories', ''));
}

function version_37($pdo)
{
    $pdo->exec("
        CREATE TABLE swimlanes (
            id INTEGER PRIMARY KEY,
            name TEXT,
            position INTEGER DEFAULT 1,
            is_active INTEGER DEFAULT 1,
            project_id INTEGER,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE,
            UNIQUE (name, project_id)
        )
    ");

    $pdo->exec('ALTER TABLE tasks ADD COLUMN swimlane_id INTEGER DEFAULT 0');
    $pdo->exec("ALTER TABLE projects ADD COLUMN default_swimlane TEXT DEFAULT 'Default swimlane'");
    $pdo->exec("ALTER TABLE projects ADD COLUMN show_default_swimlane INTEGER DEFAULT 1");
}

function version_36($pdo)
{
    $pdo->exec('ALTER TABLE project_has_users ADD COLUMN is_owner INTEGER DEFAULT "0"');
}

function version_35($pdo)
{
    $pdo->exec("
        CREATE TABLE project_daily_summaries (
            id INTEGER PRIMARY KEY,
            day TEXT NOT NULL,
            project_id INTEGER NOT NULL,
            column_id INTEGER NOT NULL,
            total INTEGER NOT NULL DEFAULT 0,
            FOREIGN KEY(column_id) REFERENCES columns(id) ON DELETE CASCADE,
            FOREIGN KEY(project_id) REFERENCES projects(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec('CREATE UNIQUE INDEX project_daily_column_stats_idx ON project_daily_summaries(day, project_id, column_id)');
}

function version_34($pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN is_everybody_allowed INTEGER DEFAULT "0"');
}

function version_33($pdo)
{
    $pdo->exec("
        CREATE TABLE project_activities (
            id INTEGER PRIMARY KEY,
            date_creation INTEGER NOT NULL,
            event_name TEXT NOT NULL,
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

function version_32($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN date_started INTEGER");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_spent NUMERIC DEFAULT 0");
    $pdo->exec("ALTER TABLE tasks ADD COLUMN time_estimated NUMERIC DEFAULT 0");
}

function version_31($pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN is_private INTEGER DEFAULT "0"');
}

function version_30($pdo)
{
    $rq = $pdo->prepare('INSERT INTO settings VALUES (?, ?)');
    $rq->execute(array('application_date_format', 'm/d/Y'));
}

function version_29($pdo)
{
    $pdo->exec("
        CREATE TABLE settings (
            option TEXT PRIMARY KEY,
            value TEXT DEFAULT ''
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

function version_28($pdo)
{
    $pdo->exec("ALTER TABLE tasks ADD COLUMN reference TEXT DEFAULT ''");
    $pdo->exec("ALTER TABLE comments ADD COLUMN reference TEXT DEFAULT ''");

    $pdo->exec('CREATE INDEX tasks_reference_idx ON tasks(reference)');
    $pdo->exec('CREATE INDEX comments_reference_idx ON comments(reference)');
}

function version_27($pdo)
{
    $pdo->exec('CREATE UNIQUE INDEX users_username_idx ON users(username)');
}

function version_26($pdo)
{
    $pdo->exec("ALTER TABLE config ADD COLUMN default_columns TEXT DEFAULT ''");
}

function version_25($pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_events (
            id INTEGER PRIMARY KEY,
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
            id INTEGER PRIMARY KEY,
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
            id INTEGER PRIMARY KEY,
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

function version_24($pdo)
{
    $pdo->exec('ALTER TABLE projects ADD COLUMN is_public INTEGER DEFAULT "0"');
}

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
    $pdo->exec("ALTER TABLE config ADD COLUMN api_token TEXT DEFAULT ''");
    $pdo->exec("UPDATE config SET api_token='".Security::generateToken()."'");
}

function version_18($pdo)
{
    $pdo->exec("
        CREATE TABLE task_has_subtasks (
            id INTEGER PRIMARY KEY,
            title TEXT COLLATE NOCASE,
            status INTEGER DEFAULT 0,
            time_estimated NUMERIC DEFAULT 0,
            time_spent NUMERIC DEFAULT 0,
            task_id INTEGER NOT NULL,
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
            language TEXT DEFAULT 'en_US',
            webhooks_token TEXT DEFAULT ''
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
            title TEXT NOCASE NOT NULL,
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
        (webhooks_token)
        VALUES ('".Security::generateToken()."')
    ");
}
