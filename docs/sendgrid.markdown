Sendgrid
========

You can use the service [Sendgrid](https://sendgrid.com/) to create tasks directly by email.

This integration works with the [Parse API of Sendgrid](https://sendgrid.com/docs/API_Reference/Webhooks/parse.html).
Kanboard use a webhook to handle incoming emails.

The [incoming email workflow is described here](http://kanboard.net/documentation/email-tasks).

Sendgrid configuration
----------------------

1. Create a new domain or subdomain (by example **inbound.mydomain.tld**) with a MX record that point to **mx.sendgrid.net**
2. Add your domain and the Kanboard webhook url to [the configuration page in Sendgrid](https://sendgrid.com/developer/reply)

The Kanboard webhook url is displayed in **Settings > Integrations > Sendgrid**

Kanboard configuration
----------------------

1. Be sure that your users have an email address in their profiles
2. Assign a project identifier to the desired projects: **Project settings > Edit**
3. Try to send an email to your project
