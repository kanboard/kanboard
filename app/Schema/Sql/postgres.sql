--
-- PostgreSQL database dump
--

-- Dumped from database version 10.5
-- Dumped by pg_dump version 10.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: SCHEMA "public"; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA "public" IS 'standard public schema';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: action_has_params; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."action_has_params" (
    "id" integer NOT NULL,
    "action_id" integer NOT NULL,
    "name" "text" NOT NULL,
    "value" "text" NOT NULL
);


--
-- Name: action_has_params_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."action_has_params_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: action_has_params_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."action_has_params_id_seq" OWNED BY "public"."action_has_params"."id";


--
-- Name: actions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."actions" (
    "id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "event_name" "text" NOT NULL,
    "action_name" "text" NOT NULL
);


--
-- Name: actions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."actions_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: actions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."actions_id_seq" OWNED BY "public"."actions"."id";


--
-- Name: column_has_move_restrictions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."column_has_move_restrictions" (
    "restriction_id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "role_id" integer NOT NULL,
    "src_column_id" integer NOT NULL,
    "dst_column_id" integer NOT NULL,
    "only_assigned" boolean DEFAULT false
);


--
-- Name: column_has_move_restrictions_restriction_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."column_has_move_restrictions_restriction_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: column_has_move_restrictions_restriction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."column_has_move_restrictions_restriction_id_seq" OWNED BY "public"."column_has_move_restrictions"."restriction_id";


--
-- Name: column_has_restrictions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."column_has_restrictions" (
    "restriction_id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "role_id" integer NOT NULL,
    "column_id" integer NOT NULL,
    "rule" character varying(255) NOT NULL
);


--
-- Name: column_has_restrictions_restriction_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."column_has_restrictions_restriction_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: column_has_restrictions_restriction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."column_has_restrictions_restriction_id_seq" OWNED BY "public"."column_has_restrictions"."restriction_id";


--
-- Name: columns; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."columns" (
    "id" integer NOT NULL,
    "title" character varying(255) NOT NULL,
    "position" integer,
    "project_id" integer NOT NULL,
    "task_limit" integer DEFAULT 0,
    "description" "text",
    "hide_in_dashboard" boolean DEFAULT false
);


--
-- Name: columns_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."columns_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: columns_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."columns_id_seq" OWNED BY "public"."columns"."id";


--
-- Name: comments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."comments" (
    "id" integer NOT NULL,
    "task_id" integer NOT NULL,
    "user_id" integer DEFAULT 0,
    "date_creation" bigint NOT NULL,
    "comment" "text",
    "reference" "text" DEFAULT ''::character varying,
    "date_modification" bigint
);


--
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."comments_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."comments_id_seq" OWNED BY "public"."comments"."id";


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."currencies" (
    "currency" character(3) NOT NULL,
    "rate" real DEFAULT 0
);


--
-- Name: custom_filters; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."custom_filters" (
    "id" integer NOT NULL,
    "filter" "text" NOT NULL,
    "project_id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "name" "text" NOT NULL,
    "is_shared" boolean DEFAULT false,
    "append" boolean DEFAULT false
);


--
-- Name: custom_filters_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."custom_filters_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: custom_filters_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."custom_filters_id_seq" OWNED BY "public"."custom_filters"."id";


--
-- Name: group_has_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."group_has_users" (
    "group_id" integer NOT NULL,
    "user_id" integer NOT NULL
);


--
-- Name: groups; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."groups" (
    "id" integer NOT NULL,
    "external_id" character varying(255) DEFAULT ''::character varying,
    "name" "text" NOT NULL
);


--
-- Name: groups_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."groups_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."groups_id_seq" OWNED BY "public"."groups"."id";


--
-- Name: invites; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."invites" (
    "email" character varying(255) NOT NULL,
    "project_id" integer NOT NULL,
    "token" character varying(255) NOT NULL
);


--
-- Name: last_logins; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."last_logins" (
    "id" integer NOT NULL,
    "auth_type" character varying(25),
    "user_id" integer,
    "ip" character varying(45),
    "user_agent" character varying(255),
    "date_creation" bigint
);


--
-- Name: last_logins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."last_logins_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: last_logins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."last_logins_id_seq" OWNED BY "public"."last_logins"."id";


--
-- Name: links; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."links" (
    "id" integer NOT NULL,
    "label" character varying(255) NOT NULL,
    "opposite_id" integer DEFAULT 0
);


--
-- Name: links_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."links_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."links_id_seq" OWNED BY "public"."links"."id";


--
-- Name: password_reset; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."password_reset" (
    "token" character varying(80) NOT NULL,
    "user_id" integer NOT NULL,
    "date_expiration" integer NOT NULL,
    "date_creation" integer NOT NULL,
    "ip" character varying(45) NOT NULL,
    "user_agent" character varying(255) NOT NULL,
    "is_active" boolean NOT NULL
);


--
-- Name: plugin_schema_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."plugin_schema_versions" (
    "plugin" character varying(80) NOT NULL,
    "version" integer DEFAULT 0 NOT NULL
);


--
-- Name: predefined_task_descriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."predefined_task_descriptions" (
    "id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "title" "text" NOT NULL,
    "description" "text" NOT NULL
);


--
-- Name: predefined_task_descriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."predefined_task_descriptions_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: predefined_task_descriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."predefined_task_descriptions_id_seq" OWNED BY "public"."predefined_task_descriptions"."id";


--
-- Name: project_activities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_activities" (
    "id" integer NOT NULL,
    "date_creation" bigint NOT NULL,
    "event_name" "text" NOT NULL,
    "creator_id" integer,
    "project_id" integer,
    "task_id" integer,
    "data" "text"
);


