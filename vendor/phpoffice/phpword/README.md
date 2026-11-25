# ![PHPWord](https://rawgit.com/PHPOffice/PHPWord/develop/docs/images/phpword.svg "PHPWord")

[![Latest Stable Version](https://poser.pugx.org/phpoffice/phpword/v)](https://packagist.org/packages/phpoffice/phpword)
[![Coverage Status](https://coveralls.io/repos/github/PHPOffice/PHPWord/badge.svg?branch=master)](https://coveralls.io/github/PHPOffice/PHPWord?branch=master)
[![Total Downloads](https://poser.pugx.org/phpoffice/phpword/downloads)](https://packagist.org/packages/phpoffice/phpword)
[![License](https://poser.pugx.org/phpoffice/phpword/license)](https://packagist.org/packages/phpoffice/phpword)

Branch Master : [![PHPWord](https://github.com/PHPOffice/PHPWord/actions/workflows/php.yml/badge.svg?branch=master)](https://github.com/PHPOffice/PHPWord/actions/workflows/php.yml)

PHPWord is a library written in pure PHP that provides a set of classes to write to and read from different document file formats. The current version of PHPWord supports Microsoft [Office Open XML](http://en.wikipedia.org/wiki/Office_Open_XML) (OOXML or OpenXML), OASIS [Open Document Format for Office Applications](http://en.wikipedia.org/wiki/OpenDocument) (OpenDocument or ODF), [Rich Text Format](http://en.wikipedia.org/wiki/Rich_Text_Format) (RTF), HTML, and PDF.

PHPWord is an open source project licensed under the terms of [LGPL version 3](COPYING.LESSER). PHPWord is aimed to be a high quality software product by incorporating [continuous integration](https://github.com/PHPOffice/PHPWord/actions) and unit testing. You can learn more about PHPWord by reading the [Developers' Documentation](https://phpoffice.github.io/PHPWord/).

If you have any questions, please ask on [StackOverFlow](https://stackoverflow.com/questions/tagged/phpword)

Read more about PHPWord:

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Getting started](#getting-started)
- [Contributing](#contributing)
- [Developers' Documentation](https://phpoffice.github.io/PHPWord/)

## Features

With PHPWord, you can create OOXML, ODF, or RTF documents dynamically using your PHP scripts. Below are some of the things that you can do with PHPWord library:

- Set document properties, e.g. title, subject, and creator.
- Create document sections with different settings, e.g. portrait/landscape, page size, and page numbering
- Create header and footer for each sections
- Set default font type, font size, and paragraph style
- Use UTF-8 and East Asia fonts/characters
- Define custom font styles (e.g. bold, italic, color) and paragraph styles (e.g. centered, multicolumns, spacing) either as named style or inline in text
- Insert paragraphs, either as a simple text or complex one (a text run) that contains other elements
- Insert titles (headers) and table of contents
- Insert text breaks and page breaks
- Insert and format images, either local, remote, or as page watermarks
- Insert binary OLE Objects such as Excel or Visio
- Insert and format table with customized properties for each rows (e.g. repeat as header row) and cells (e.g. background color, rowspan, colspan)
- Insert list items as bulleted, numbered, or multilevel
- Insert hyperlinks
- Insert footnotes and endnotes
- Insert drawing shapes (arc, curve, line, polyline, rect, oval)
- Insert charts (pie, doughnut, bar, line, area, scatter, radar)
- Insert form fields (textinput, checkbox, and dropdown)
- Create document from templates
- Use XSL 1.0 style sheets to transform headers, main document part, and footers of an OOXML template
- ... and many more features on progress

## Requirements

PHPWord requires the following:

- PHP 7.1+
- [XML Parser extension](http://www.php.net/manual/en/xml.installation.php)
- [Laminas Escaper component](https://docs.laminas.dev/laminas-escaper/intro/)
- [Zip extension](http://php.net/manual/en/book.zip.php) (optional, used to write OOXML and ODF)
- [GD extension](http://php.net/manual/en/book.image.php) (optional, used to add images)
- [XMLWriter extension](http://php.net/manual/en/book.xmlwriter.php) (optional, used to write OOXML and ODF)
- [XSL extension](http://php.net/manual/en/book.xsl.php) (optional, used to apply XSL style sheet to template )
- [dompdf library](https://github.com/dompdf/dompdf) (optional, used to write PDF)

## Installation

PHPWord is installed via [Composer](https://getcomposer.org/).
To [add a dependency](https://getcomposer.org/doc/04-schema.md#package-links) to PHPWord in your project, either

Run the following to use the latest stable version
```sh
composer require phpoffice/phpword
```
or if you want the latest unreleased version
```sh
composer require phpoffice/phpword:dev-master
```

## Getting started

The following is a basic usage example of the PHPWord library.

```php
<?php

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...
$section->addText(
    '"Learn from yesterday, live for today, hope for tomorrow. '
        . 'The important thing is not to stop questioning." '
        . '(Albert Einstein)'
);

/*
 * Note: it's possible to customize font style of the Text element you add in three ways:
 * - inline;
 * - using named font style (new font style object will be implicitly created);
 * - using explicitly created font style object.
 */

// Adding Text element with font customized inline...
$section->addText(
    '"Great achievement is usually born of great sacrifice, '
        . 'and is never the result of selfishness." '
        . '(Napoleon Hill)',
    array('name' => 'Tahoma', 'size' => 10)
);

// Adding Text element with font customized using named font style...
$fontStyleName = 'oneUserDefinedStyle';
$phpWord->addFontStyle(
    $fontStyleName,
    array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
);
$section->addText(
    '"The greatest accomplishment is not in never falling, '
        . 'but in rising again after you fall." '
        . '(Vince Lombardi)',
    $fontStyleName
);

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
$myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('helloWorld.docx');

// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
$objWriter->save('helloWorld.odt');

// Saving the document as HTML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
$objWriter->save('helloWorld.html');

/* Note: we skip RTF, because it's not XML-based and requires a different example. */
/* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */
```

More examples are provided in the [samples folder](samples/). For an easy access to those samples launch `php -S localhost:8000` in the samples directory then browse to [http://localhost:8000](http://localhost:8000) to view the samples.
You can also read the [Developers' Documentation](https://phpoffice.github.io/PHPWord/) for more detail.

## Contributing

We welcome everyone to contribute to PHPWord. Below are some of the things that you can do to contribute.

- Read [our contributing guide](CONTRIBUTING.md).
- [Fork us](https://github.com/PHPOffice/PHPWord/fork) and [request a pull](https://github.com/PHPOffice/PHPWord/pulls) to the [master](https://github.com/PHPOffice/PHPWord/tree/master) branch.
- Submit [bug reports or feature requests](https://github.com/PHPOffice/PHPWord/issues) to GitHub.
- Follow [@PHPOffice](https://twitter.com/PHPOffice) on Twitter.
