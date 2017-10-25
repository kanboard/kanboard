One Time Passwords
==================

[![Build Status](https://secure.travis-ci.org/ChristianRiesen/otp.png)](http://travis-ci.org/ChristianRiesen/otp)

Did you like this? Flattr it:

[![Flattr otp](http://api.flattr.com/button/flattr-badge-large.png)](https://flattr.com/thing/719284/ChristianRiesenotp-on-GitHub)

Installation
------------

Use [composer](http://getcomposer.org/) and require the library in your `composer.json`

	{
    	"require": {
        	"christian-riesen/otp": "1.*",
    	}
	}

Usage
-----

```php
<?php

use Otp\Otp;
use Otp\GoogleAuthenticator;

// Seperate class, see https://github.com/ChristianRiesen/base32
use Base32\Base32;

// Get a Pseudo Secret
// Defaults to 16 characters
$secret = GoogleAuthenticator::generateRandom();

// Url for the QR code
// Using totp method
$url = GoogleAuthenticator::getQrCodeUrl('totp', 'Label like user@host.com', $secret);

// Save the secret with the users account
// Display QR Code to the user

// Now how to check
$otp = new Otp();

// $key is a 6 digit number, coming from the User
// Assuming this is present and sanitized
// Allows for a 1 code time drift by default
// Third parameter can alter that behavior
if ($otp->checkTotp(Base32::decode($secret), $key)) {
    // Correct key
    // IMPORTANT! Note this key as being used
    // so nobody could launch a replay attack.
    // Cache that for the next minutes and you
    // should be good.
} else {
    // Wrong key
}

// Just to create a key for display (testing)
$key = $otp->totp($secret);

```

Sample script in `example` folder. Requires sessions to work (for secret storage).

Class Otp
---------

Implements hotp according to [RFC4226](https://tools.ietf.org/html/rfc4226) and totp according to [RFC6238](https://tools.ietf.org/html/rfc6238) (only sha1 algorithm). Once you have a secret, you can use it directly in this class to create the passwords themselves (mainly for debugging use) or use the check functions to safely check the validity of the keys. The `checkTotp` function also includes a helper to battle timedrift.

Class GoogleAuthenticator
-------------------------

Static function class to generate a correct url for the QR code, so you can easy scan it with your device. Google Authenticator is avaiaible as application for iPhone and Android. This removes the burden to create such an app from the developers of websites by using this set of classes.

There are also older open source versions of the Google Authenticator app for both [iPhone](https://github.com/google/google-authenticator) and [Android](https://github.com/google/google-authenticator-android)

This helper class uses the random_int function from PHP7, or the polyfill method from [paragonie/random_compat](https://packagist.org/packages/paragonie/random_compat) if present and falls back on other (less "secure") random generators.

About
=====

Requirements
------------

PHP 5.3.x+

Uses [Base32 class](https://github.com/ChristianRiesen/base32).

If you want to run the tests, PHPUnit 3.6 or up is required.

Author
------

Christian Riesen <chris.riesen@gmail.com> http://christianriesen.com

Acknowledgements
----------------

The classes have been inspired by many different places that were talking about otp and Google Authenticator. Thank you all for your help.

Project setup ideas blantently taken from https://github.com/Seldaek/monolog

