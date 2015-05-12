Jabber/XMPP integration
=======================

You can send notifications to a Jabber room for all projects or only for specific projects.

- To send notifications for all projects, go to **Settings > Integrations > Jabber**
- To send notifications for only some projects, go to **Project settings > Integrations > Jabber**

Each project can send notifications to a separate room.

## Example of notification

Here an example with the Jabber client Adium:

![Jabber notification](http://kanboard.net/screenshots/documentation/jabber-notification.png)

## Configuration

![Jabber settings](http://kanboard.net/screenshots/documentation/jabber-settings.png)

1. Go to **Settings > Integrations > Jabber** or **Project settings > Integrations > Jabber**
2. **XMPP server address**: URL of the XMPP server, example: **tcp://172.28.128.3:5222**
3. **Jabber domain**: The **"to"** attribute of the XMPP protocol, example: **example.com**
4. **Username**: The Jabber username used by Kanboard, example: **kanboard**
5. **Password**: The Jabber password
6. **Jabber nickname**: The nickname used to connect to the room
7. **Multi-user chat room**: The address of the room, example: **demo@conference.example.com**

Now, Kanboard events will be sent to the Jabber conference room.

## Troubleshooting

- All connection errors with the XMPP server are recorded in the log files `data/debug.log` or syslog
