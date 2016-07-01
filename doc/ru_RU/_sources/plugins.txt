Plugin Development
==================

Note: The plugin API is **considered alpha** at the moment.

Plugins are useful to extend the core functionalities of Kanboard,
adding features, creating themes or changing the default behavior.

Plugin creators should specify explicitly the compatible versions of
Kanboard. Internal code of Kanboard may change over time and your plugin
must be tested with new versions. Always check the
`ChangeLog <https://github.com/fguillot/kanboard/blob/master/ChangeLog>`__
for breaking changes.

-  `Creating your plugin <plugin-registration.markdown>`__
-  `Using plugin hooks <plugin-hooks.markdown>`__
-  `Events <plugin-events.markdown>`__
-  `Override default application
   behaviors <plugin-overrides.markdown>`__
-  `Add schema migrations for
   plugins <plugin-schema-migrations.markdown>`__
-  `Custom routes <plugin-routes.markdown>`__
-  `Add helpers <plugin-helpers.markdown>`__
-  `Add mail transports <plugin-mail-transports.markdown>`__
-  `Add notification types <plugin-notifications.markdown>`__
-  `Add automatic actions <plugin-automatic-actions.markdown>`__
-  `Attach metadata to users, tasks and
   projects <plugin-metadata.markdown>`__
-  `Authentication
   architecture <plugin-authentication-architecture.markdown>`__
-  `Authentication plugin
   registration <plugin-authentication.markdown>`__
-  `Authorization
   architecture <plugin-authorization-architecture.markdown>`__
-  `Custom group providers <plugin-group-provider.markdown>`__
-  `External link providers <plugin-external-link.markdown>`__
-  `Add avatar providers <plugin-avatar-provider.markdown>`__
-  `LDAP client <plugin-ldap-client.markdown>`__

Examples of plugins
-------------------

-  `SMS Two-Factor
   Authentication <https://github.com/kanboard/plugin-sms-2fa>`__
-  `Reverse-Proxy Authentication with LDAP
   support <https://github.com/kanboard/plugin-reverse-proxy-ldap>`__
-  `Slack <https://github.com/kanboard/plugin-slack>`__
-  `Hipchat <https://github.com/kanboard/plugin-hipchat>`__
-  `Jabber <https://github.com/kanboard/plugin-jabber>`__
-  `Sendgrid <https://github.com/kanboard/plugin-sendgrid>`__
-  `Mailgun <https://github.com/kanboard/plugin-mailgun>`__
-  `Postmark <https://github.com/kanboard/plugin-postmark>`__
-  `Amazon S3 <https://github.com/kanboard/plugin-s3>`__
-  `Budget planning <https://github.com/kanboard/plugin-budget>`__
-  `User timetables <https://github.com/kanboard/plugin-timetable>`__
-  `Subtask
   Forecast <https://github.com/kanboard/plugin-subtask-forecast>`__
-  `Automatic Action
   example <https://github.com/kanboard/plugin-example-automatic-action>`__
-  `Theme plugin
   example <https://github.com/kanboard/plugin-example-theme>`__
-  `CSS plugin
   example <https://github.com/kanboard/plugin-example-css>`__

