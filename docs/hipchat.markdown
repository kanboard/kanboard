Hipchat integration
===================

Send notifications to a room
-----------------------------

Example of notifications:

![Hipchat notification](http://kanboard.net/screenshots/documentation/hipchat-notification.png)

This feature use the room notification token system of Hipchat.

### Hipchat configuration

![Hipchat room token](http://kanboard.net/screenshots/documentation/hipchat-room-token.png)

1. Go to to **My account**
2. Click on the tab **Rooms** and select the room you want to send the notifications
3. On the left, choose **Tokens**
4. Enter a label, by example "Kanboard" and save

### Kanboard configuration

![Hipchat settings](http://kanboard.net/screenshots/documentation/hipchat-settings.png)

1. Go to **Settings > Integrations > Hipchat**
2. Replace the API url if you use the self-hosted version of Hipchat
3. Set the room name or the room API ID
4. Copy and paste the token generated previously

Now, all Kanboard events will be sent to the Hipchat room.
