Reverse Proxy Authentication
============================

This authentication method is often used for [SSO](http://en.wikipedia.org/wiki/Single_sign-on) (Single Sign-On) especially for large organizations.

The authentication is done by another system, Kanboard doesn't know your password and suppose you are already authenticated.

Requirements
------------

- A well configured reverse proxy

or

- Apache auth on the same server


How does this work?
-------------------

1. Your reverse proxy authenticates the user and send the username through a HTTP header.
2. Kanboard retreive the username from the request
    - The user is created automatically if necessary
    - Open a new Kanboard session without any prompt assuming it's valid

Installation instructions
-------------------------

### Setting up your reverse proxy

This is not in the scope of this documentation.
You should check the user login is sent by the reverse proxy using a HTTP header, and find which one.

### Setting up Kanboard

Create a custom `config.php` file or copy the `config.default.php` file:

```php
<?php

// Enable/disable reverse proxy authentication
define('REVERSE_PROXY_AUTH', true); // Set this value to true

// The HTTP header to retrieve. If not specified, REMOTE_USER is the default
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// The default Kanboard admin for your organization.
// Since everything should be filtered by the reverse proxy,
// you should want to have a bootstrap admin user.
define('REVERSE_PROXY_DEFAULT_ADMIN', 'myadmin');

// The default domain to assume for the email address.
// In case the username is not an email address, it
// will be updated automatically as USER@mydomain.com
define('REVERSE_PROXY_DEFAULT_DOMAIN', 'mydomain.com');
```

Notes:

- If the proxy is the same web server that runs Kanboard, according the [CGI protocol](http://www.ietf.org/rfc/rfc3875) the header name will be `REMOTE_USER`. By example, Apache add `REMOTE_USER` by default if `Require valid-user` is set.

- If Apache is a reverse proxy to another Apache running Kanboard, the header `REMOTE_USER` is not set (same behaviour with IIS and Nginx).

- If you have a real reverse proxy, the [HTTP ICAP draft](http://tools.ietf.org/html/draft-stecher-icap-subid-00#section-3.4) proposes the header to be `X-Authenticated-User`. This de-facto standart has been adopted by a number of tools.
