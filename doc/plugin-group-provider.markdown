Custom Group Providers
======================

Kanboard is able to load groups from an external system.
This feature is mainly used for project permissions.

Project managers can allow access to a project for a group.
The end-user will use an auto-complete box and search for a group.

Each time a group query is executed, all registered group providers are executed.

Group Provider Workflow
-----------------------

1. The end-user start to type the group name in the auto-complete field
2. The `GroupManager` class will execute the query across all registered group providers
3. Results are merged and returned to the user interface
4. After selecting a group, the information of the group are synced to the local database if necessary

Group Provider Interface
------------------------

Interface to implement: `Kanboard\Core\Group\GroupProviderInterface`.

Classes that implements this interface abstract the group information, there are only 3 methods:

- `getInternalId()`: Get internal database id, return 0 otherwise
- `getExternalId()`: Get external unique id
- `getName()`: Get group name

Kanboard will use the external id to sync with the local database.

Group Backend Provider Interface
--------------------------------

Interface to implement: `Kanboard\Core\Group\GroupBackendProviderInterface`.

This interface requires only one method: `find($input)`.
The argument `$input` is the text entered from the user interface.

This method must return a list of `GroupProviderInterface`, this is the result of the search.

Backend Registration from Plugins
---------------------------------

In the method `initialize()` of your plugin register your custom backend like that:

```php
$groupManager->register(new MyCustomLdapBackendGroupProvider($this->container));
```

Examples
--------

- [Group providers included in Kanboard (LDAP and Database)](https://github.com/kanboard/kanboard/tree/master/app/Group)
