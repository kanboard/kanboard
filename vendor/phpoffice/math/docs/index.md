#

Math is a library written in pure PHP that provides a set of classes to manipulate different formula file formats, i.e. [MathML](https://en.wikipedia.org/wiki/MathML) and [Office MathML (OOML)](https://en.wikipedia.org/wiki/Office_Open_XML_file_formats#Office_MathML_(OMML)).

Math is an open source project licensed under the terms of [MIT](https://github.com/PHPOffice/Math/blob/master/LICENCE). Math is aimed to be a high quality software product by incorporating [continuous integration and unit testing](https://github.com/PHPOffice/Math/actions/workflows/php.yml). You can learn more about Math by reading this Developers'Documentation and the [API Documentation](http://phpoffice.github.io/Math/docs/develop/)

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

## Support

### Readers

| Features                  |                      | MathML           | Office MathML    |
|---------------------------|----------------------|:----------------:|:----------------:|
| **Basic**                 | Identifier           | :material-check: | :material-check: |
|                           | Operator             | :material-check: | :material-check: |
|                           | Numeric              | :material-check: | :material-check: |
| **Simple**                | Fraction             | :material-check: | :material-check: |
|                           | Superscript          | :material-check: |                  |
| **Architectural**         | Row                  | :material-check: |                  |
|                           | Semantics            | :material-check: |                  |

### Writers

| Features                  |                      | MathML           | Office MathML    |
|---------------------------|----------------------|:----------------:|:----------------:|
| **Basic**                 | Identifier           | :material-check: | :material-check: |
|                           | Operator             | :material-check: | :material-check: |
|                           | Numeric              | :material-check: | :material-check: |
| **Simple**                | Fraction             | :material-check: | :material-check: |
|                           | Superscript          | :material-check: |                  |
| **Architectural**         | Row                  | :material-check: | :material-check: |
|                           | Semantics            | :material-check: |                  |

## Contributing

We welcome everyone to contribute to Math. Below are some of the things that you can do to contribute:

-  [Fork us](https://github.com/PHPOffice/Math/fork) and [request a pull](https://github.com/PHPOffice/Math/pulls) to the [master](https://github.com/PHPOffice/Math/tree/master) branch
-  Submit [bug reports or feature requests](https://github.com/PHPOffice/Math/issues) to GitHub
-  Follow [@PHPOffice](https://twitter.com/PHPOffice) on Twitter
