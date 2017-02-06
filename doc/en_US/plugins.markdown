Plugin Development
==================

Note: The plugin API is **considered alpha** at the moment.

Plugins are useful to extend the core functionalities of Kanboard, adding features, creating themes or changing the default behavior.

Plugin creators should specify explicitly the compatible versions of Kanboard. Internal code of Kanboard may change over time and your plugin must be tested with new versions. Always check the [ChangeLog](https://github.com/kanboard/kanboard/blob/master/ChangeLog) for breaking changes.

- [Creating your plugin](plugin-registration.markdown)
- [Using plugin hooks](plugin-hooks.markdown)
- [Events](plugin-events.markdown)
- [Override default application behaviors](plugin-overrides.markdown)
- [Add schema migrations for plugins](plugin-schema-migrations.markdown)
- [Custom routes](plugin-routes.markdown)
- [Add helpers](plugin-helpers.markdown)
- [Add mail transports](plugin-mail-transports.markdown)
- [Add notification types](plugin-notifications.markdown)
- [Add automatic actions](plugin-automatic-actions.markdown)
- [Attach metadata to users, tasks and projects](plugin-metadata.markdown)
- [Authentication architecture](plugin-authentication-architecture.markdown)
- [Authentication plugin registration](plugin-authentication.markdown)
- [Authorization architecture](plugin-authorization-architecture.markdown)
- [Custom group providers](plugin-group-provider.markdown)
- [External link providers](plugin-external-link.markdown)
- [External tasks](plugin-external-tasks.markdown)
- [Add avatar providers](plugin-avatar-provider.markdown)
- [LDAP client](plugin-ldap-client.markdown)

Examples of plugins
-------------------

- [SMS Two-Factor Authentication](https://github.com/kanboard/plugin-sms-2fa)
- [Reverse-Proxy Authentication with LDAP support](https://github.com/kanboard/plugin-reverse-proxy-ldap)
- [Slack](https://github.com/kanboard/plugin-slack)
- [Hipchat](https://github.com/kanboard/plugin-hipchat)
- [Jabber](https://github.com/kanboard/plugin-jabber)
- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Postmark](https://github.com/kanboard/plugin-postmark)
- [Amazon S3](https://github.com/kanboard/plugin-s3)
- [Budget planning](https://github.com/kanboard/plugin-budget)
- [User timetables](https://github.com/kanboard/plugin-timetable)
- [Subtask Forecast](https://github.com/kanboard/plugin-subtask-forecast)
- [Automatic Action example](https://github.com/kanboard/plugin-example-automatic-action)
- [Theme plugin example](https://github.com/kanboard/plugin-example-theme)
- [CSS plugin example](https://github.com/kanboard/plugin-example-css)
