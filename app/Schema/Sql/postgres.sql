--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: action_has_params; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE action_has_params (
    id integer NOT NULL,
    action_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value character varying(50) NOT NULL
);


--
-- Name: action_has_params_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE action_has_params_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: action_has_params_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE action_has_params_id_seq OWNED BY action_has_params.id;


--
-- Name: actions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE actions (
    id integer NOT NULL,
    project_id integer NOT NULL,
    event_name character varying(50) NOT NULL,
    action_name character varying(50) NOT NULL
);


--
-- Name: actions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE actions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: actions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE actions_id_seq OWNED BY actions.id;


--
-- Name: columns; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE columns (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    "position" integer,
    project_id integer NOT NULL,
    task_limit integer DEFAULT 0,
    description text
);


--
-- Name: columns_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE columns_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: columns_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE columns_id_seq OWNED BY columns.id;


--
-- Name: comments; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE comments (
    id integer NOT NULL,
    task_id integer NOT NULL,
    user_id integer DEFAULT 0,
    date_creation bigint NOT NULL,
    comment text,
    reference character varying(50) DEFAULT ''::character varying
);


--
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE comments_id_seq OWNED BY comments.id;


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE currencies (
    currency character(3) NOT NULL,
    rate real DEFAULT 0
);


--
-- Name: custom_filters; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE custom_filters (
    id integer NOT NULL,
    filter character varying(100) NOT NULL,
    project_id integer NOT NULL,
    user_id integer NOT NULL,
    name character varying(100) NOT NULL,
    is_shared boolean DEFAULT false,
    append boolean DEFAULT false
);


--
-- Name: custom_filters_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE custom_filters_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: custom_filters_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE custom_filters_id_seq OWNED BY custom_filters.id;


--
-- Name: files; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE files (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    path character varying(255),
    is_image boolean DEFAULT false,
    task_id integer NOT NULL,
    date bigint DEFAULT 0 NOT NULL,
    user_id integer DEFAULT 0 NOT NULL,
    size integer DEFAULT 0 NOT NULL
);


--
-- Name: last_logins; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE last_logins (
    id integer NOT NULL,
    auth_type character varying(25),
    user_id integer,
    ip character varying(40),
    user_agent character varying(255),
    date_creation bigint
);


--
-- Name: last_logins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE last_logins_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: last_logins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE last_logins_id_seq OWNED BY last_logins.id;


--
-- Name: links; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE links (
    id integer NOT NULL,
    label character varying(255) NOT NULL,
    opposite_id integer DEFAULT 0
);


--
-- Name: links_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE links_id_seq OWNED BY links.id;


--
-- Name: plugin_schema_versions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE plugin_schema_versions (
    plugin character varying(80) NOT NULL,
    version integer DEFAULT 0 NOT NULL
);


--
-- Name: project_activities; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_activities (
    id integer NOT NULL,
    date_creation bigint NOT NULL,
    event_name character varying(50) NOT NULL,
    creator_id integer,
    project_id integer,
    task_id integer,
    data text
);


--
-- Name: project_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_activities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_activities_id_seq OWNED BY project_activities.id;


--
-- Name: project_daily_column_stats; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_daily_column_stats (
    id integer NOT NULL,
    day character(10) NOT NULL,
    project_id integer NOT NULL,
    column_id integer NOT NULL,
    total integer DEFAULT 0 NOT NULL,
    score integer DEFAULT 0 NOT NULL
);


--
-- Name: project_daily_stats; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_daily_stats (
    id integer NOT NULL,
    day character(10) NOT NULL,
    project_id integer NOT NULL,
    avg_lead_time integer DEFAULT 0 NOT NULL,
    avg_cycle_time integer DEFAULT 0 NOT NULL
);


--
-- Name: project_daily_stats_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_daily_stats_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_daily_stats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_daily_stats_id_seq OWNED BY project_daily_stats.id;


--
-- Name: project_daily_summaries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_daily_summaries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_daily_summaries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_daily_summaries_id_seq OWNED BY project_daily_column_stats.id;