--
-- Name: project_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_activities_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_activities_id_seq" OWNED BY "public"."project_activities"."id";


--
-- Name: project_daily_column_stats; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_daily_column_stats" (
    "id" integer NOT NULL,
    "day" character(10) NOT NULL,
    "project_id" integer NOT NULL,
    "column_id" integer NOT NULL,
    "total" integer DEFAULT 0 NOT NULL,
    "score" integer DEFAULT 0 NOT NULL
);


--
-- Name: project_daily_stats; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_daily_stats" (
    "id" integer NOT NULL,
    "day" character(10) NOT NULL,
    "project_id" integer NOT NULL,
    "avg_lead_time" integer DEFAULT 0 NOT NULL,
    "avg_cycle_time" integer DEFAULT 0 NOT NULL
);


--
-- Name: project_daily_stats_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_daily_stats_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_daily_stats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_daily_stats_id_seq" OWNED BY "public"."project_daily_stats"."id";


--
-- Name: project_daily_summaries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_daily_summaries_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_daily_summaries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_daily_summaries_id_seq" OWNED BY "public"."project_daily_column_stats"."id";


--
-- Name: project_has_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_categories" (
    "id" integer NOT NULL,
    "name" character varying(255) NOT NULL,
    "project_id" integer NOT NULL,
    "description" "text",
    "color_id" character varying(50) DEFAULT NULL::character varying
);


--
-- Name: project_has_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_has_categories_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_has_categories_id_seq" OWNED BY "public"."project_has_categories"."id";


--
-- Name: project_has_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_files" (
    "id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "name" "text" NOT NULL,
    "path" "text" NOT NULL,
    "is_image" boolean DEFAULT false,
    "size" integer DEFAULT 0 NOT NULL,
    "user_id" integer DEFAULT 0 NOT NULL,
    "date" integer DEFAULT 0 NOT NULL
);


--
-- Name: project_has_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_has_files_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_has_files_id_seq" OWNED BY "public"."project_has_files"."id";


--
-- Name: project_has_groups; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_groups" (
    "group_id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "role" character varying(255) NOT NULL
);


--
-- Name: project_has_metadata; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_metadata" (
    "project_id" integer NOT NULL,
    "name" character varying(50) NOT NULL,
    "value" character varying(255) DEFAULT ''::character varying,
    "changed_by" integer DEFAULT 0 NOT NULL,
    "changed_on" integer DEFAULT 0 NOT NULL
);


--
-- Name: project_has_notification_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_notification_types" (
    "id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "notification_type" character varying(50) NOT NULL
);


--
-- Name: project_has_notification_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_has_notification_types_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_notification_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_has_notification_types_id_seq" OWNED BY "public"."project_has_notification_types"."id";


--
-- Name: project_has_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_roles" (
    "role_id" integer NOT NULL,
    "role" character varying(255) NOT NULL,
    "project_id" integer NOT NULL
);


--
-- Name: project_has_roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_has_roles_role_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_has_roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_has_roles_role_id_seq" OWNED BY "public"."project_has_roles"."role_id";


--
-- Name: project_has_users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_has_users" (
    "project_id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "role" character varying(255) DEFAULT 'project-viewer'::character varying NOT NULL
);


--
-- Name: project_role_has_restrictions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."project_role_has_restrictions" (
    "restriction_id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "role_id" integer NOT NULL,
    "rule" character varying(255) NOT NULL
);


--
-- Name: project_role_has_restrictions_restriction_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."project_role_has_restrictions_restriction_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: project_role_has_restrictions_restriction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."project_role_has_restrictions_restriction_id_seq" OWNED BY "public"."project_role_has_restrictions"."restriction_id";


--
-- Name: projects; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."projects" (
    "id" integer NOT NULL,
    "name" "text" NOT NULL,
    "is_active" boolean DEFAULT true,
    "token" character varying(255),
    "last_modified" bigint DEFAULT 0,
    "is_public" boolean DEFAULT false,
    "is_private" boolean DEFAULT false,
    "description" "text",
    "identifier" character varying(50) DEFAULT ''::character varying,
    "start_date" character varying(10) DEFAULT ''::character varying,
    "end_date" character varying(10) DEFAULT ''::character varying,
    "owner_id" integer DEFAULT 0,
    "priority_default" integer DEFAULT 0,
    "priority_start" integer DEFAULT 0,
    "priority_end" integer DEFAULT 3,
    "email" "text",
    "predefined_email_subjects" "text"
);


--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."projects_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."projects_id_seq" OWNED BY "public"."projects"."id";


--
-- Name: remember_me; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."remember_me" (
    "id" integer NOT NULL,
    "user_id" integer,
    "ip" character varying(45),
    "user_agent" character varying(255),
    "token" character varying(255),
    "sequence" character varying(255),
    "expiration" integer,
    "date_creation" bigint
);


--
-- Name: remember_me_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."remember_me_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: remember_me_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."remember_me_id_seq" OWNED BY "public"."remember_me"."id";


--
-- Name: schema_version; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."schema_version" (
    "version" integer DEFAULT 0
);


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."sessions" (
    "id" "text" NOT NULL,
    "expire_at" integer NOT NULL,
    "data" "text" DEFAULT ''::"text"
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."settings" (
    "option" character varying(100) NOT NULL,
    "value" "text" DEFAULT ''::character varying,
    "changed_by" integer DEFAULT 0 NOT NULL,
    "changed_on" integer DEFAULT 0 NOT NULL
);


--
-- Name: subtask_time_tracking; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."subtask_time_tracking" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "subtask_id" integer NOT NULL,
    "start" bigint DEFAULT 0,
    "end" bigint DEFAULT 0,
    "time_spent" real DEFAULT 0
);


