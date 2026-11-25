# Math

[![Latest Stable Version](https://poser.pugx.org/phpoffice/math/v/stable.png)](https://packagist.org/packages/phpoffice/math)
[![Coverage Status](https://coveralls.io/repos/github/PHPOffice/Math/badge.svg?branch=master)](https://coveralls.io/github/PHPOffice/Math?branch=master)
[![Total Downloads](https://poser.pugx.org/phpoffice/math/downloads.png)](https://packagist.org/packages/phpoffice/math)
[![License](https://poser.pugx.org/phpoffice/math/license.png)](https://packagist.org/packages/phpoffice/math)
[![CI](https://github.com/PHPOffice/Math/actions/workflows/php.yml/badge.svg)](https://github.com/PHPOffice/Math/actions/workflows/php.yml)

Math is a library written in pure PHP that provides a set of classes to manipulate different formula file formats, i.e. [MathML](https://en.wikipedia.org/wiki/MathML) and [Office MathML (OOML)](https://en.wikipedia.org/wiki/Office_Open_XML_file_formats#Office_MathML_(OMML)).

Math is an open source project licensed under the terms of [MIT](https://github.com/PHPOffice/Math/blob/master/LICENCE). Math is aimed to be a high quality software product by incorporating [continuous integration and unit testing](https://github.com/PHPOffice/Math/actions/workflows/php.yml). You can learn more about Math by reading this Developers'Documentation and the [API Documentation](http://phpoffice.github.io/Math/docs/)

If you have any questions, please ask on [StackOverFlow](https://stackoverflow.com/questions/tagged/phpoffice-math)

Read more about Math:

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Contributing](#contributing)
- [Developers' Documentation](https://phpoffice.github.io/Math/)

## Features

- Insert elements:

    * Basic :

        * Identifier : <math display="inline"><mi>a</mi></math>
        * Operator : <math display="inline"><mo>+</mo></math>
        * Numeric : <math display="inline"><mn>2</mn></math>

    * Simple :
    
        * Fraction : <math display="inline"><mfrac><mi>a</mi><mn>3</mn></mfrac></math>
        * Superscript : <math display="inline"><msup><mi>a</mi><mn>3</mn></msup></math>

    * Architectural :

        * Row
        * Semantics

- Support

    * MathML
    * OfficeMathML
## Requirements

Math requires the following:

- PHP 7.1+
- [XML Parser extension](http://www.php.net/manual/en/xml.installation.php)
- [XMLWriter extension](http://php.net/manual/en/book.xmlwriter.php)

## Installation

Math is installed via [Composer](https://getcomposer.org/).
To [add a dependency](https://getcomposer.org/doc/04-schema.md#package-links) to Math in your project, either

Run the following to use the latest stable version
```sh
composer require phpoffice/math
```
or if you want the latest unreleased version
```sh
composer require phpoffice/math:dev-master
```

## Contributing

We welcome everyone to contribute to PHPWord. Below are some of the things that you can do to contribute.

- [Fork us](https://github.com/PHPOffice/Math/fork) and [request a pull](https://github.com/PHPOffice/Math/pulls) to the [master](https://github.com/PHPOffice/Math/tree/master) branch.
- Submit [bug reports or feature requests](https://github.com/PHPOffice/Math/issues) to GitHub.
- Follow [@PHPOffice](https://twitter.com/PHPOffice) on Twitter.
