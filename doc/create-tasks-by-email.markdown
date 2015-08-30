Create tasks by email
=====================

You can create tasks directly by sending an email.

At the moment, Kanboard is integrated with 3 external services:

- [Mailgun](http://kanboard.net/documentation/mailgun)
- [Sendgrid](http://kanboard.net/documentation/sendgrid)
- [Postmark](http://kanboard.net/documentation/postmark)

These services handle incoming emails without having to configure any SMTP server.

When an email is received, Kanboard receive the message on a specific end-point.
All complicated works are already handled by those services.

Incoming emails workflow
------------------------

1. You send an email to a specific address, by example **something+myproject@inbound.mydomain.tld**
2. Your email is forwarded to the third-party SMTP servers
3. The SMTP provider call the Kanboard webhook with the email in JSON or multipart/form-data formats
4. Kanboard parse the received email and create the task to the right project

Note: New tasks are automatically created in the first column.

Email format
------------

- The local part of the email address must use the plus separator, by example **kanboard+project123**
- The string defined after the plus sign must match a project identifier, by example **project123** is the identifier of the project **Project 123**
- The email subject becomes the task title
- The email body becomes the task description (Markdown format)

Incoming emails can be written in text or HTML formats.
**Kanboard is able to convert simple HTML emails to Markdown**.

Security and requirements
-------------------------

- The Kanboard webhook is protected by a random token
- The sender email address must match a Kanboard user
- The Kanboard project must have a unique identifier, by example **MYPROJECT**
- The Kanboard user must be member of the project
