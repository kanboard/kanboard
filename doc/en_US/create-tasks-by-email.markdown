Create tasks by email
=====================

You can create tasks directly by sending an email.
This feature is available by using plugins.

At the moment, Kanboard is integrated with 3 external services:

- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Postmark](https://github.com/kanboard/plugin-postmark)

These services handle incoming emails without having to configure any SMTP server.

When an email is received, Kanboard receive the message on a specific end-point.
All complicated works are already handled by those services.

Incoming emails workflow
------------------------

1. You send an email to a specific address, for example **myproject@inbound.mydomain.tld**
2. Your email is forwarded to the third-party SMTP servers
3. The SMTP provider call the Kanboard web hook with the email in JSON or multipart/form-data formats
4. Kanboard parses the received email and create the task to the right project

Notes
-----

- The Kanboard web hook is protected by a random token
- The sender email address must match a Kanboard user
- The Kanboard user must be a member of the project
- Kanboard is able to convert only simple HTML emails to Markdown
- New tasks are automatically created in the first column
- Refer to the documentation of each plugin for the configuration
