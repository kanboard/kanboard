GitHub Authentication
=====================

Requirements
------------

- OAuth GitHub API credentials (available in your [Settings > Applications > Developer applications](https://github.com/settings/applications))

How does this work?
-------------------

The GitHub authentication in Kanboard uses the [OAuth 2.0](http://oauth.net/2/) protocol, so any user of Kanboard can be linked to a GitHub account.

When that is done, they no longer need to manually login with their Kanboard account, but can simply automatically login with their GitHub account.

How to link a GitHub account
----------------------------------

1. Login to Kanboard with the desired user
2. Go to the **Edit user** page and click on the link **Link my GitHub Account**
3. You are redirected to the GitHub **Authorize application** form, authorize Kanboard by clicking on the button **Accept**
4. Finally, you are redirected to Kanboard and now your user account is linked to your GitHub account
5. During the process, Kanboard has updated your full name and your email address based on your GitHub profile, if either of those are publically available
6. Log out of Kanboard and you should be able to login directly with GitHub by clicking on the link **Login with my GitHub Account**

Installation instructions
-------------------------

### Setting up OAuth 2.0

On Github, go to the page ["Register a new OAuth application"](https://github.com/settings/applications/new).

Just follow the [official GitHub documentation](https://developer.github.com/guides/basics-of-authentication/#registering-your-app):

- Open your [Settings](https://github.com/settings), select [Applications](https://github.com/settings/applications) from the sidebar and click on [Register new application](https://github.com/settings/applications/new) on the top, next to where it says **Developer applications**
- Fill out the form with whatever values you like, the **Authorization callback URL** _must_ be: **http://YOUR_SERVER/?controller=user&action=gitHub**

### Setting up Kanboard

Either create a new `config.php` file or rename the `config.default.php` file and set the following values:

```php
// Enable/disable GitHub authentication
define('GITHUB_AUTH', true);

// GitHub client id (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_ID', 'YOUR_GITHUB_CLIENT_ID');

// GitHub client secret key (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_SECRET', 'YOUR_GITHUB_CLIENT_SECRET');

```

Notes
-----
**Important:** _*Never*_ store your GITHUB_CLIENT_ID or GITHUB_CLIENT_SECRET in GitHub or somewhere with full public access in general!

Kanboard uses these information from your public GitHub profile:

- Full name
- Public email address
- GitHub unique id

The GitHub unique id is used to link the local user account and the GitHub account.
