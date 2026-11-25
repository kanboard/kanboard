## Usage

To attach a superscript to an expression, use the `PhpOffice\Math\Element\Superscript` class.

### Methods
#### getBase

The method has no parameter.

#### getSuperscript

The method has no parameter.

#### setBase

The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

#### setSuperscript

The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

## Example

### Math
<math display="block">
  <msup>
    <mi>X</mi>
    <mn>2</mn>
  </msup>
</math>

### XML
``` xml
<math display="block">
  <msup>
    <mi>X</mi>
    <mn>2</mn>
  </msup>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$superscript = new Element\Superscript();
$superscript->setBase(new Element\Identifier('X'));
$superscript->setSuperscript(new Element\Numeric(2));

$math->add($superscript);
```