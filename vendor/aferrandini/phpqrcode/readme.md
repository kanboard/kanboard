# PHP QRCode Library

To install this library please follow the next steps:

## Install the library using `composer`:

Add the required module to your `composer.json` file:

    {
        "require": {
            ...
            "aferrandini/phpqrcode": "1.0.1"
            ...
        }
    }

Then run the command `composer update`.


## Usage

Sample code:

    \PHPQRCode\QRcode::png("Test", "/tmp/qrcode.png", 'L', 4, 2);

This code will generate a PNG file on '/tmp/qrcode.png' with a QRCode that contains the word 'Test'.

## Acknowledgements

This library is an import of PHP QR Code by Dominik Dzienia that you can find at http://phpqrcode.sourceforge.net

Based on C libqrencode library (ver. 3.1.1), Copyright (C) 2006-2010 by Kentaro Fukuchi
http://megaui.net/fukuchi/works/qrencode/index.en.html

QR Code is registered trademarks of DENSO WAVE INCORPORATED in JAPAN and other countries.

Reed-Solomon code encoder is written by Phil Karn, KA9Q. Copyright (C) 2002, 2003, 2004, 2006 Phil Karn, KA9Q