--
-- Name: project_has_categories; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_has_categories (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    project_id integer NOT NULL,
    description text
);


--
-- Name: project_has_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_has_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_has_categories_id_seq OWNED BY project_has_categories.id;


--
-- Name: project_has_metadata; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_has_metadata (
    project_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value character varying(255) DEFAULT ''::character varying
);


--
-- Name: project_has_notification_types; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_has_notification_types (
    id integer NOT NULL,
    project_id integer NOT NULL,
    notification_type character varying(50) NOT NULL
);


--
-- Name: project_has_notification_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_has_notification_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_notification_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_has_notification_types_id_seq OWNED BY project_has_notification_types.id;


--
-- Name: project_has_users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE project_has_users (
    id integer NOT NULL,
    project_id integer NOT NULL,
    user_id integer NOT NULL,
    is_owner boolean DEFAULT false
);


--
-- Name: project_has_users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE project_has_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE project_has_users_id_seq OWNED BY project_has_users.id;


--
-- Name: projects; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE projects (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true,
    token character varying(255),
    last_modified bigint DEFAULT 0,
    is_public boolean DEFAULT false,
    is_private boolean DEFAULT false,
    is_everybody_allowed boolean DEFAULT false,
    default_swimlane character varying(200) DEFAULT 'Default swimlane'::character varying,
    show_default_swimlane boolean DEFAULT true,
    description text,
    identifier character varying(50) DEFAULT ''::character varying,
    start_date character varying(10) DEFAULT ''::character varying,
    end_date character varying(10) DEFAULT ''::character varying
);


--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- Name: remember_me; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE remember_me (
    id integer NOT NULL,
    user_id integer,
    ip character varying(40),
    user_agent character varying(255),
    token character varying(255),
    sequence character varying(255),
    expiration integer,
    date_creation bigint
);


--
-- Name: remember_me_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE remember_me_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: remember_me_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE remember_me_id_seq OWNED BY remember_me.id;


