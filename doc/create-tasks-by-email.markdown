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

1. You send an email to a specific address, for example **something+myproject@inbound.mydomain.tld**
2. Your email is forwarded to the third-party SMTP servers
3. The SMTP provider call the Kanboard web hook with the email in JSON or multipart/form-data formats
4. Kanboard parses the received email and create the task to the right project

Note: New tasks are automatically created in the first column.

Email format
------------

- The local part of the email address must use the plus separator, for example **kanboard+project123**
- The string defined after the plus sign must match a project identifier, for example **project123** is the identifier of the project **Project 123**
- The email subject becomes the task title
- The email body becomes the task description (Markdown format)

Incoming emails can be written in text or HTML formats.
**Kanboard is able to convert simple HTML emails to Markdown**.

Security and requirements
-------------------------

- The Kanboard web hook is protected by a random token
- The sender email address must match a Kanboard user
- The Kanboard project must have a unique identifier, for example **MYPROJECT**
- The Kanboard user must be a member of the project
