
## Writers
### MathML
The name of the writer is `MathML`.

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;
use PhpOffice\Math\Writer\MathML;

$math = new Math();
$math->add(new Element\Operator('+'));

$writer = new MathML();
$output = $writer->write($math);
```

### OfficeMathML

The name of the writer is `OfficeMathML`.

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;
use PhpOffice\Math\Writer\OfficeMathML;

$math = new Math();
$math->add(new Element\Operator('+'));

$writer = new OfficeMathML();
$output = $writer->write($math);
```

## Methods

### writer

The method has one parameter :

* `PhpOffice\Math\Math` **$math**

The method returns a `string`.