Mailgun
=======

You can use the service [Mailgun](http://www.mailgun.com/) to create tasks directly by email.

This integration works with the inbound email service of Mailgun (routes).
Kanboard use a webhook to handle incoming emails.

The [incoming email workflow is described here](http://kanboard.net/documentation/email-tasks).

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
