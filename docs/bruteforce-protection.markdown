Bruteforce Protection
=====================

The brute force protection of Kanboard works at the user account level:

- After 3 authentication failure for the same username, the login form show a captcha image to prevent automated bot tentatives.
- After 6 authentication failure, the user account is locked down for a period of 15 minutes.

This feature works only for authentication methods that use the login form.

However, **after 3 authentication failure through the user API**, the account have to be unlocked by using the login form.

Kanboard doesn't block any IP addresses since bots can use several anonymous proxies. However, you can use external tools like [fail2ban](http://www.fail2ban.org) to avoid massive scans.

Default settings can be changed with these configuration variables:

```php
// Enable captcha after 3 authentication failure
define('BRUTEFORCE_CAPTCHA', 3);

// Lock the account after 6 authentication failure
define('BRUTEFORCE_LOCKDOWN', 6);

// Lock account duration in minute
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);
```
