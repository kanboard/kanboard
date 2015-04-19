Mailgun
=======

You can use the service [Mailgun](http://www.mailgun.com/) to create tasks directly by email.

This integration works with the inbound email service of Mailgun (routes).
Kanboard use a webhook to handle incoming emails.

Incoming emails workflow
------------------------

1. You send an email to a specific address, by example **something+myproject@inbound.mydomain.tld**
2. Your email is forwarded to Mailgun SMTP servers
3. Mailgun call the Kanboard webhook with the email in JSON format
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

Mailgun configuration
---------------------

Create a new route in the web interface or via the API ([official documentation](https://documentation.mailgun.com/user_manual.html#routes)), here an example:

```
match_recipient("^kanboard\+(.*)@mydomain.tld$")
forward("https://mykanboard/?controller=webhook&action=mailgun&token=mytoken")
```

The Kanboard webhook url is displayed in **Settings > Integrations > Mailgun**

Kanboard configuration
----------------------

1. Be sure that your users have an email address in their profiles
2. Assign a project identifier to the desired projects: **Project settings > Edit**
3. Try to send an email to your project

Troubleshootings
----------------

- Test if your route match in the console
- Double-check requirements mentioned above
