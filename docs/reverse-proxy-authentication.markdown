Reverse Proxy Authentication
============================

Requirements
------------

- A well configured reverse proxy (or Apache auth on the same server), that performs authentication and sends the authenticated username to Kanboard using a HTTP header. It is useful if you have yet some SSO (Single-Sign-On) in your organization, so you should know what your are doing.

How does this work?
-------------------

- Your reverse proxy authenticates the user and adds a HTTP header containing its login to the request. The default header name or how to specify it should be in the reverse proxy documentation, however:
  - If it is the same web server that runs Kanboard, the CGI spec specifies this header to be REMOTE_USER (see [RFC 3875](http://www.ietf.org/rfc/rfc3875) §4.1.11). For example, Apache adds REMOTE_USER by default if `Require valid-user` is set. Note this header is only set for CGI (like PHP), and not if Apache is a reverse proxy to another Apache running Kanboard. It works the same with IIS and nginx according to their documentation.
  - If it is a real reverse proxy, the HTTP ICAP extension draft spec proposes the header to be X-Authenticated-User (see [IETF draft spec §3.4](http://tools.ietf.org/html/draft-stecher-icap-subid-00#section-3.4)). This de-facto standart has been adopted by a number of tools.
- Kanboard retrieves the value of the specified HTTP header, and:
  - If the user does not exist yet, creates it (it also checks if it is the default admin).
  - **Authenticates the given user without any prompt, assuming it's valid (so it does NOT prompt the login page)**.


Installation instructions
-------------------------

### Setting up your reverse proxy

This is not in the scope of this documentation. You should check that the user login is sent by the reverse proxy using a HTTP header, and find which one.

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

```
