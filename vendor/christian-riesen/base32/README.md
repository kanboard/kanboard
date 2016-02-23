base32
======

Base32 Encoder/Decoder for PHP according to RFC 4648

[![Build Status](https://secure.travis-ci.org/ChristianRiesen/base32.png)](http://travis-ci.org/ChristianRiesen/base32)
[![HHVM Status](http://hhvm.h4cc.de/badge/christian-riesen/base32.png)](http://hhvm.h4cc.de/package/christian-riesen/base32)

[![Latest Stable Version](https://poser.pugx.org/christian-riesen/base32/v/stable.png)](https://packagist.org/packages/christian-riesen/base32) [![Total Downloads](https://poser.pugx.org/christian-riesen/base32/downloads.png)](https://packagist.org/packages/christian-riesen/base32) [![Latest Unstable Version](https://poser.pugx.org/christian-riesen/base32/v/unstable.png)](https://packagist.org/packages/christian-riesen/base32) [![License](https://poser.pugx.org/christian-riesen/base32/license.png)](https://packagist.org/packages/christian-riesen/base32)

Do you like this? Flattr it:

[![Flattr base32](http://api.flattr.com/button/flattr-badge-large.png)](http://flattr.com/thing/720563/ChristianRiesenbase32-on-GitHub)

Usage
-----

    <?php

    // Include class or user autoloader
    use Base32\Base32;

    $string = 'fooba';

    $encoded = Base32::encode($string);
    // $encoded contains now 'MZXW6YTB'

    $decoded = Base32::decode($encoded);
    // $decoded is again 'fooba'


About
=====

Use
---

Initially created to work with the [one time password project](https://github.com/ChristianRiesen/otp), yet it can stand alone just as well as [Jordi Boggiano](http://seld.be/) kindly pointed out. It's the only Base32 implementation I could make work that passes the test vectors (and contains unit tests).

Goal
----
Have a RFC compliant Base32 encoder and decoder. The implementation could be improved, but for now, it does the job and has unit tests. Ideally, the class can be enhanced while the unit tests keep passing.

Requirements
------------

PHP 5.3.x+

If you want to run the tests, PHPUnit 3.6 or up is required.

Author
------

Christian Riesen <chris.riesen@gmail.com> http://christianriesen.com

Acknowledgements
----------------

Base32 is mostly based on the work of https://github.com/NTICompass/PHP-Base32

