External Link Providers
=======================

This functionality allows you to link a task to additional items stored on another system.

For example, you can link a task to:

- Traditional web page
- Attachment (PDF documents stored on the web, archive...)
- Any ticketing system (bug tracker, customer support ticket...)

Each item has a type, a URL, a dependency type and a title.

By default, Kanboard includes two kinds of providers:

- Web Link: You copy and paste a link and Kanboard will fetch the page title automatically
- Attachment: Link to anything that is not a web page

Workflow
--------

1. The end-user copy and paste the URL to the form and submit
2. If the link type is "auto", Kanboard will loop through all providers registered until there is a match
3. Then, the link provider returns a object that implements the interface `ExternalLinkInterface`
4. A form is shown to the user with all pre-filled data before to save the link

Interfaces
----------

To implement a new link provider from a plugin, you need to create 2 classes that implement those interfaces:

- `Kanboard\Core\ExternalLink\ExternalLinkProviderInterface`
- `Kanboard\Core\ExternalLink\ExternalLinkInterface`

### ExternalLinkProviderInterface

| Method                     | Usage                                                           |
|----------------------------|-----------------------------------------------------------------|
| `getName()`                | Get provider name (label)                                       |
| `getType()`                | Get link type (will be saved in the database)                   |
| `getDependencies()`        | Get a dictionary of supported dependency types by the provider  |
| `setUserTextInput($input)` | Set text entered by the user                                    |
| `match()`                  | Return true if the provider can parse correctly the user input  |
| `getLink()`                | Get the link found with the properties                          |

### ExternalLinkInterface

| Method            | Usage            |
|-------------------|------------------|
| `getTitle()`      | Get link title   |
| `getUrl()`        | Get link URL     |
| `setUrl($url)`    | Set link URL     |

Register a new link provider
----------------------------

In your `Plugin.php`, just call the method `register()` from the object `ExternalLinkManager`:

```php
<?php

namespace Kanboard\Plugin\MyExternalLink;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->externalLinkManager->register(new MyLinkProvider());
    }
}
```

Examples
--------

- Kanboard includes the default providers "WebLink" and "Attachment"
