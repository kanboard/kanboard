Authorization Architecture
==========================

Kanboard [supports multiple roles](roles.markdown) at the application level and at the project level.

Authorization Workflow
----------------------

For each HTTP request:

1. Authorize or not access to the resource based on the application access list
2. If the resource is for a project (board, task...):
    1. Fetch user role for this project
    2. Grant/Denied access based on the project access map

Extending Access Map
--------------------

The Access List (ACL) is based on the controller class name and the method name.
The list of access is handled by the class `Kanboard\Core\Security\AccessMap`.

There are two access map: one for the application and another one for projects.

- Application access map: `$this->applicationAccessMap`
- Project access map: `$this->projectAccessMap`

Examples to define a new policy from your plugin:

```php
// All methods of the class MyController:
$this->projectAccessMap->add('MyController', '*', Role::PROJECT_MANAGER);

// All some methods:
$this->projectAccessMap->add('MyOtherController', array('create', 'save'), Role::PROJECT_MEMBER);
```

Roles are defined in the class `Kanboard\Core\Security\Role`.

The Authorization class (`Kanboard\Core\Security\Authorization`) will check the access for each page.