--
-- Name: schema_version; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE schema_version (
    version integer DEFAULT 0
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE settings (
    option character varying(100) NOT NULL,
    value character varying(255) DEFAULT ''::character varying
);


--
-- Name: subtask_time_tracking; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE subtask_time_tracking (
    id integer NOT NULL,
    user_id integer NOT NULL,
    subtask_id integer NOT NULL,
    start bigint DEFAULT 0,
    "end" bigint DEFAULT 0,
    time_spent real DEFAULT 0
);


--
-- Name: subtask_time_tracking_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE subtask_time_tracking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subtask_time_tracking_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE subtask_time_tracking_id_seq OWNED BY subtask_time_tracking.id;


--
-- Name: subtasks; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE subtasks (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    status smallint DEFAULT 0,
    time_estimated double precision DEFAULT 0,
    time_spent double precision DEFAULT 0,
    task_id integer NOT NULL,
    user_id integer,
    "position" integer DEFAULT 1
);


--
-- Name: swimlanes; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE swimlanes (
    id integer NOT NULL,
    name character varying(200) NOT NULL,
    "position" integer DEFAULT 1,
    is_active boolean DEFAULT true,
    project_id integer,
    description text
);


--
-- Name: swimlanes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE swimlanes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: swimlanes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE swimlanes_id_seq OWNED BY swimlanes.id;


--
-- Name: task_has_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE task_has_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE task_has_files_id_seq OWNED BY files.id;


--
-- Name: task_has_links; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE task_has_links (
    id integer NOT NULL,
    link_id integer NOT NULL,
    task_id integer NOT NULL,
    opposite_task_id integer NOT NULL
);


--
-- Name: task_has_links_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE task_has_links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE task_has_links_id_seq OWNED BY task_has_links.id;


--
-- Name: task_has_metadata; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE task_has_metadata (
    task_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value character varying(255) DEFAULT ''::character varying
);


--
-- Name: task_has_subtasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE task_has_subtasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_subtasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE task_has_subtasks_id_seq OWNED BY subtasks.id;


--
-- Name: tasks; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE tasks (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    date_creation bigint,
    color_id character varying(255),
    project_id integer NOT NULL,
    column_id integer NOT NULL,
    owner_id integer DEFAULT 0,
    "position" integer,
    is_active boolean DEFAULT true,
    date_completed bigint,
    score integer,
    date_due bigint,
    category_id integer DEFAULT 0,
    creator_id integer DEFAULT 0,
    date_modification integer DEFAULT 0,
    reference character varying(50) DEFAULT ''::character varying,
    date_started bigint,
    time_spent double precision DEFAULT 0,
    time_estimated double precision DEFAULT 0,
    swimlane_id integer DEFAULT 0,
    date_moved bigint DEFAULT 0,
    recurrence_status integer DEFAULT 0 NOT NULL,
    recurrence_trigger integer DEFAULT 0 NOT NULL,
    recurrence_factor integer DEFAULT 0 NOT NULL,
    recurrence_timeframe integer DEFAULT 0 NOT NULL,
    recurrence_basedate integer DEFAULT 0 NOT NULL,
    recurrence_parent integer,
    recurrence_child integer
);


--
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE tasks_id_seq OWNED BY tasks.id;


--
-- Name: transitions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE transitions (
    id integer NOT NULL,
    user_id integer NOT NULL,
    project_id integer NOT NULL,
    task_id integer NOT NULL,
    src_column_id integer NOT NULL,
    dst_column_id integer NOT NULL,
    date bigint NOT NULL,
    time_spent integer DEFAULT 0
);


--
-- Name: transitions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE transitions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transitions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE transitions_id_seq OWNED BY transitions.id;


--
-- Name: user_has_metadata; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_has_metadata (
    user_id integer NOT NULL,
    name character varying(50) NOT NULL,
    value character varying(255) DEFAULT ''::character varying
);


--
-- Name: user_has_notification_types; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_has_notification_types (
    id integer NOT NULL,
    user_id integer NOT NULL,
    notification_type character varying(50)
);


--
-- Name: user_has_notification_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_has_notification_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_has_notification_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_has_notification_types_id_seq OWNED BY user_has_notification_types.id;


--
-- Name: user_has_notifications; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_has_notifications (
    user_id integer NOT NULL,
    project_id integer
);


--
-- Name: user_has_unread_notifications; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_has_unread_notifications (
    id integer NOT NULL,
    user_id integer NOT NULL,
    date_creation bigint NOT NULL,
    event_name character varying(50) NOT NULL,
    event_data text NOT NULL
);


--
-- Name: user_has_unread_notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_has_unread_notifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_has_unread_notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_has_unread_notifications_id_seq OWNED BY user_has_unread_notifications.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username character varying(50) NOT NULL,
    password character varying(255),
    is_admin boolean DEFAULT false,
    is_ldap_user boolean DEFAULT false,
    name character varying(255),
    email character varying(255),
    google_id character varying(255),
    github_id character varying(30),
    notifications_enabled boolean DEFAULT false,
    timezone character varying(50),
    language character(5),
    disable_login_form boolean DEFAULT false,
    twofactor_activated boolean DEFAULT false,
    twofactor_secret character(16),
    token character varying(255) DEFAULT ''::character varying,
    notifications_filter integer DEFAULT 4,
    nb_failed_login integer DEFAULT 0,
    lock_expiration_date bigint DEFAULT 0,
    is_project_admin boolean DEFAULT false,
    gitlab_id integer
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY action_has_params ALTER COLUMN id SET DEFAULT nextval('action_has_params_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY actions ALTER COLUMN id SET DEFAULT nextval('actions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY columns ALTER COLUMN id SET DEFAULT nextval('columns_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY comments ALTER COLUMN id SET DEFAULT nextval('comments_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY custom_filters ALTER COLUMN id SET DEFAULT nextval('custom_filters_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY files ALTER COLUMN id SET DEFAULT nextval('task_has_files_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY last_logins ALTER COLUMN id SET DEFAULT nextval('last_logins_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY links ALTER COLUMN id SET DEFAULT nextval('links_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_activities ALTER COLUMN id SET DEFAULT nextval('project_activities_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_daily_column_stats ALTER COLUMN id SET DEFAULT nextval('project_daily_summaries_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_daily_stats ALTER COLUMN id SET DEFAULT nextval('project_daily_stats_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_categories ALTER COLUMN id SET DEFAULT nextval('project_has_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_notification_types ALTER COLUMN id SET DEFAULT nextval('project_has_notification_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_users ALTER COLUMN id SET DEFAULT nextval('project_has_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY remember_me ALTER COLUMN id SET DEFAULT nextval('remember_me_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY subtask_time_tracking ALTER COLUMN id SET DEFAULT nextval('subtask_time_tracking_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY subtasks ALTER COLUMN id SET DEFAULT nextval('task_has_subtasks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY swimlanes ALTER COLUMN id SET DEFAULT nextval('swimlanes_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY task_has_links ALTER COLUMN id SET DEFAULT nextval('task_has_links_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY tasks ALTER COLUMN id SET DEFAULT nextval('tasks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions ALTER COLUMN id SET DEFAULT nextval('transitions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_notification_types ALTER COLUMN id SET DEFAULT nextval('user_has_notification_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_unread_notifications ALTER COLUMN id SET DEFAULT nextval('user_has_unread_notifications_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: action_has_params_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY action_has_params
    ADD CONSTRAINT action_has_params_pkey PRIMARY KEY (id);


--
-- Name: actions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY (id);


--
-- Name: columns_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY columns
    ADD CONSTRAINT columns_pkey PRIMARY KEY (id);


--
-- Name: columns_title_project_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY columns
    ADD CONSTRAINT columns_title_project_id_key UNIQUE (title, project_id);


--
-- Name: comments_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- Name: currencies_currency_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY currencies
    ADD CONSTRAINT currencies_currency_key UNIQUE (currency);


--
-- Name: custom_filters_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY custom_filters
    ADD CONSTRAINT custom_filters_pkey PRIMARY KEY (id);


--
-- Name: last_logins_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY last_logins
    ADD CONSTRAINT last_logins_pkey PRIMARY KEY (id);


--
-- Name: links_label_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY links
    ADD CONSTRAINT links_label_key UNIQUE (label);


--
-- Name: links_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY links
    ADD CONSTRAINT links_pkey PRIMARY KEY (id);


--
-- Name: plugin_schema_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY plugin_schema_versions
    ADD CONSTRAINT plugin_schema_versions_pkey PRIMARY KEY (plugin);


--
-- Name: project_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_activities
    ADD CONSTRAINT project_activities_pkey PRIMARY KEY (id);


--
-- Name: project_daily_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_daily_stats
    ADD CONSTRAINT project_daily_stats_pkey PRIMARY KEY (id);


--
-- Name: project_daily_summaries_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_daily_column_stats
    ADD CONSTRAINT project_daily_summaries_pkey PRIMARY KEY (id);


--
-- Name: project_has_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_categories
    ADD CONSTRAINT project_has_categories_pkey PRIMARY KEY (id);


--
-- Name: project_has_categories_project_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_categories
    ADD CONSTRAINT project_has_categories_project_id_name_key UNIQUE (project_id, name);


--
-- Name: project_has_metadata_project_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_metadata
    ADD CONSTRAINT project_has_metadata_project_id_name_key UNIQUE (project_id, name);


--
-- Name: project_has_notification_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_notification_types
    ADD CONSTRAINT project_has_notification_types_pkey PRIMARY KEY (id);


--
-- Name: project_has_notification_types_project_id_notification_type_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_notification_types
    ADD CONSTRAINT project_has_notification_types_project_id_notification_type_key UNIQUE (project_id, notification_type);


--
-- Name: project_has_users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_users
    ADD CONSTRAINT project_has_users_pkey PRIMARY KEY (id);


--
-- Name: project_has_users_project_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY project_has_users
    ADD CONSTRAINT project_has_users_project_id_user_id_key UNIQUE (project_id, user_id);


--
-- Name: projects_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_name_key UNIQUE (name);


--
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: remember_me_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY remember_me
    ADD CONSTRAINT remember_me_pkey PRIMARY KEY (id);


--
-- Name: settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (option);


--
-- Name: subtask_time_tracking_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY subtask_time_tracking
    ADD CONSTRAINT subtask_time_tracking_pkey PRIMARY KEY (id);


--
-- Name: swimlanes_name_project_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY swimlanes
    ADD CONSTRAINT swimlanes_name_project_id_key UNIQUE (name, project_id);


--
-- Name: swimlanes_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY swimlanes
    ADD CONSTRAINT swimlanes_pkey PRIMARY KEY (id);


--
-- Name: task_has_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY files
    ADD CONSTRAINT task_has_files_pkey PRIMARY KEY (id);


--
-- Name: task_has_links_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY task_has_links
    ADD CONSTRAINT task_has_links_pkey PRIMARY KEY (id);


--
-- Name: task_has_metadata_task_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY task_has_metadata
    ADD CONSTRAINT task_has_metadata_task_id_name_key UNIQUE (task_id, name);


--
-- Name: task_has_subtasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY subtasks
    ADD CONSTRAINT task_has_subtasks_pkey PRIMARY KEY (id);


--
-- Name: tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY tasks
    ADD CONSTRAINT tasks_pkey PRIMARY KEY (id);


--
-- Name: transitions_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_pkey PRIMARY KEY (id);


--
-- Name: user_has_metadata_user_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_has_metadata
    ADD CONSTRAINT user_has_metadata_user_id_name_key UNIQUE (user_id, name);


--
-- Name: user_has_notification_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_has_notification_types
    ADD CONSTRAINT user_has_notification_types_pkey PRIMARY KEY (id);


--
-- Name: user_has_notifications_project_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_has_notifications
    ADD CONSTRAINT user_has_notifications_project_id_user_id_key UNIQUE (project_id, user_id);


--
-- Name: user_has_unread_notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_has_unread_notifications
    ADD CONSTRAINT user_has_unread_notifications_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: categories_project_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX categories_project_idx ON project_has_categories USING btree (project_id);


--
-- Name: columns_project_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX columns_project_idx ON columns USING btree (project_id);


--
-- Name: comments_reference_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX comments_reference_idx ON comments USING btree (reference);


--
-- Name: comments_task_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX comments_task_idx ON comments USING btree (task_id);


--
-- Name: files_task_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX files_task_idx ON files USING btree (task_id);


--
-- Name: project_daily_column_stats_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX project_daily_column_stats_idx ON project_daily_column_stats USING btree (day, project_id, column_id);


--
-- Name: project_daily_stats_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX project_daily_stats_idx ON project_daily_stats USING btree (day, project_id);


--
-- Name: subtasks_task_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX subtasks_task_idx ON subtasks USING btree (task_id);


--
-- Name: swimlanes_project_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX swimlanes_project_idx ON swimlanes USING btree (project_id);


--
-- Name: task_has_links_task_index; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX task_has_links_task_index ON task_has_links USING btree (task_id);


--
-- Name: task_has_links_unique; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX task_has_links_unique ON task_has_links USING btree (link_id, task_id, opposite_task_id);


--
-- Name: tasks_project_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX tasks_project_idx ON tasks USING btree (project_id);


--
-- Name: tasks_reference_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX tasks_reference_idx ON tasks USING btree (reference);


--
-- Name: transitions_project_index; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX transitions_project_index ON transitions USING btree (project_id);


--
-- Name: transitions_task_index; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX transitions_task_index ON transitions USING btree (task_id);


--
-- Name: transitions_user_index; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX transitions_user_index ON transitions USING btree (user_id);


--
-- Name: user_has_notification_types_user_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX user_has_notification_types_user_idx ON user_has_notification_types USING btree (user_id, notification_type);


--
-- Name: users_admin_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE INDEX users_admin_idx ON users USING btree (is_admin);


--
-- Name: users_username_idx; Type: INDEX; Schema: public; Owner: -; Tablespace: 
--

CREATE UNIQUE INDEX users_username_idx ON users USING btree (username);


--
-- Name: action_has_params_action_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY action_has_params
    ADD CONSTRAINT action_has_params_action_id_fkey FOREIGN KEY (action_id) REFERENCES actions(id) ON DELETE CASCADE;


--
-- Name: actions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: columns_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY columns
    ADD CONSTRAINT columns_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: comments_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT comments_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: last_logins_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY last_logins
    ADD CONSTRAINT last_logins_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: project_activities_creator_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_activities
    ADD CONSTRAINT project_activities_creator_id_fkey FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: project_activities_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_activities
    ADD CONSTRAINT project_activities_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_activities_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_activities
    ADD CONSTRAINT project_activities_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: project_daily_stats_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_daily_stats
    ADD CONSTRAINT project_daily_stats_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_daily_summaries_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_daily_column_stats
    ADD CONSTRAINT project_daily_summaries_column_id_fkey FOREIGN KEY (column_id) REFERENCES columns(id) ON DELETE CASCADE;


--
-- Name: project_daily_summaries_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_daily_column_stats
    ADD CONSTRAINT project_daily_summaries_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_has_categories_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_categories
    ADD CONSTRAINT project_has_categories_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_has_metadata_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_metadata
    ADD CONSTRAINT project_has_metadata_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_has_notification_types_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_notification_types
    ADD CONSTRAINT project_has_notification_types_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_has_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_users
    ADD CONSTRAINT project_has_users_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: project_has_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY project_has_users
    ADD CONSTRAINT project_has_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: remember_me_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY remember_me
    ADD CONSTRAINT remember_me_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: subtask_time_tracking_subtask_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY subtask_time_tracking
    ADD CONSTRAINT subtask_time_tracking_subtask_id_fkey FOREIGN KEY (subtask_id) REFERENCES subtasks(id) ON DELETE CASCADE;


--
-- Name: subtask_time_tracking_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY subtask_time_tracking
    ADD CONSTRAINT subtask_time_tracking_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: swimlanes_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY swimlanes
    ADD CONSTRAINT swimlanes_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: task_has_files_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY files
    ADD CONSTRAINT task_has_files_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: task_has_links_link_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY task_has_links
    ADD CONSTRAINT task_has_links_link_id_fkey FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE;


--
-- Name: task_has_links_opposite_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY task_has_links
    ADD CONSTRAINT task_has_links_opposite_task_id_fkey FOREIGN KEY (opposite_task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: task_has_links_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY task_has_links
    ADD CONSTRAINT task_has_links_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: task_has_metadata_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY task_has_metadata
    ADD CONSTRAINT task_has_metadata_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: task_has_subtasks_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY subtasks
    ADD CONSTRAINT task_has_subtasks_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: tasks_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY tasks
    ADD CONSTRAINT tasks_column_id_fkey FOREIGN KEY (column_id) REFERENCES columns(id) ON DELETE CASCADE;


--
-- Name: tasks_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY tasks
    ADD CONSTRAINT tasks_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: transitions_dst_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_dst_column_id_fkey FOREIGN KEY (dst_column_id) REFERENCES columns(id) ON DELETE CASCADE;


--
-- Name: transitions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: transitions_src_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_src_column_id_fkey FOREIGN KEY (src_column_id) REFERENCES columns(id) ON DELETE CASCADE;


--
-- Name: transitions_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_task_id_fkey FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE;


--
-- Name: transitions_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY transitions
    ADD CONSTRAINT transitions_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: user_has_metadata_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_metadata
    ADD CONSTRAINT user_has_metadata_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: user_has_notification_types_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_notification_types
    ADD CONSTRAINT user_has_notification_types_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: user_has_notifications_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_notifications
    ADD CONSTRAINT user_has_notifications_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;


--
-- Name: user_has_notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_notifications
    ADD CONSTRAINT user_has_notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: user_has_unread_notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_has_unread_notifications
    ADD CONSTRAINT user_has_unread_notifications_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM fred;
GRANT ALL ON SCHEMA public TO fred;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO settings (option, value) VALUES ('board_highlight_period', '172800');
INSERT INTO settings (option, value) VALUES ('board_public_refresh_interval', '60');
INSERT INTO settings (option, value) VALUES ('board_private_refresh_interval', '10');
INSERT INTO settings (option, value) VALUES ('board_columns', '');
INSERT INTO settings (option, value) VALUES ('webhook_token', '29877f0b69d230e57bee9d02e0aa9034a69f7a2c0ba1e3b5d3b390241f36');
INSERT INTO settings (option, value) VALUES ('api_token', '5682955e965bd0cd7618559a25131fe6094d9fff3bb56c31291d64991353');
INSERT INTO settings (option, value) VALUES ('application_language', 'en_US');
INSERT INTO settings (option, value) VALUES ('application_timezone', 'UTC');
INSERT INTO settings (option, value) VALUES ('application_url', '');
INSERT INTO settings (option, value) VALUES ('application_date_format', 'm/d/Y');
INSERT INTO settings (option, value) VALUES ('project_categories', '');
INSERT INTO settings (option, value) VALUES ('subtask_restriction', '0');
INSERT INTO settings (option, value) VALUES ('application_stylesheet', '');
INSERT INTO settings (option, value) VALUES ('application_currency', 'USD');
INSERT INTO settings (option, value) VALUES ('integration_gravatar', '0');
INSERT INTO settings (option, value) VALUES ('calendar_user_subtasks_time_tracking', '0');
INSERT INTO settings (option, value) VALUES ('calendar_user_tasks', 'date_started');
INSERT INTO settings (option, value) VALUES ('calendar_project_tasks', 'date_started');
INSERT INTO settings (option, value) VALUES ('webhook_url', '');
INSERT INTO settings (option, value) VALUES ('default_color', 'yellow');
INSERT INTO settings (option, value) VALUES ('subtask_time_tracking', '1');
INSERT INTO settings (option, value) VALUES ('cfd_include_closed_tasks', '1');


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: links; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO links (id, label, opposite_id) VALUES (1, 'relates to', 0);
INSERT INTO links (id, label, opposite_id) VALUES (2, 'blocks', 3);
INSERT INTO links (id, label, opposite_id) VALUES (3, 'is blocked by', 2);
INSERT INTO links (id, label, opposite_id) VALUES (4, 'duplicates', 5);
INSERT INTO links (id, label, opposite_id) VALUES (5, 'is duplicated by', 4);
INSERT INTO links (id, label, opposite_id) VALUES (6, 'is a child of', 7);
INSERT INTO links (id, label, opposite_id) VALUES (7, 'is a parent of', 6);
INSERT INTO links (id, label, opposite_id) VALUES (8, 'targets milestone', 9);
INSERT INTO links (id, label, opposite_id) VALUES (9, 'is a milestone of', 8);
INSERT INTO links (id, label, opposite_id) VALUES (10, 'fixes', 11);
INSERT INTO links (id, label, opposite_id) VALUES (11, 'is fixed by', 10);


--
-- Name: links_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('links_id_seq', 11, true);


--
-- PostgreSQL database dump complete
--

INSERT INTO users (username, password, is_admin) VALUES ('admin', '$2y$10$fDbO.nKAjDxm70DyghADCuqIhF919BAkRTAq0bARDTGwcxZscqIZq', '1');INSERT INTO schema_version VALUES ('73');
