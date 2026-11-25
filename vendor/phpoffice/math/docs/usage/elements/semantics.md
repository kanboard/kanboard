## Usage

To create a semantics, use the `PhpOffice\Math\Element\Semantics` class.

### Methods
#### add

The method add an element to the `semantics` element.
The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

#### addAnnotation

The method add an annotation to the `semantics` element.
The method has two parameters : 

* `string` **$encoding**
* `string` **$annotation**

#### getAnnotation

The method return an annotation based on its encoding.
The method has one parameter : 

* `string` **$encoding**

#### getAnnotations

The method return alls annotation of the `semantics` element.
The method has no parameter.

#### getElements

The method return all elements of the `semantics` element.

#### remove

The method remove an element to the `semantics` element.
The method has one parameter : 

* `PhpOffice\Math\Element\AbstractElement` **$element**

## Example

### Math
<math display="block">
  <semantics>
    <mi>y</mi>
    
    <annotation encoding="application/x-tex"> y </annotation>
  </semantics>
</math>

### XML
``` xml
<math display="block">
  <semantics>
    <mi>y</mi>
    
    <annotation encoding="application/x-tex"> y </annotation>
  </semantics>
</math>
```

### PHP

``` php
<?php

use PhpOffice\Math\Element;
use PhpOffice\Math\Math;

$math = new Math();

$semantics = new Element\Semantics();
$semantics->add(new Element\Identifier('y'));
$semantics->addAnnotation('application/x-tex', ' y ');

$math->add($semantics);
```