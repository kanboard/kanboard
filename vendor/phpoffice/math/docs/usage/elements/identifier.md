## Usage

To create an identifier, use the `PhpOffice\Math\Element\Identifier` class.

### Methods
#### getValue

The method has no parameter.

#### setValue

The method has one parameter : 

* `string` **$value**

## Example

### Math
<math display="block">
  <mi>a</mi>
</math>

### XML
``` xml
<math display="block">
  <mi>a</mi>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$identifier = new Element\Identifier('a');

$math->add($identifier);
```