--
-- Name: subtask_time_tracking_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."subtask_time_tracking_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subtask_time_tracking_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."subtask_time_tracking_id_seq" OWNED BY "public"."subtask_time_tracking"."id";


--
-- Name: subtasks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."subtasks" (
    "id" integer NOT NULL,
    "title" "text" NOT NULL,
    "status" smallint DEFAULT 0,
    "time_estimated" double precision DEFAULT 0,
    "time_spent" double precision DEFAULT 0,
    "task_id" integer NOT NULL,
    "user_id" integer,
    "position" integer DEFAULT 1
);


--
-- Name: swimlanes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."swimlanes" (
    "id" integer NOT NULL,
    "name" "text" NOT NULL,
    "position" integer DEFAULT 1,
    "is_active" boolean DEFAULT true,
    "project_id" integer,
    "description" "text"
);


--
-- Name: swimlanes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."swimlanes_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: swimlanes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."swimlanes_id_seq" OWNED BY "public"."swimlanes"."id";


--
-- Name: tags; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."tags" (
    "id" integer NOT NULL,
    "name" character varying(255) NOT NULL,
    "project_id" integer NOT NULL,
    "color_id" character varying(50) DEFAULT NULL::character varying
);


--
-- Name: tags_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."tags_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tags_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."tags_id_seq" OWNED BY "public"."tags"."id";


--
-- Name: task_has_external_links; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."task_has_external_links" (
    "id" integer NOT NULL,
    "link_type" character varying(100) NOT NULL,
    "dependency" character varying(100) NOT NULL,
    "title" "text" NOT NULL,
    "url" "text" NOT NULL,
    "date_creation" integer NOT NULL,
    "date_modification" integer NOT NULL,
    "task_id" integer NOT NULL,
    "creator_id" integer DEFAULT 0
);


--
-- Name: task_has_external_links_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."task_has_external_links_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_external_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."task_has_external_links_id_seq" OWNED BY "public"."task_has_external_links"."id";


--
-- Name: task_has_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."task_has_files" (
    "id" integer NOT NULL,
    "name" "text" NOT NULL,
    "path" "text",
    "is_image" boolean DEFAULT false,
    "task_id" integer NOT NULL,
    "date" bigint DEFAULT 0 NOT NULL,
    "user_id" integer DEFAULT 0 NOT NULL,
    "size" integer DEFAULT 0 NOT NULL
);


--
-- Name: task_has_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."task_has_files_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."task_has_files_id_seq" OWNED BY "public"."task_has_files"."id";


--
-- Name: task_has_links; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."task_has_links" (
    "id" integer NOT NULL,
    "link_id" integer NOT NULL,
    "task_id" integer NOT NULL,
    "opposite_task_id" integer NOT NULL
);


--
-- Name: task_has_links_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."task_has_links_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."task_has_links_id_seq" OWNED BY "public"."task_has_links"."id";


--
-- Name: task_has_metadata; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."task_has_metadata" (
    "task_id" integer NOT NULL,
    "name" character varying(50) NOT NULL,
    "value" character varying(255) DEFAULT ''::character varying,
    "changed_by" integer DEFAULT 0 NOT NULL,
    "changed_on" integer DEFAULT 0 NOT NULL
);


--
-- Name: task_has_subtasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."task_has_subtasks_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: task_has_subtasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."task_has_subtasks_id_seq" OWNED BY "public"."subtasks"."id";


--
-- Name: task_has_tags; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."task_has_tags" (
    "task_id" integer NOT NULL,
    "tag_id" integer NOT NULL
);


--
-- Name: tasks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."tasks" (
    "id" integer NOT NULL,
    "title" "text" NOT NULL,
    "description" "text",
    "date_creation" bigint,
    "color_id" character varying(255),
    "project_id" integer NOT NULL,
    "column_id" integer NOT NULL,
    "owner_id" integer DEFAULT 0,
    "position" integer,
    "is_active" boolean DEFAULT true,
    "date_completed" bigint,
    "score" integer,
    "date_due" bigint,
    "category_id" integer DEFAULT 0,
    "creator_id" integer DEFAULT 0,
    "date_modification" integer DEFAULT 0,
    "reference" "text" DEFAULT ''::character varying,
    "date_started" bigint,
    "time_spent" double precision DEFAULT 0,
    "time_estimated" double precision DEFAULT 0,
    "swimlane_id" integer NOT NULL,
    "date_moved" bigint DEFAULT 0,
    "recurrence_status" integer DEFAULT 0 NOT NULL,
    "recurrence_trigger" integer DEFAULT 0 NOT NULL,
    "recurrence_factor" integer DEFAULT 0 NOT NULL,
    "recurrence_timeframe" integer DEFAULT 0 NOT NULL,
    "recurrence_basedate" integer DEFAULT 0 NOT NULL,
    "recurrence_parent" integer,
    "recurrence_child" integer,
    "priority" integer DEFAULT 0,
    "external_provider" character varying(255),
    "external_uri" character varying(255)
);


--
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."tasks_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tasks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."tasks_id_seq" OWNED BY "public"."tasks"."id";


--
-- Name: transitions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."transitions" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "project_id" integer NOT NULL,
    "task_id" integer NOT NULL,
    "src_column_id" integer NOT NULL,
    "dst_column_id" integer NOT NULL,
    "date" bigint NOT NULL,
    "time_spent" integer DEFAULT 0
);


--
-- Name: transitions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."transitions_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transitions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."transitions_id_seq" OWNED BY "public"."transitions"."id";


