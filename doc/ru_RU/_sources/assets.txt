How to build assets (Javascript and CSS files)
==============================================

Stylesheet and Javascript files are merged together and minified.

-  Original CSS files are stored in the folder ``assets/css/src/*.css``
-  Original Javascript code is stored in the folder
   ``assets/js/src/*.js``
-  ``assets/*/vendor.min.*`` are external dependencies merged and
   minified
-  ``assets/*/app.min.*`` are application source code merged and
   minified

Requirements
------------

-  `NodeJS <https://nodejs.org/>`__ with ``npm``

Building Javascript and CSS files
---------------------------------

Kanboard use `Gulp <http://gulpjs.com/>`__ to build the assets and
`Bower <http://bower.io/>`__ to manage dependencies. These tools are
installed as NodeJS dependencies into the project.

Run everything
~~~~~~~~~~~~~~

.. code:: bash

    make static

Build ``vendor.min.js`` and ``vendor.min.css``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code:: bash

    gulp vendor

Build ``app.min.js``
~~~~~~~~~~~~~~~~~~~~

.. code:: bash

    gulp js

Build ``app.min.css``
~~~~~~~~~~~~~~~~~~~~~

.. code:: bash

    gulp css

Notes
-----

Building assets is not possible from the Kanboard's archive, you have to
clone the repository.
