## Usage

To create a numeric, use the `PhpOffice\Math\Element\Numeric` class.

### Methods
#### getValue

The method has no parameter.

#### setValue

The method has one parameter : 

* `float` **$value**

## Example

### Math
<math display="block">
  <mn>3</mn>
</math>

### XML
``` xml
<math display="block">
  <mn>3</mn>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$identifier = new Element\Numeric(3);

$math->add($identifier);
```