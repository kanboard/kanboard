Syntax Guide
============

Kanboard use the [Markdown syntax](http://en.wikipedia.org/wiki/Markdown) for comments or task descriptions.
Here are some examples:

Bold and italic
----------------

- Bold text: Use 2 asterisks or 2 underscores
- Italic text: Use 1 asterisk or 1 underscore

### Source
```
This **word** is very __important__.

And here, an *italic* word with one _underscore_.
```

### Result

This **word** is very __important__.

And here, an *italic* word with one _underscore_.

Unordered Lists
---------------

Unordered list can use asterisks, minuses or pluses.

### Source

```
- Item 1
- Item 2
- Item 3

or

* Item 1
* Item 2
* Item 3
```

### Result

- Item 1
- Item 2
- Item 3

Ordered lists
-------------

Ordered lists are prefixed by a number like that:

### Source

```
1. Do that first
2. Do this
3. And that
```

### Result

1. Do that first
2. Do this
3. And that

Links
-----

### Source

```
[My link title](http://kanboard.net/)

<http://kanboard.net>

```

### Result

[My link title](http://kanboard.net/)

<http://kanboard.net>

Source code
-----------

### Inline code

Use a backtick.

```
Execute this command: `tail -f /var/log/messages`.
```

### Result

Execute this command: `tail -f /var/log/messages`.

### Code blocks

Use 3 backticks with eventually the language name.

<pre>
<code class="language-markdown">```php
&lt;?php

phpinfo();

?&gt;
```
</code>
</pre>

### Result

```
<?php

phpinfo();

?>
```

Titles
------

### Source

```
# Title level 1

## Title level 2

### Title level 3
```
