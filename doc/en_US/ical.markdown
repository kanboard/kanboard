Syncing your calendars
======================

Kanboard supports iCal feeds for projects and users.
This feature allows you to import Kanboard tasks in almost any calendar program (by example Microsoft Outlook, Apple Calendar, Mozilla Thunderbird and Google Calendar).

Calendar subscriptions are **read-only** access, you cannot create tasks from external calendar software.
The Calendar feed export follows the iCal standard.

Note: Only tasks within the date range of -2 months to +6 months are exported to the iCalendar feed.

Project calendars
-----------------

- Each project has its own calendar.
- The subscription link is unique per project, the link is activated when you enable the public access of your project: **Project settings > Public access**.
- This calendar shows only tasks for the selected project.

User calendars
--------------

- Each user has its own calendar.
- The subscription link is unique per user, the link is activated when you enable the public access of your user: **User profile > Public access**.
- This calendar show tasks assigned to the user for all projects.

Adding your Kanboard calendar to Apple Calendar
-----------------------------------------------

- Open Calendar
- Select **File > New Calendar Subscription**
- Copy and paste the iCal feed URL from Kanboard

![Add iCal subscription](../screenshots/apple-calendar-add-subscription.png)

- You can choose to synchronize the calendar with iCloud to be available across all your devices
- Don't forget to select the refresh frequency

![Edit iCal subscription](../screenshots/apple-calendar-edit-subscription.png)

Adding your Kanboard calendar to Microsoft Outlook
--------------------------------------------------

![Outlook Add Internet Calendar](../screenshots/outlook-add-subscription.png)

- Open Outlook
- Select **Open Calendar > From Internet**
- Copy and paste the iCal feed URL from Kanboard

![Outlook Edit Internet Calendar](../screenshots/outlook-edit-subscription.png)

Adding your Kanboard calendar to Mozilla Thunderbird
----------------------------------------------------

- Install the Add-on **Lightning** to add calendar support to Thunderbird
- Click on **File > New Calendar**
- In the dialog box, choose **On the Network**

![Thunderbird Step 1](../screenshots/thunderbird-new-calendar-step1.png)

- Choose the format iCalendar
- Copy and paste the iCal feed URL from Kanboard

![Thunderbird Step 2](../screenshots/thunderbird-new-calendar-step2.png)

- Choose the colors and other settings and finally save

Adding your Kanboard calendar to Google Calendar
------------------------------------------------

- Click the down-arrow next to **Other calendars**.
- Select **Add by URL** from the menu.
- Copy and paste the iCal feed URL from Kanboard

![Google Calendar](../screenshots/google-calendar-add-subscription.png)

Your Kanboard calendar can also be available from your Android device if you enable the synchronization.

Note: According to the Google Support, external calendars are not refreshed very often, [read the documentation](https://support.google.com/calendar/answer/37100?hl=en&ref_topic=1672445).
