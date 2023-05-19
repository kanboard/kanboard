<?php

namespace Schema;

require_once __DIR__.'/Migration.php';

use PDO;
use Kanboard\Core\Security\Token;
use Kanboard\Core\Security\Role;

const VERSION = 2;

function version_2(PDO $pdo)
{
  $pdo->exec("ALTER TABLE dbo.users ADD theme nvarchar(50) DEFAULT N'light' NOT NULL");
}

function version_1(PDO $pdo)
{
    // create tables
    $pdo->exec("
        CREATE TABLE dbo.users (
          id int identity PRIMARY KEY
          , username nvarchar(255) NOT NULL
          , password nvarchar(255)
          , is_ldap_user bit DEFAULT 0
          , name nvarchar(255)
          , email nvarchar(255)
          , google_id nvarchar(255)
          , github_id nvarchar(30)
          , notifications_enabled bit DEFAULT 0
          , timezone nvarchar(50) DEFAULT N''
          , language nvarchar(11) DEFAULT N''
          , disable_login_form bit DEFAULT 0
          , twofactor_activated bit DEFAULT 0
          , twofactor_secret char(16)
          , token nvarchar(255) DEFAULT N''
          , notifications_filter int DEFAULT 4
          , nb_failed_login int DEFAULT 0
          , lock_expiration_date bigint DEFAULT 0
          , gitlab_id int
          , role nvarchar(25) NOT NULL
          , is_active bit DEFAULT 1
          , avatar_path nvarchar(255)
          , api_access_token nvarchar(255)
          , filter nvarchar(max) DEFAULT N''
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.projects (
          id int identity PRIMARY KEY
          , name nvarchar(max) NOT NULL
          , is_active bit DEFAULT 1
          , token nvarchar(255)
          , last_modified bigint DEFAULT 0
          , is_public bit DEFAULT 0
          , is_private bit DEFAULT 0
          , description nvarchar(max)
          , identifier nvarchar(50) DEFAULT N''
          , start_date nvarchar(10) DEFAULT ''
          , end_date nvarchar(10) DEFAULT ''
          , owner_id int DEFAULT 0
          , priority_default int DEFAULT 0
          , priority_start int DEFAULT 0
          , priority_end int DEFAULT 3
          , email nvarchar(max)
          , predefined_email_subjects nvarchar(max)
          , per_swimlane_task_limits bit DEFAULT 0 NOT NULL
          , task_limit int DEFAULT 0
          , enable_global_tags bit DEFAULT 1 NOT NULL
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.columns (
          id int identity PRIMARY KEY
          , title nvarchar(255) NOT NULL
          , position int
          , project_id int NOT NULL
          , task_limit int DEFAULT 0
          , description nvarchar(max)
          , hide_in_dashboard bit DEFAULT 0 NOT NULL
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , UNIQUE (title, project_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_users (
          project_id int NOT NULL
          , user_id int NOT NULL
          , role nvarchar(255) NOT NULL
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , UNIQUE(project_id, user_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.actions (
            id int identity PRIMARY KEY
          , project_id int NOT NULL
          , event_name nvarchar(max) NOT NULL
          , action_name nvarchar(max) NOT NULL
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.action_has_params (
            id int identity PRIMARY KEY
          , action_id int NOT NULL
          , name nvarchar(max) NOT NULL
          , value nvarchar(max) NOT NULL
          , FOREIGN KEY(action_id) REFERENCES dbo.actions(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.remember_me (
            id int identity PRIMARY KEY
          , user_id int NOT NULL
          , ip nvarchar(45)
          , user_agent nvarchar(255)
          , token nvarchar(255)
          , sequence nvarchar(255)
          , expiration int
          , date_creation bigint
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.last_logins (
            id int identity PRIMARY KEY
          , auth_type nvarchar(25)
          , user_id int NOT NULL
          , ip nvarchar(45)
          , user_agent nvarchar(255)
          , date_creation bigint
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_categories (
            id int identity PRIMARY KEY
          , name nvarchar(255) NOT NULL
          , project_id int NOT NULL
          , description nvarchar(max)
          , color_id nvarchar(50)
          , UNIQUE (project_id, name)
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.swimlanes (
            id int identity PRIMARY KEY
          , name nvarchar(848) NOT NULL  /* max size for unique index */
          , position int DEFAULT 1
          , is_active bit DEFAULT 1
          , project_id int NOT NULL
          , description nvarchar(max)
          , task_limit int DEFAULT 0
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , UNIQUE (name, project_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.tasks
        (
            id                   int identity PRIMARY KEY
          , title                nvarchar(max) NOT NULL
          , description          nvarchar(max)
          , date_creation        bigint
          , color_id             nvarchar(255)
          , project_id           int NOT NULL
          , column_id            int NOT NULL
          , owner_id             int DEFAULT 0
          , position             int
          , is_active            bit DEFAULT 1
          , date_completed       bigint
          , score                int
          , date_due             bigint
          , category_id          int DEFAULT 0
          , creator_id           int DEFAULT 0
          , date_modification    int DEFAULT 0
          , reference            nvarchar(max) DEFAULT ''
          , date_started         bigint
          , time_spent           float DEFAULT 0
          , time_estimated       float DEFAULT 0
          , swimlane_id          int NOT NULL
          , date_moved           bigint DEFAULT 0
          , recurrence_status    int DEFAULT 0 NOT NULL
          , recurrence_trigger   int DEFAULT 0 NOT NULL
          , recurrence_factor    int DEFAULT 0 NOT NULL
          , recurrence_timeframe int DEFAULT 0 NOT NULL
          , recurrence_basedate  int DEFAULT 0 NOT NULL
          , recurrence_parent    int
          , recurrence_child     int
          , priority             int DEFAULT 0
          , external_provider    nvarchar(255)
          , external_uri         nvarchar(255)
          , FOREIGN KEY (project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY (column_id) REFERENCES dbo.columns(id) ON DELETE NO ACTION /* columns_cascade_delete_trigger */
          , FOREIGN KEY (swimlane_id) REFERENCES dbo.swimlanes(id) ON DELETE NO ACTION /* swimlanes_cascade_delete_trigger */
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.task_has_files (
            id int identity PRIMARY KEY
          , name nvarchar(max) NOT NULL
          , path nvarchar(max)
          , is_image bit DEFAULT 0
          , task_id int NOT NULL
          , date bigint NOT NULL DEFAULT 0
          , user_id int NOT NULL DEFAULT 0
          , size int NOT NULL DEFAULT 0
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.subtasks (
            id int identity PRIMARY KEY
          , title nvarchar(max) NOT NULL
          , status smallint DEFAULT 0
          , time_estimated float DEFAULT 0
          , time_spent float DEFAULT 0
          , task_id int NOT NULL
          , user_id int
          , position int DEFAULT 1
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.user_has_notifications (
            user_id int NOT NULL
          , project_id int NOT NULL
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
          , UNIQUE(project_id, user_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.settings (
            [option] nvarchar(100) PRIMARY KEY
          , value nvarchar(max) DEFAULT ''
          , changed_by int DEFAULT 0 NOT NULL
          , changed_on int DEFAULT 0 NOT NULL
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_daily_column_stats (
            id int identity PRIMARY KEY
          , day nchar(10) NOT NULL
          , project_id int NOT NULL
          , column_id int NOT NULL
          , total int NOT NULL DEFAULT 0
          , score int NOT NULL DEFAULT 0
          , FOREIGN KEY(column_id) REFERENCES dbo.columns(id) ON DELETE CASCADE
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.subtask_time_tracking (
            id int identity PRIMARY KEY
          , user_id int NOT NULL
          , subtask_id int NOT NULL
          , [start] bigint DEFAULT 0
          , [end] bigint DEFAULT 0
          , time_spent real DEFAULT 0
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , FOREIGN KEY(subtask_id) REFERENCES dbo.subtasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.links (
            id int identity PRIMARY KEY
          , label nvarchar(255) NOT NULL
          , opposite_id int DEFAULT 0
          , UNIQUE(label)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.task_has_links (
            id int identity PRIMARY KEY
          , link_id int NOT NULL
          , task_id int NOT NULL
          , opposite_task_id int NOT NULL
          , FOREIGN KEY(link_id) REFERENCES dbo.links(id) ON DELETE CASCADE
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
          , FOREIGN KEY(opposite_task_id) REFERENCES dbo.tasks(id) ON DELETE NO ACTION /* Handled in tasks_cascade_delete_trigger */
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.transitions (
            id int identity PRIMARY KEY
          , user_id int NOT NULL
          , project_id int NOT NULL
          , task_id int NOT NULL
          , src_column_id int NOT NULL
          , dst_column_id int NOT NULL
          , date bigint NOT NULL
          , time_spent int DEFAULT 0
          , FOREIGN KEY(src_column_id) REFERENCES dbo.columns(id) ON DELETE NO ACTION  /* columns_cascade_delete_trigger */
          , FOREIGN KEY(dst_column_id) REFERENCES dbo.columns(id) ON DELETE NO ACTION  /* columns_cascade_delete_trigger */
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.currencies (
            currency nvarchar(3) NOT NULL UNIQUE
          , rate REAL DEFAULT 0
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.comments (
            id int identity PRIMARY KEY
          , task_id int NOT NULL
          , user_id int DEFAULT 0
          , date_creation bigint NOT NULL
          , comment nvarchar(max) NOT NULL
          , reference nvarchar(max) DEFAULT N''
          , date_modification bigint
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_daily_stats (
            id int identity PRIMARY KEY
          , day nchar(10) NOT NULL
          , project_id int NOT NULL
          , avg_lead_time int NOT NULL DEFAULT 0
          , avg_cycle_time int NOT NULL DEFAULT 0
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.plugin_schema_versions (
            plugin nvarchar(80) NOT NULL PRIMARY KEY
          , version int NOT NULL DEFAULT 0
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.custom_filters (
            id int identity PRIMARY KEY
          , filter nvarchar(max) NOT NULL
          , project_id int NOT NULL
          , user_id int NOT NULL
          , name nvarchar(max) NOT NULL
          , is_shared bit DEFAULT 0
          , append bit DEFAULT 0
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.user_has_unread_notifications (
            id int identity PRIMARY KEY
          , user_id int NOT NULL
          , date_creation bigint NOT NULL
          , event_name nvarchar(max) NOT NULL
          , event_data nvarchar(max) NOT NULL
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.user_has_notification_types (
            id int identity PRIMARY KEY
          , user_id int NOT NULL
          , notification_type nvarchar(50)
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_notification_types (
            id int identity PRIMARY KEY
          , project_id int NOT NULL
          , notification_type nvarchar(50) NOT NULL
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
          , UNIQUE(project_id, notification_type)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.user_has_metadata (
            user_id int NOT NULL
          , name nvarchar(50) NOT NULL
          , value nvarchar(255) DEFAULT ''
          , changed_by int DEFAULT 0 NOT NULL
          , changed_on int DEFAULT 0 NOT NULL /* TODO: should be bigint?? */
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , UNIQUE(user_id, name)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_metadata (
            project_id int NOT NULL
          , name nvarchar(50) NOT NULL
          , value nvarchar(255) DEFAULT ''
          , changed_by int DEFAULT 0 NOT NULL
          , changed_on int DEFAULT 0 NOT NULL /* TODO: should be bigint?? */
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
          , UNIQUE(project_id, name)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.task_has_metadata (
            task_id int NOT NULL
          , name nvarchar(50) NOT NULL
          , value nvarchar(255) DEFAULT ''
          , changed_by int DEFAULT 0 NOT NULL
          , changed_on int DEFAULT 0 NOT NULL /* TODO: should be bigint?? */
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
          , UNIQUE(task_id, name)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.groups (
            id int identity PRIMARY KEY
          , external_id nvarchar(255) DEFAULT ''
          , name nvarchar(850) NOT NULL UNIQUE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.group_has_users (
            group_id int NOT NULL
          , user_id int NOT NULL
          , FOREIGN KEY(group_id) REFERENCES dbo.groups(id) ON DELETE CASCADE
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , UNIQUE(group_id, user_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_groups (
            group_id int NOT NULL
          , project_id int NOT NULL
          , role nvarchar(255) NOT NULL
          , FOREIGN KEY(group_id) REFERENCES dbo.groups(id) ON DELETE CASCADE
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
          , UNIQUE(group_id, project_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.password_reset (
            token nvarchar(80) PRIMARY KEY
          , user_id int NOT NULL
          , date_expiration int NOT NULL /* TODO: bigint?? */
          , date_creation int NOT NULL /* TODO: bigint?? */
          , ip nvarchar(45) NOT NULL
          , user_agent nvarchar(255) NOT NULL
          , is_active bit NOT NULL
          , FOREIGN KEY(user_id) REFERENCES dbo.users(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.task_has_external_links (
            id int identity PRIMARY KEY
          , link_type nvarchar(100) NOT NULL
          , dependency nvarchar(100) NOT NULL
          , title nvarchar(max) NOT NULL
          , url nvarchar(max) NOT NULL
          , date_creation int NOT NULL /* TODO: bigint?? */
          , date_modification int NOT NULL /* TODO: bigint?? */
          , task_id int NOT NULL
          , creator_id int DEFAULT 0
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_files (
            id int identity PRIMARY KEY
          , project_id int NOT NULL
          , name nvarchar(max) NOT NULL
          , path nvarchar(max) NOT NULL
          , is_image bit DEFAULT 0
          , size int DEFAULT 0 NOT NULL
          , user_id int DEFAULT 0 NOT NULL
          , date int DEFAULT 0 NOT NULL /* TODO: bigint?? */
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.tags (
            id int identity PRIMARY KEY
          , name nvarchar(255) NOT NULL
          , project_id int NOT NULL
          , color_id nvarchar(50) DEFAULT NULL
          , UNIQUE(project_id, name)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.task_has_tags (
            task_id int NOT NULL
          , tag_id int NOT NULL
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
          , FOREIGN KEY(tag_id) REFERENCES dbo.tags(id) ON DELETE CASCADE
          , UNIQUE(tag_id, task_id)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_has_roles (
            role_id int identity PRIMARY KEY
          , role nvarchar(255) NOT NULL
          , project_id int NOT NULL
          , UNIQUE(project_id, role)
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.column_has_move_restrictions (
            restriction_id int identity PRIMARY KEY
          , project_id int NOT NULL
          , role_id int NOT NULL
          , src_column_id int NOT NULL
          , dst_column_id int NOT NULL
          , only_assigned bit DEFAULT 0
          , UNIQUE(role_id, src_column_id, dst_column_id)
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY(role_id) REFERENCES dbo.project_has_roles(role_id) ON DELETE CASCADE
          , FOREIGN KEY(src_column_id) REFERENCES dbo.columns(id) ON DELETE NO ACTION /* columns_cascade_delete_trigger */
          , FOREIGN KEY(dst_column_id) REFERENCES dbo.columns(id) ON DELETE NO ACTION /* columns_cascade_delete_trigger */
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_role_has_restrictions (
            restriction_id int identity PRIMARY KEY
          , project_id int NOT NULL
          , role_id int NOT NULL
          , [rule] nvarchar(255) NOT NULL
          , UNIQUE(role_id, [rule])
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY(role_id) REFERENCES dbo.project_has_roles(role_id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.column_has_restrictions (
            restriction_id int identity PRIMARY KEY
          , project_id int NOT NULL
          , role_id int NOT NULL
          , column_id int NOT NULL
          , [rule] nvarchar(255) NOT NULL
          , UNIQUE(role_id, column_id, [rule])
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY(role_id) REFERENCES dbo.project_has_roles(role_id) ON DELETE CASCADE
          , FOREIGN KEY(column_id) REFERENCES dbo.columns(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.invites (
            email nvarchar(255) NOT NULL
          , project_id int NOT NULL
          , token nvarchar(255) NOT NULL
          , PRIMARY KEY(email, token)
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.project_activities (
            id int identity PRIMARY KEY
          , date_creation bigint NOT NULL
          , event_name nvarchar(max) NOT NULL
          , creator_id int NOT NULL
          , project_id int NOT NULL
          , task_id int NOT NULL
          , data nvarchar(max)
          , FOREIGN KEY(creator_id) REFERENCES dbo.users(id) ON DELETE CASCADE
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE NO ACTION /* projects_cascade_delete_trigger */
          , FOREIGN KEY(task_id) REFERENCES dbo.tasks(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.predefined_task_descriptions (
            id int identity PRIMARY KEY
          , project_id int NOT NULL
          , title nvarchar(max) NOT NULL
          , description nvarchar(max) NOT NULL
          , FOREIGN KEY(project_id) REFERENCES dbo.projects(id) ON DELETE CASCADE
        );
    ");
    $pdo->exec("
        CREATE TABLE dbo.sessions (
            id nvarchar(450) PRIMARY KEY /* max length for primary key */
          , expire_at int NOT NULL
          , data nvarchar(max) DEFAULT ''
        );
    ");

    // create triggers -- each of which must be in its own batch
    $pdo->exec("
        CREATE TRIGGER dbo.columns_cascade_delete_trigger
        ON dbo.columns INSTEAD OF DELETE
        AS
          SET NOCOUNT ON;
          DELETE dbo.column_has_move_restrictions
          WHERE src_column_id IN (SELECT id FROM deleted)
            OR  dst_column_id IN (SELECT id FROM deleted);
          DELETE dbo.transitions
          WHERE src_column_id IN (SELECT id FROM deleted)
            OR  dst_column_id IN (SELECT id FROM deleted);
          DELETE dbo.tasks
          WHERE column_id IN (SELECT id FROM deleted);
          DELETE dbo.columns
          WHERE id IN (SELECT id FROM deleted);
    ");

    $pdo->exec("
        CREATE TRIGGER projects_cascade_delete_trigger
        ON dbo.projects INSTEAD OF DELETE
        AS
          SET NOCOUNT ON;
          DELETE dbo.column_has_move_restrictions
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.column_has_restrictions
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.columns
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.project_activities
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.project_daily_column_stats
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.project_role_has_restrictions
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.swimlanes
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.tasks
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.transitions
          WHERE project_id IN (SELECT id FROM deleted);
          DELETE dbo.projects
          WHERE id IN (SELECT id FROM deleted);
    ");

    $pdo->exec("
        CREATE TRIGGER dbo.swimlanes_cascade_delete_trigger
        ON dbo.swimlanes INSTEAD OF DELETE
        AS
          SET NOCOUNT ON;
          DELETE dbo.tasks
          WHERE swimlane_id IN (SELECT id FROM deleted);
          DELETE dbo.swimlanes
          WHERE id IN (SELECT id FROM deleted);
    ");

    $pdo->exec("
        CREATE TRIGGER dbo.tasks_cascade_delete_trigger
        ON dbo.tasks INSTEAD OF DELETE
        AS
          SET NOCOUNT ON;
          DELETE dbo.task_has_links
          WHERE opposite_task_id IN (SELECT id FROM deleted);
          DELETE dbo.tasks
          WHERE id IN (SELECT id FROM deleted);
    ");

    // set defaults
    $pdo->exec("
        ALTER TABLE dbo.project_has_users
        ADD DEFAULT N'" .Role::PROJECT_VIEWER. "' FOR role;
    ");
    $pdo->exec("
        ALTER TABLE dbo.users
        ADD DEFAULT N'" .Role::APP_USER. "' FOR role;
    ");

    // insert starting data
    $aui = $pdo->prepare("INSERT INTO dbo.users (username, password, role) VALUES (?, ?, ?);");
    $aui->execute(array('admin', \password_hash('admin', PASSWORD_BCRYPT), Role::APP_ADMIN));

    $rq = $pdo->prepare('INSERT INTO dbo.settings ([option],value) VALUES (?, ?);');
    $rq->execute(array('api_token', Token::getToken()));
    $rq->execute(array('application_url', defined('KANBOARD_URL') ? KANBOARD_URL : ''));
    $rq->execute(array('board_highlight_period', defined('RECENT_TASK_PERIOD') ? RECENT_TASK_PERIOD : 48*60*60));
    $rq->execute(array('board_private_refresh_interval', defined('BOARD_CHECK_INTERVAL') ? BOARD_CHECK_INTERVAL : 10));
    $rq->execute(array('board_public_refresh_interval', defined('BOARD_PUBLIC_CHECK_INTERVAL') ? BOARD_PUBLIC_CHECK_INTERVAL : 60));
    $rq->execute(array('webhook_token', Token::getToken()));

    $pdo->exec("
        INSERT INTO dbo.settings ([option], value) VALUES
          ('application_currency','USD'),
          ('application_date_format','m/d/Y'),
          ('application_language','en_US'),
          ('application_stylesheet',''),
          ('application_time_format','H:i'),
          ('application_timezone','UTC'),
          ('board_columns',''),
          ('calendar_project_tasks','date_started'),
          ('calendar_user_subtasks_time_tracking','0'),
          ('calendar_user_tasks','date_started'),
          ('cfd_include_closed_tasks','1'),
          ('default_color','yellow'),
          ('integration_gravatar','0'),
          ('password_reset','1'),
          ('project_categories',''),
          ('subtask_restriction','0'),
          ('subtask_time_tracking','1'),
          ('webhook_url','')
        ;
    ");

    $pdo->exec("
        SET IDENTITY_INSERT dbo.links ON;
        INSERT INTO dbo.links (id, label, opposite_id) VALUES
          (1,'relates to',0),
          (2,'blocks',3),
          (3,'is blocked by',2),
          (4,'duplicates',5),
          (5,'is duplicated by',4),
          (6,'is a child of',7),
          (7,'is a parent of',6),
          (8,'targets milestone',9),
          (9,'is a milestone of',8),
          (10,'fixes',11),
          (11,'is fixed by',10)
        ;
        SET IDENTITY_INSERT dbo.links OFF;
    ");

    // create indexes
    $pdo->exec("
        CREATE UNIQUE INDEX users_username_idx ON dbo.users(username);
        CREATE UNIQUE INDEX project_daily_column_stats_idx ON dbo.project_daily_column_stats(day, project_id, column_id);
        CREATE UNIQUE INDEX task_has_links_unique ON dbo.task_has_links(link_id, task_id, opposite_task_id);
        CREATE UNIQUE INDEX project_daily_stats_idx ON dbo.project_daily_stats(day, project_id);
        CREATE UNIQUE INDEX user_has_notification_types_user_idx ON dbo.user_has_notification_types(user_id, notification_type);

        CREATE INDEX columns_project_idx ON dbo.columns(project_id);
        CREATE INDEX swimlanes_project_idx ON dbo.swimlanes(project_id);
        CREATE INDEX categories_project_idx ON dbo.project_has_categories(project_id);
        CREATE INDEX subtasks_task_idx ON dbo.subtasks(task_id);
        CREATE INDEX files_task_idx ON dbo.task_has_files(task_id);
        CREATE INDEX task_has_links_task_index ON dbo.task_has_links(task_id);
        CREATE INDEX transitions_task_index ON dbo.transitions(task_id);
        CREATE INDEX transitions_project_index ON dbo.transitions(project_id);
        CREATE INDEX transitions_user_index ON dbo.transitions(user_id);
    ");
}
