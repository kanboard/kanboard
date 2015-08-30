Syncing your calendars
======================

Kanboard supports iCal feeds for projects and users.
This feature allow you to import Kanboard tasks in almost any calendar program (by example Microsoft Outlook, Apple Calendar, Mozilla Thunderbird and Google Calendar).

Calendar subscriptions are **read-only** access, you cannot create tasks from an external calendar software.
The Calendar feed export follow the iCal standard.

Note: Only tasks within the date range of -2 months to +6 months are exported to the iCalendar feed.

Project calendars
-----------------

- Each project have its own calendar.
- The subscription link is unique per project, the link is activated when you enable the public access of your project: **Project settings > Public access**.
- This calendar show only tasks for the selected project.

User calendars
--------------

- Each user have its own calendar.
- The subscription link is unique per user, the link is activated when you enable the public access of your user: **User profile > Public access**.
- This calendar show tasks assigned to the user for all projects.

Adding your Kanboard calendar to Apple Calendar
-----------------------------------------------

- Open Calendar
- Select **File > New Calendar Subscription**
- Copy and paste the iCal feed url from Kanboard

![Add iCal subscription](http://kanboard.net/screenshots/documentation/apple-calendar-add-subscription.png)

- You can choose to synchronize the calendar with iCloud to be available across all your devices
- Don't forget to select the refresh frequency

![Edit iCal subscription](http://kanboard.net/screenshots/documentation/apple-calendar-edit-subscription.png)

Adding your Kanboard calendar to Microsoft Outlook
--------------------------------------------------

![Outlook Add Internet Calendar](http://kanboard.net/screenshots/documentation/outlook-add-subscription.png)

- Open Outlook
- Select **Open Calendar > From Internet**
- Copy and paste the iCal feed url from Kanboard

![Outlook Edit Internet Calendar](http://kanboard.net/screenshots/documentation/outlook-edit-subscription.png)

Adding your Kanboard calendar to Mozilla Thunderbird
----------------------------------------------------

- Install the Add-on **Lightning** to add the calendar support to Thunderbird
- Click on **File > New Calendar**
- In the dialog box, choose **On the Network**

![Thunderbird Step 1](http://kanboard.net/screenshots/documentation/thunderbird-new-calendar-step1.png)

- Choose the format iCalendar
- Copy and paste the iCal feed url from Kanboard

![Thunderbird Step 2](http://kanboard.net/screenshots/documentation/thunderbird-new-calendar-step2.png)

- Choose the colors and other settings and finally save

Adding your Kanboard calendar to Google Calendar
------------------------------------------------

- Click the down-arrow next to **Other calendars**.
- Select **Add by URL** from the menu.
- Copy and paste the iCal feed url from Kanboard

![Google Calendar](http://kanboard.net/screenshots/documentation/google-calendar-add-subscription.png)

Your Kanboard calendar can also be available from your Android device if you enable the synchronization.

Note: According to the Google Support, external calendars are not refreshed very often, [read the documentation](https://support.google.com/calendar/answer/37100?hl=en&ref_topic=1672445).
