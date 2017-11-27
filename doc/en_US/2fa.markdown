Two-Factor Authentication
=========================

Each user can enable the [two-factor authentication](http://en.wikipedia.org/wiki/Two_factor_authentication).
After a successful login, a one-time code (6 characters) is asked to the user to allow access to Kanboard.

This code has to be provided by a compatible software usually installed on your smartphone.

Kanboard use the [Time-based One-time Password Algorithm](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) defined in the [RFC 6238](http://tools.ietf.org/html/rfc6238).

There are many software compatible with the standard TOTP system.
For example, you can use these applications:

- [Google Authenticator](https://github.com/google/google-authenticator/) (Android, iOS, Blackberry)
- [FreeOTP](https://freeotp.github.io/) (Android, iOS)
- [OATH Toolkit](http://www.nongnu.org/oath-toolkit/) (Command line utility on Unix/Linux)

This system can work offline and you don't necessarily need to have a mobile phone.

Setup
-----

1. Go to your user profile
2. On the left, click on **Two factor authentication** and check the box
3. A secret key is generated for you

![2FA](../screenshots/2fa.png)

- You have to save the secret key in your TOTP software. If you use a smartphone, the easiest solution is to scan the QR code with FreeOTP or Google Authenticator.
- Each time you will open a new session, a new code will be asked
- Don't forget to test your device before closing your session

A new secret key is generated each time you enable/disable this feature.
