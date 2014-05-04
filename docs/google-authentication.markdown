Google Authentication
=====================

Requirements
------------

- OAuth Google API credentials (available in the Google Developer Console)

How does this work?
-------------------

- The Google authentication in Kanboard use the OAuth 2.0 protocol
- Any user account in Kanboard can be linked to a Google Account
- When a Kanboard user account is linked to Google, you can login with one click

Procedure to link a Google Account
----------------------------------

1. The first step is to link an existing user account to a Google Account
2. Login with the desired user
3. Go to the **Edit user** page and click on the link **Link my Google Account**
4. You are redirected to the **Google Consent screen**, authorize Kanboard by clicking on the button **Accept**
5. Finally, you are redirected to Kanboard and now your user account is linked to your Google account
6. During the process, Kanboard have updated your full name and your email address based on your Google profile
7. Log out and you should be able to login directly with Google by clicking on the link **Login with my Google Account**

Installation instructions
-------------------------

### Setting up OAuth 2.0

Follow the [official Google documentation](https://developers.google.com/accounts/docs/OAuth2Login#appsetup), in summary:

- Go to the [Developer Console](https://console.developers.google.com)
- On the sidebar, click on **Credentials** and choose **Create a new Client Id**
- Fill the form, the redirect URL must be: **http://YOUR_SERVER/?controller=user&action=google**

### Setting up Kanboad

Create a custom `config.php` file or copy the `config.default.php` file:

```php
<?php

// Enable/disable Google authentication
define('GOOGLE_AUTH', true); // Set this value to true

// Google client id (Get this value from the Google developer console)
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID');

// Google client secret key (Get this value from the Google developer console)
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');

```

Notes
-----

Kanboard use these information from your Google profile:

- Full name
- Email address
- Google unique id

The Google unique id is used to link together the local user account and the Google account.
