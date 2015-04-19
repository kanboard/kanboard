Postmark
========

You can use the service [Postmark](https://postmarkapp.com/) to create tasks directly by email.

This integration works with the inbound email service of Postmark.
Kanboard use a webhook to handle incoming emails.

The [incoming email workflow is described here](http://kanboard.net/documentation/email-tasks).

Postmark configuration
----------------------

Just follow the [official documentation about inbound email processing](http://developer.postmarkapp.com/developer-process-configure.html).
Basically, you have to forward your own domain or subdomain to a specific Postmark email address.

The Kanboard webhook url is displayed in **Settings > Integrations > Postmark**

Kanboard configuration
----------------------

1. Be sure that your users have an email address in their profiles
2. Assign a project identifier to the desired projects: **Project settings > Edit**
3. Try to send an email to your project

Troubleshootings
----------------

- Test the webhook url from the Postmark console, you should have a status code `200 OK`
