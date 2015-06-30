Slack integration
=================

You can send notifications to Slack for all projects or only for specific projects.

- To send notifications for all projects, go to **Settings > Integrations > Slack**
- To send notifications for only some projects, go to **Project settings > Integrations > Slack**

Each project can send notifications to a separate channel.

Send notifications to a channel
-------------------------------

Example of notifications:

![Slack notification](http://kanboard.net/screenshots/documentation/slack-notification.png)

This feature use the [Incoming webhook](https://api.slack.com/incoming-webhooks) system of Slack.

### Slack configuration

![Slack webhook creation](http://kanboard.net/screenshots/documentation/slack-add-incoming-webhook.png)

1. Click on the Team dropdown and choose **Configure Integrations**
2. On the list of services, scroll-down and choose **DIY Integrations & Customizations > Incoming WebHooks**
3. Copy the webhook url to the Kanboard settings page: **Settings > Integrations > Slack** or **Project settings > Integrations > Slack**

Now, Kanboard events will be sent to the Slack channel.
