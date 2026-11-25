# ZipStream-PHP

[![Main Branch](https://github.com/maennchen/ZipStream-PHP/actions/workflows/branch_main.yml/badge.svg)](https://github.com/maennchen/ZipStream-PHP/actions/workflows/branch_main.yml)
[![Coverage Status](https://coveralls.io/repos/github/maennchen/ZipStream-PHP/badge.svg?branch=main)](https://coveralls.io/github/maennchen/ZipStream-PHP?branch=main)
[![Latest Stable Version](https://poser.pugx.org/maennchen/zipstream-php/v/stable)](https://packagist.org/packages/maennchen/zipstream-php)
[![Total Downloads](https://poser.pugx.org/maennchen/zipstream-php/downloads)](https://packagist.org/packages/maennchen/zipstream-php)
[![Financial Contributors on Open Collective](https://opencollective.com/zipstream/all/badge.svg?label=financial+contributors)](https://opencollective.com/zipstream) [![License](https://img.shields.io/github/license/maennchen/zipstream-php.svg)](LICENSE)

## Unstable Branch

The `main` branch is not stable. Please see the
[releases](https://github.com/maennchen/ZipStream-PHP/releases) for a stable
version.

## Overview

A fast and simple streaming zip file downloader for PHP. Using this library will save you from having to write the Zip to disk. You can directly send it to the user, which is much faster. It can work with S3 buckets or any PSR7 Stream.

Please see the [LICENSE](LICENSE) file for licensing and warranty information.

## Installation

Simply add a dependency on maennchen/zipstream-php to your project's composer.json file if you use Composer to manage the dependencies of your project. Use following command to add the package to your project's dependencies:

```bash
composer require maennchen/zipstream-php
```

## Usage

For detailed instructions, please check the
[Documentation](https://maennchen.dev/ZipStream-PHP/).

Here's a simple example:

```php
// Autoload the dependencies
require 'vendor/autoload.php';

// enable output of HTTP headers
$options = new ZipStream\Option\Archive();
$options->setSendHttpHeaders(true);

// create a new zipstream object
$zip = new ZipStream\ZipStream('example.zip', $options);

// create a file named 'hello.txt'
$zip->addFile('hello.txt', 'This is the contents of hello.txt');

// add a file named 'some_image.jpg' from a local file 'path/to/image.jpg'
$zip->addFileFromPath('some_image.jpg', 'path/to/image.jpg');

// finish the zip stream
$zip->finish();
```

## Upgrade to version 2.0.0

- Only the self opened streams will be closed (#139)
  If you were relying on ZipStream to close streams that the library didn't open,
  you'll need to close them yourself now.

## Upgrade to version 1.0.0

- All options parameters to all function have been moved from an `array` to structured option objects. See [the wiki](https://github.com/maennchen/ZipStream-PHP/wiki/Available-options) for examples.
- The whole library has been refactored. The minimal PHP requirement has been raised to PHP 7.1.

## Usage with Symfony and S3

You can find example code on [the wiki](https://github.com/maennchen/ZipStream-PHP/wiki/Symfony-example).

## Contributing

ZipStream-PHP is a collaborative project. Please take a look at the
[.github/CONTRIBUTING.md](.github/CONTRIBUTING.md) file.

## About the Authors

- Paul Duncan <pabs@pablotron.org> - https://pablotron.org/
- Jonatan Männchen <jonatan@maennchen.ch> - https://maennchen.dev
- Jesse G. Donat <donatj@gmail.com> - https://donatstudios.com
- Nicolas CARPi <nico-git@deltablot.email> - https://www.deltablot.com
- Nik Barham <nik@brokencube.co.uk> - https://www.brokencube.co.uk

## Contributors

### Code Contributors

This project exists thanks to all the people who contribute.
[[Contribute](.github/CONTRIBUTING.md)].
<a href="https://github.com/maennchen/ZipStream-PHP/graphs/contributors"><img src="https://opencollective.com/zipstream/contributors.svg?width=890&button=false" /></a>

### Financial Contributors

Become a financial contributor and help us sustain our community. [[Contribute](https://opencollective.com/zipstream/contribute)]

#### Individuals

<a href="https://opencollective.com/zipstream"><img src="https://opencollective.com/zipstream/individuals.svg?width=890"></a>

#### Organizations

Support this project with your organization. Your logo will show up here with a link to your website. [[Contribute](https://opencollective.com/zipstream/contribute)]

<a href="https://opencollective.com/zipstream/organization/0/website"><img src="https://opencollective.com/zipstream/organization/0/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/1/website"><img src="https://opencollective.com/zipstream/organization/1/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/2/website"><img src="https://opencollective.com/zipstream/organization/2/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/3/website"><img src="https://opencollective.com/zipstream/organization/3/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/4/website"><img src="https://opencollective.com/zipstream/organization/4/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/5/website"><img src="https://opencollective.com/zipstream/organization/5/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/6/website"><img src="https://opencollective.com/zipstream/organization/6/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/7/website"><img src="https://opencollective.com/zipstream/organization/7/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/8/website"><img src="https://opencollective.com/zipstream/organization/8/avatar.svg"></a>
<a href="https://opencollective.com/zipstream/organization/9/website"><img src="https://opencollective.com/zipstream/organization/9/avatar.svg"></a>
