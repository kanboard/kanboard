## Usage

To create an operator, use the `PhpOffice\Math\Element\Operator` class.

### Methods
#### getValue

The method has no parameter.

#### setValue

The method has one parameter : 

* `string` **$value**

## Example

### Math
<math display="block">
  <mo>+</mo>
</math>

### XML
``` xml
<math display="block">
  <mo>+</mo>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$identifier = new Element\Operator('+');

$math->add($identifier);
```