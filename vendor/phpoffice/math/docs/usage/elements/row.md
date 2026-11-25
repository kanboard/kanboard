## Usage

To create a row, use the `PhpOffice\Math\Element\Row` class.

### Methods
#### add

The method add an element to the row.
The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

#### getElements

The method return all elements of the row.

#### remove

The method remove an element to the row.
The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

## Example

### Math
<math display="block">
  <mrow>
    <mn>1</mn>
    <mo>+</mo>
    <mi>K</mi>
  </mrow>
</math>

### XML
``` xml
<math display="block">
  <mrow>
    <mn>1</mn>
    <mo>+</mo>
    <mi>K</mi>
  </mrow>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$row = new Element\Row();
$row->add(new Element\Numeric(1));
$row->add(new Element\Operator('+'));
$row->add(new Element\Identifier('K'));

$math->add($row);
```