--
-- Name: user_has_metadata; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."user_has_metadata" (
    "user_id" integer NOT NULL,
    "name" character varying(50) NOT NULL,
    "value" character varying(255) DEFAULT ''::character varying,
    "changed_by" integer DEFAULT 0 NOT NULL,
    "changed_on" integer DEFAULT 0 NOT NULL
);


--
-- Name: user_has_notification_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."user_has_notification_types" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "notification_type" character varying(50)
);


--
-- Name: user_has_notification_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."user_has_notification_types_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_has_notification_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."user_has_notification_types_id_seq" OWNED BY "public"."user_has_notification_types"."id";


--
-- Name: user_has_notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."user_has_notifications" (
    "user_id" integer NOT NULL,
    "project_id" integer
);


--
-- Name: user_has_unread_notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."user_has_unread_notifications" (
    "id" integer NOT NULL,
    "user_id" integer NOT NULL,
    "date_creation" bigint NOT NULL,
    "event_name" "text" NOT NULL,
    "event_data" "text" NOT NULL
);


--
-- Name: user_has_unread_notifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."user_has_unread_notifications_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_has_unread_notifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."user_has_unread_notifications_id_seq" OWNED BY "public"."user_has_unread_notifications"."id";


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE "public"."users" (
    "id" integer NOT NULL,
    "username" "text" NOT NULL,
    "password" character varying(255),
    "is_ldap_user" boolean DEFAULT false,
    "name" character varying(255),
    "email" character varying(255),
    "google_id" character varying(255),
    "github_id" character varying(30),
    "notifications_enabled" boolean DEFAULT false,
    "timezone" character varying(50),
    "language" character varying(11),
    "disable_login_form" boolean DEFAULT false,
    "twofactor_activated" boolean DEFAULT false,
    "twofactor_secret" character(16),
    "token" character varying(255) DEFAULT ''::character varying,
    "notifications_filter" integer DEFAULT 4,
    "nb_failed_login" integer DEFAULT 0,
    "lock_expiration_date" bigint DEFAULT 0,
    "gitlab_id" integer,
    "role" character varying(25) DEFAULT 'app-user'::character varying NOT NULL,
    "is_active" boolean DEFAULT true,
    "avatar_path" character varying(255),
    "api_access_token" character varying(255) DEFAULT NULL::character varying,
    "filter" "text" DEFAULT NULL::character varying
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE "public"."users_id_seq"
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE "public"."users_id_seq" OWNED BY "public"."users"."id";


--
-- Name: action_has_params id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."action_has_params" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."action_has_params_id_seq"'::"regclass");


--
-- Name: actions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."actions" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."actions_id_seq"'::"regclass");


--
-- Name: column_has_move_restrictions restriction_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions" ALTER COLUMN "restriction_id" SET DEFAULT "nextval"('"public"."column_has_move_restrictions_restriction_id_seq"'::"regclass");


--
-- Name: column_has_restrictions restriction_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions" ALTER COLUMN "restriction_id" SET DEFAULT "nextval"('"public"."column_has_restrictions_restriction_id_seq"'::"regclass");


--
-- Name: columns id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."columns" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."columns_id_seq"'::"regclass");


--
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."comments" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."comments_id_seq"'::"regclass");


--
-- Name: custom_filters id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."custom_filters" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."custom_filters_id_seq"'::"regclass");


--
-- Name: groups id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."groups" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."groups_id_seq"'::"regclass");


--
-- Name: last_logins id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."last_logins" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."last_logins_id_seq"'::"regclass");


--
-- Name: links id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."links" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."links_id_seq"'::"regclass");


--
-- Name: predefined_task_descriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."predefined_task_descriptions" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."predefined_task_descriptions_id_seq"'::"regclass");


--
-- Name: project_activities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_activities" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_activities_id_seq"'::"regclass");


--
-- Name: project_daily_column_stats id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_column_stats" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_daily_summaries_id_seq"'::"regclass");


--
-- Name: project_daily_stats id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_stats" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_daily_stats_id_seq"'::"regclass");


--
-- Name: project_has_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_categories" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_has_categories_id_seq"'::"regclass");


--
-- Name: project_has_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_files" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_has_files_id_seq"'::"regclass");


--
-- Name: project_has_notification_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_notification_types" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."project_has_notification_types_id_seq"'::"regclass");


--
-- Name: project_has_roles role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_roles" ALTER COLUMN "role_id" SET DEFAULT "nextval"('"public"."project_has_roles_role_id_seq"'::"regclass");


--
-- Name: project_role_has_restrictions restriction_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_role_has_restrictions" ALTER COLUMN "restriction_id" SET DEFAULT "nextval"('"public"."project_role_has_restrictions_restriction_id_seq"'::"regclass");


--
-- Name: projects id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."projects" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."projects_id_seq"'::"regclass");


--
-- Name: remember_me id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."remember_me" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."remember_me_id_seq"'::"regclass");


--
-- Name: subtask_time_tracking id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtask_time_tracking" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."subtask_time_tracking_id_seq"'::"regclass");


--
-- Name: subtasks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtasks" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."task_has_subtasks_id_seq"'::"regclass");


--
-- Name: swimlanes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."swimlanes" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."swimlanes_id_seq"'::"regclass");


--
-- Name: tags id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tags" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."tags_id_seq"'::"regclass");


--
-- Name: task_has_external_links id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_external_links" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."task_has_external_links_id_seq"'::"regclass");


--
-- Name: task_has_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_files" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."task_has_files_id_seq"'::"regclass");


--
-- Name: task_has_links id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_links" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."task_has_links_id_seq"'::"regclass");


--
-- Name: tasks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tasks" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."tasks_id_seq"'::"regclass");


--
-- Name: transitions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."transitions_id_seq"'::"regclass");


--
-- Name: user_has_notification_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notification_types" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."user_has_notification_types_id_seq"'::"regclass");


--
-- Name: user_has_unread_notifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_unread_notifications" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."user_has_unread_notifications_id_seq"'::"regclass");


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."users" ALTER COLUMN "id" SET DEFAULT "nextval"('"public"."users_id_seq"'::"regclass");


--
-- Name: action_has_params action_has_params_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."action_has_params"
    ADD CONSTRAINT "action_has_params_pkey" PRIMARY KEY ("id");


--
-- Name: actions actions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."actions"
    ADD CONSTRAINT "actions_pkey" PRIMARY KEY ("id");


--
-- Name: column_has_move_restrictions column_has_move_restrictions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_pkey" PRIMARY KEY ("restriction_id");


--
-- Name: column_has_move_restrictions column_has_move_restrictions_role_id_src_column_id_dst_colu_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_role_id_src_column_id_dst_colu_key" UNIQUE ("role_id", "src_column_id", "dst_column_id");


--
-- Name: column_has_restrictions column_has_restrictions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions"
    ADD CONSTRAINT "column_has_restrictions_pkey" PRIMARY KEY ("restriction_id");


--
-- Name: column_has_restrictions column_has_restrictions_role_id_column_id_rule_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions"
    ADD CONSTRAINT "column_has_restrictions_role_id_column_id_rule_key" UNIQUE ("role_id", "column_id", "rule");


--
-- Name: columns columns_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."columns"
    ADD CONSTRAINT "columns_pkey" PRIMARY KEY ("id");


--
-- Name: columns columns_title_project_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."columns"
    ADD CONSTRAINT "columns_title_project_id_key" UNIQUE ("title", "project_id");


--
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."comments"
    ADD CONSTRAINT "comments_pkey" PRIMARY KEY ("id");


--
-- Name: currencies currencies_currency_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."currencies"
    ADD CONSTRAINT "currencies_currency_key" UNIQUE ("currency");


--
-- Name: custom_filters custom_filters_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."custom_filters"
    ADD CONSTRAINT "custom_filters_pkey" PRIMARY KEY ("id");


--
-- Name: group_has_users group_has_users_group_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."group_has_users"
    ADD CONSTRAINT "group_has_users_group_id_user_id_key" UNIQUE ("group_id", "user_id");


--
-- Name: groups groups_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."groups"
    ADD CONSTRAINT "groups_name_key" UNIQUE ("name");


--
-- Name: groups groups_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."groups"
    ADD CONSTRAINT "groups_pkey" PRIMARY KEY ("id");


--
-- Name: invites invites_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."invites"
    ADD CONSTRAINT "invites_pkey" PRIMARY KEY ("email", "token");


--
-- Name: last_logins last_logins_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."last_logins"
    ADD CONSTRAINT "last_logins_pkey" PRIMARY KEY ("id");


--
-- Name: links links_label_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."links"
    ADD CONSTRAINT "links_label_key" UNIQUE ("label");


--
-- Name: links links_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."links"
    ADD CONSTRAINT "links_pkey" PRIMARY KEY ("id");


--
-- Name: password_reset password_reset_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."password_reset"
    ADD CONSTRAINT "password_reset_pkey" PRIMARY KEY ("token");


--
-- Name: plugin_schema_versions plugin_schema_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."plugin_schema_versions"
    ADD CONSTRAINT "plugin_schema_versions_pkey" PRIMARY KEY ("plugin");


--
-- Name: predefined_task_descriptions predefined_task_descriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."predefined_task_descriptions"
    ADD CONSTRAINT "predefined_task_descriptions_pkey" PRIMARY KEY ("id");


--
-- Name: project_activities project_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_activities"
    ADD CONSTRAINT "project_activities_pkey" PRIMARY KEY ("id");


--
-- Name: project_daily_stats project_daily_stats_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_stats"
    ADD CONSTRAINT "project_daily_stats_pkey" PRIMARY KEY ("id");


--
-- Name: project_daily_column_stats project_daily_summaries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_column_stats"
    ADD CONSTRAINT "project_daily_summaries_pkey" PRIMARY KEY ("id");


--
-- Name: project_has_categories project_has_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_categories"
    ADD CONSTRAINT "project_has_categories_pkey" PRIMARY KEY ("id");


--
-- Name: project_has_categories project_has_categories_project_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_categories"
    ADD CONSTRAINT "project_has_categories_project_id_name_key" UNIQUE ("project_id", "name");


--
-- Name: project_has_files project_has_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_files"
    ADD CONSTRAINT "project_has_files_pkey" PRIMARY KEY ("id");


--
-- Name: project_has_groups project_has_groups_group_id_project_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_groups"
    ADD CONSTRAINT "project_has_groups_group_id_project_id_key" UNIQUE ("group_id", "project_id");


--
-- Name: project_has_metadata project_has_metadata_project_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_metadata"
    ADD CONSTRAINT "project_has_metadata_project_id_name_key" UNIQUE ("project_id", "name");


--
-- Name: project_has_notification_types project_has_notification_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_notification_types"
    ADD CONSTRAINT "project_has_notification_types_pkey" PRIMARY KEY ("id");


--
-- Name: project_has_notification_types project_has_notification_types_project_id_notification_type_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_notification_types"
    ADD CONSTRAINT "project_has_notification_types_project_id_notification_type_key" UNIQUE ("project_id", "notification_type");


--
-- Name: project_has_roles project_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_roles"
    ADD CONSTRAINT "project_has_roles_pkey" PRIMARY KEY ("role_id");


--
-- Name: project_has_roles project_has_roles_project_id_role_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_roles"
    ADD CONSTRAINT "project_has_roles_project_id_role_key" UNIQUE ("project_id", "role");


--
-- Name: project_has_users project_has_users_project_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_users"
    ADD CONSTRAINT "project_has_users_project_id_user_id_key" UNIQUE ("project_id", "user_id");


--
-- Name: project_role_has_restrictions project_role_has_restrictions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_role_has_restrictions"
    ADD CONSTRAINT "project_role_has_restrictions_pkey" PRIMARY KEY ("restriction_id");


--
-- Name: project_role_has_restrictions project_role_has_restrictions_role_id_rule_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_role_has_restrictions"
    ADD CONSTRAINT "project_role_has_restrictions_role_id_rule_key" UNIQUE ("role_id", "rule");


--
-- Name: projects projects_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."projects"
    ADD CONSTRAINT "projects_pkey" PRIMARY KEY ("id");


--
-- Name: remember_me remember_me_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."remember_me"
    ADD CONSTRAINT "remember_me_pkey" PRIMARY KEY ("id");


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."sessions"
    ADD CONSTRAINT "sessions_pkey" PRIMARY KEY ("id");


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."settings"
    ADD CONSTRAINT "settings_pkey" PRIMARY KEY ("option");


--
-- Name: subtask_time_tracking subtask_time_tracking_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtask_time_tracking"
    ADD CONSTRAINT "subtask_time_tracking_pkey" PRIMARY KEY ("id");


--
-- Name: swimlanes swimlanes_name_project_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."swimlanes"
    ADD CONSTRAINT "swimlanes_name_project_id_key" UNIQUE ("name", "project_id");


--
-- Name: swimlanes swimlanes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."swimlanes"
    ADD CONSTRAINT "swimlanes_pkey" PRIMARY KEY ("id");


--
-- Name: tags tags_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tags"
    ADD CONSTRAINT "tags_pkey" PRIMARY KEY ("id");


--
-- Name: tags tags_project_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tags"
    ADD CONSTRAINT "tags_project_id_name_key" UNIQUE ("project_id", "name");


--
-- Name: task_has_external_links task_has_external_links_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_external_links"
    ADD CONSTRAINT "task_has_external_links_pkey" PRIMARY KEY ("id");


--
-- Name: task_has_files task_has_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_files"
    ADD CONSTRAINT "task_has_files_pkey" PRIMARY KEY ("id");


--
-- Name: task_has_links task_has_links_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_links"
    ADD CONSTRAINT "task_has_links_pkey" PRIMARY KEY ("id");


--
-- Name: task_has_metadata task_has_metadata_task_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_metadata"
    ADD CONSTRAINT "task_has_metadata_task_id_name_key" UNIQUE ("task_id", "name");


--
-- Name: subtasks task_has_subtasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtasks"
    ADD CONSTRAINT "task_has_subtasks_pkey" PRIMARY KEY ("id");


--
-- Name: task_has_tags task_has_tags_tag_id_task_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_tags"
    ADD CONSTRAINT "task_has_tags_tag_id_task_id_key" UNIQUE ("tag_id", "task_id");


--
-- Name: tasks tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tasks"
    ADD CONSTRAINT "tasks_pkey" PRIMARY KEY ("id");


--
-- Name: transitions transitions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_pkey" PRIMARY KEY ("id");


--
-- Name: user_has_metadata user_has_metadata_user_id_name_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_metadata"
    ADD CONSTRAINT "user_has_metadata_user_id_name_key" UNIQUE ("user_id", "name");


--
-- Name: user_has_notification_types user_has_notification_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notification_types"
    ADD CONSTRAINT "user_has_notification_types_pkey" PRIMARY KEY ("id");


--
-- Name: user_has_notifications user_has_notifications_project_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notifications"
    ADD CONSTRAINT "user_has_notifications_project_id_user_id_key" UNIQUE ("project_id", "user_id");


--
-- Name: user_has_unread_notifications user_has_unread_notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_unread_notifications"
    ADD CONSTRAINT "user_has_unread_notifications_pkey" PRIMARY KEY ("id");


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."users"
    ADD CONSTRAINT "users_pkey" PRIMARY KEY ("id");


--
-- Name: categories_project_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "categories_project_idx" ON "public"."project_has_categories" USING "btree" ("project_id");


--
-- Name: columns_project_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "columns_project_idx" ON "public"."columns" USING "btree" ("project_id");


--
-- Name: comments_reference_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "comments_reference_idx" ON "public"."comments" USING "btree" ("reference");


--
-- Name: comments_task_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "comments_task_idx" ON "public"."comments" USING "btree" ("task_id");


--
-- Name: files_task_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "files_task_idx" ON "public"."task_has_files" USING "btree" ("task_id");


--
-- Name: project_daily_column_stats_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "project_daily_column_stats_idx" ON "public"."project_daily_column_stats" USING "btree" ("day", "project_id", "column_id");


--
-- Name: project_daily_stats_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "project_daily_stats_idx" ON "public"."project_daily_stats" USING "btree" ("day", "project_id");


--
-- Name: subtasks_task_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "subtasks_task_idx" ON "public"."subtasks" USING "btree" ("task_id");


--
-- Name: swimlanes_project_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "swimlanes_project_idx" ON "public"."swimlanes" USING "btree" ("project_id");


--
-- Name: task_has_links_task_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "task_has_links_task_index" ON "public"."task_has_links" USING "btree" ("task_id");


--
-- Name: task_has_links_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "task_has_links_unique" ON "public"."task_has_links" USING "btree" ("link_id", "task_id", "opposite_task_id");


--
-- Name: tasks_project_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "tasks_project_idx" ON "public"."tasks" USING "btree" ("project_id");


--
-- Name: tasks_reference_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "tasks_reference_idx" ON "public"."tasks" USING "btree" ("reference");


--
-- Name: transitions_project_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "transitions_project_index" ON "public"."transitions" USING "btree" ("project_id");


--
-- Name: transitions_task_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "transitions_task_index" ON "public"."transitions" USING "btree" ("task_id");


--
-- Name: transitions_user_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX "transitions_user_index" ON "public"."transitions" USING "btree" ("user_id");


--
-- Name: user_has_notification_types_user_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "user_has_notification_types_user_idx" ON "public"."user_has_notification_types" USING "btree" ("user_id", "notification_type");


--
-- Name: users_username_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX "users_username_idx" ON "public"."users" USING "btree" ("username");


--
-- Name: action_has_params action_has_params_action_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."action_has_params"
    ADD CONSTRAINT "action_has_params_action_id_fkey" FOREIGN KEY ("action_id") REFERENCES "public"."actions"("id") ON DELETE CASCADE;


--
-- Name: actions actions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."actions"
    ADD CONSTRAINT "actions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: column_has_move_restrictions column_has_move_restrictions_dst_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_dst_column_id_fkey" FOREIGN KEY ("dst_column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: column_has_move_restrictions column_has_move_restrictions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: column_has_move_restrictions column_has_move_restrictions_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "public"."project_has_roles"("role_id") ON DELETE CASCADE;


--
-- Name: column_has_move_restrictions column_has_move_restrictions_src_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_move_restrictions"
    ADD CONSTRAINT "column_has_move_restrictions_src_column_id_fkey" FOREIGN KEY ("src_column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: column_has_restrictions column_has_restrictions_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions"
    ADD CONSTRAINT "column_has_restrictions_column_id_fkey" FOREIGN KEY ("column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: column_has_restrictions column_has_restrictions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions"
    ADD CONSTRAINT "column_has_restrictions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: column_has_restrictions column_has_restrictions_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."column_has_restrictions"
    ADD CONSTRAINT "column_has_restrictions_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "public"."project_has_roles"("role_id") ON DELETE CASCADE;


--
-- Name: columns columns_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."columns"
    ADD CONSTRAINT "columns_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: comments comments_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."comments"
    ADD CONSTRAINT "comments_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: group_has_users group_has_users_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."group_has_users"
    ADD CONSTRAINT "group_has_users_group_id_fkey" FOREIGN KEY ("group_id") REFERENCES "public"."groups"("id") ON DELETE CASCADE;


--
-- Name: group_has_users group_has_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."group_has_users"
    ADD CONSTRAINT "group_has_users_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: last_logins last_logins_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."last_logins"
    ADD CONSTRAINT "last_logins_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: password_reset password_reset_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."password_reset"
    ADD CONSTRAINT "password_reset_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: predefined_task_descriptions predefined_task_descriptions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."predefined_task_descriptions"
    ADD CONSTRAINT "predefined_task_descriptions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_activities project_activities_creator_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_activities"
    ADD CONSTRAINT "project_activities_creator_id_fkey" FOREIGN KEY ("creator_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: project_activities project_activities_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_activities"
    ADD CONSTRAINT "project_activities_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_activities project_activities_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_activities"
    ADD CONSTRAINT "project_activities_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: project_daily_stats project_daily_stats_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_stats"
    ADD CONSTRAINT "project_daily_stats_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_daily_column_stats project_daily_summaries_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_column_stats"
    ADD CONSTRAINT "project_daily_summaries_column_id_fkey" FOREIGN KEY ("column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: project_daily_column_stats project_daily_summaries_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_daily_column_stats"
    ADD CONSTRAINT "project_daily_summaries_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_categories project_has_categories_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_categories"
    ADD CONSTRAINT "project_has_categories_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_files project_has_files_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_files"
    ADD CONSTRAINT "project_has_files_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_groups project_has_groups_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_groups"
    ADD CONSTRAINT "project_has_groups_group_id_fkey" FOREIGN KEY ("group_id") REFERENCES "public"."groups"("id") ON DELETE CASCADE;


--
-- Name: project_has_groups project_has_groups_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_groups"
    ADD CONSTRAINT "project_has_groups_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_metadata project_has_metadata_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_metadata"
    ADD CONSTRAINT "project_has_metadata_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_notification_types project_has_notification_types_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_notification_types"
    ADD CONSTRAINT "project_has_notification_types_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_roles project_has_roles_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_roles"
    ADD CONSTRAINT "project_has_roles_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_users project_has_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_users"
    ADD CONSTRAINT "project_has_users_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_has_users project_has_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_has_users"
    ADD CONSTRAINT "project_has_users_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: project_role_has_restrictions project_role_has_restrictions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_role_has_restrictions"
    ADD CONSTRAINT "project_role_has_restrictions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: project_role_has_restrictions project_role_has_restrictions_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."project_role_has_restrictions"
    ADD CONSTRAINT "project_role_has_restrictions_role_id_fkey" FOREIGN KEY ("role_id") REFERENCES "public"."project_has_roles"("role_id") ON DELETE CASCADE;


--
-- Name: remember_me remember_me_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."remember_me"
    ADD CONSTRAINT "remember_me_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: subtask_time_tracking subtask_time_tracking_subtask_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtask_time_tracking"
    ADD CONSTRAINT "subtask_time_tracking_subtask_id_fkey" FOREIGN KEY ("subtask_id") REFERENCES "public"."subtasks"("id") ON DELETE CASCADE;


--
-- Name: subtask_time_tracking subtask_time_tracking_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtask_time_tracking"
    ADD CONSTRAINT "subtask_time_tracking_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: swimlanes swimlanes_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."swimlanes"
    ADD CONSTRAINT "swimlanes_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: task_has_external_links task_has_external_links_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_external_links"
    ADD CONSTRAINT "task_has_external_links_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: task_has_files task_has_files_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_files"
    ADD CONSTRAINT "task_has_files_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: task_has_links task_has_links_link_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_links"
    ADD CONSTRAINT "task_has_links_link_id_fkey" FOREIGN KEY ("link_id") REFERENCES "public"."links"("id") ON DELETE CASCADE;


--
-- Name: task_has_links task_has_links_opposite_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_links"
    ADD CONSTRAINT "task_has_links_opposite_task_id_fkey" FOREIGN KEY ("opposite_task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: task_has_links task_has_links_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_links"
    ADD CONSTRAINT "task_has_links_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: task_has_metadata task_has_metadata_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_metadata"
    ADD CONSTRAINT "task_has_metadata_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: subtasks task_has_subtasks_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."subtasks"
    ADD CONSTRAINT "task_has_subtasks_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: task_has_tags task_has_tags_tag_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_tags"
    ADD CONSTRAINT "task_has_tags_tag_id_fkey" FOREIGN KEY ("tag_id") REFERENCES "public"."tags"("id") ON DELETE CASCADE;


--
-- Name: task_has_tags task_has_tags_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."task_has_tags"
    ADD CONSTRAINT "task_has_tags_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: tasks tasks_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tasks"
    ADD CONSTRAINT "tasks_column_id_fkey" FOREIGN KEY ("column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: tasks tasks_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tasks"
    ADD CONSTRAINT "tasks_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: tasks tasks_swimlane_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."tasks"
    ADD CONSTRAINT "tasks_swimlane_id_fkey" FOREIGN KEY ("swimlane_id") REFERENCES "public"."swimlanes"("id") ON DELETE CASCADE;


--
-- Name: transitions transitions_dst_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_dst_column_id_fkey" FOREIGN KEY ("dst_column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: transitions transitions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: transitions transitions_src_column_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_src_column_id_fkey" FOREIGN KEY ("src_column_id") REFERENCES "public"."columns"("id") ON DELETE CASCADE;


--
-- Name: transitions transitions_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_task_id_fkey" FOREIGN KEY ("task_id") REFERENCES "public"."tasks"("id") ON DELETE CASCADE;


--
-- Name: transitions transitions_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."transitions"
    ADD CONSTRAINT "transitions_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: user_has_metadata user_has_metadata_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_metadata"
    ADD CONSTRAINT "user_has_metadata_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: user_has_notification_types user_has_notification_types_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notification_types"
    ADD CONSTRAINT "user_has_notification_types_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: user_has_notifications user_has_notifications_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notifications"
    ADD CONSTRAINT "user_has_notifications_project_id_fkey" FOREIGN KEY ("project_id") REFERENCES "public"."projects"("id") ON DELETE CASCADE;


--
-- Name: user_has_notifications user_has_notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_notifications"
    ADD CONSTRAINT "user_has_notifications_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- Name: user_has_unread_notifications user_has_unread_notifications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY "public"."user_has_unread_notifications"
    ADD CONSTRAINT "user_has_unread_notifications_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."users"("id") ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 10.5
-- Dumped by pg_dump version 10.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('board_highlight_period', '172800', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('board_public_refresh_interval', '60', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('board_private_refresh_interval', '10', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('board_columns', '', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('webhook_token', '1a9fe6b6651d4f17db363279ec08b6b44c8ee4f205d0c9527848a648436c', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('api_token', '8e6d6c81e25529d4d83e8b30385922ce1a99af3908743e888aa87344f8f3', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_language', 'en_US', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_timezone', 'UTC', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_url', '', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_date_format', 'm/d/Y', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('project_categories', '', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('subtask_restriction', '0', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_stylesheet', '', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('application_currency', 'USD', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('integration_gravatar', '0', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('calendar_user_subtasks_time_tracking', '0', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('calendar_user_tasks', 'date_started', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('calendar_project_tasks', 'date_started', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('webhook_url', '', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('default_color', 'yellow', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('subtask_time_tracking', '1', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('cfd_include_closed_tasks', '1', 0, 0);
INSERT INTO public.settings (option, value, changed_by, changed_on) VALUES ('password_reset', '1', 0, 0);


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 10.5
-- Dumped by pg_dump version 10.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: links; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.links (id, label, opposite_id) VALUES (1, 'relates to', 0);
INSERT INTO public.links (id, label, opposite_id) VALUES (2, 'blocks', 3);
INSERT INTO public.links (id, label, opposite_id) VALUES (3, 'is blocked by', 2);
INSERT INTO public.links (id, label, opposite_id) VALUES (4, 'duplicates', 5);
INSERT INTO public.links (id, label, opposite_id) VALUES (5, 'is duplicated by', 4);
INSERT INTO public.links (id, label, opposite_id) VALUES (6, 'is a child of', 7);
INSERT INTO public.links (id, label, opposite_id) VALUES (7, 'is a parent of', 6);
INSERT INTO public.links (id, label, opposite_id) VALUES (8, 'targets milestone', 9);
INSERT INTO public.links (id, label, opposite_id) VALUES (9, 'is a milestone of', 8);
INSERT INTO public.links (id, label, opposite_id) VALUES (10, 'fixes', 11);
INSERT INTO public.links (id, label, opposite_id) VALUES (11, 'is fixed by', 10);


--
-- Name: links_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.links_id_seq', 11, true);


--
-- PostgreSQL database dump complete
--

INSERT INTO public.users (username, password, role) VALUES ('admin', '$2y$10$GzDCeQl/GdH.pCZfz4fWdO3qmayutRCmxEIY9U9t1k9q9F89VNDCm', 'app-admin');
INSERT INTO public.schema_version VALUES ('111');
