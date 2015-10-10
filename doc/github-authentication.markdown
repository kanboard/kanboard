Github Authentication
=====================

Requirements
------------

OAuth Github API credentials (available in your [Settings > Applications > Developer applications](https://github.com/settings/applications))

How does this work?
-------------------

The Github authentication in Kanboard uses the [OAuth 2.0](http://oauth.net/2/) protocol, so any user of Kanboard can be linked to a Github account.

That means you can use your Github account to login on Kanboard.

How to link a Github account
----------------------------

1. Go to your user profile
2. Click on **External accounts**
3. Click on the link **Link my Github Account**
4. You are redirected to the **Github Authorize application form**
5. Authorize Kanboard by clicking on the button **Accept**
6. Your account is now linked

Now, on the login page you can be authenticated in one click with the link **Login with my Github Account**.

Your name and email are automatically updated from your Github Account if defined.

Installation instructions
-------------------------

### Setting up OAuth 2.0

- On Github, go to the page [Register a new OAuth application](https://github.com/settings/applications/new)
- Just follow the [official Github documentation](https://developer.github.com/guides/basics-of-authentication/#registering-your-app)
- In Kanboard, you can get the **callback url** in **Settings > Integrations > Github Authentication**

### Setting up Kanboard

Either create a new `config.php` file or rename the `config.default.php` file and set the following values:

```php
// Enable/disable Github authentication
define('GITHUB_AUTH', true);

// Github client id (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_ID', 'YOUR_GITHUB_CLIENT_ID');

// Github client secret key (Copy it from your settings -> Applications -> Developer applications)
define('GITHUB_CLIENT_SECRET', 'YOUR_GITHUB_CLIENT_SECRET');
```

### Github Entreprise

To use this authentication method with Github Enterprise you have to change the default urls.

Replace these values by your self-hosted instance of Github:

```php
// Github oauth2 authorize url
define('GITHUB_OAUTH_AUTHORIZE_URL', 'https://github.com/login/oauth/authorize');

// Github oauth2 token url
define('GITHUB_OAUTH_TOKEN_URL', 'https://github.com/login/oauth/access_token');

// Github API url (don't forget the slash at the end)
define('GITHUB_API_URL', 'https://api.github.com/');
```

Notes
-----

Kanboard uses these information from your public Github profile:

- Full name
- Public email address
- Github unique id

The Github unique id is used to link the local user account and the Github account.
