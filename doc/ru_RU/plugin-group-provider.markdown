Custom Group Providers[¶](#custom-group-providers "Ссылка на этот заголовок")

=============================================================================



Kanboard is able to load groups from an external system. This feature is mainly used for project permissions.



Project managers can allow access to a project for a group. The end-user will use an auto-complete box and search for a group.



Each time a group query is executed, all registered group providers are executed.



Group Provider Workflow[¶](#group-provider-workflow "Ссылка на этот заголовок")

-------------------------------------------------------------------------------



1.  The end-user start to type the group name in the auto-complete field

2.  The `GroupManager`{.docutils .literal} class will execute the query across all registered group providers

3.  Results are merged and returned to the user interface

4.  After selecting a group, the information of the group are synced to the local database if necessary



Group Provider Interface[¶](#group-provider-interface "Ссылка на этот заголовок")

---------------------------------------------------------------------------------



Interface to implement: `Kanboard\Core\Group\GroupProviderInterface`{.docutils .literal}.



Classes that implements this interface abstract the group information, there are only 3 methods:



-   `getInternalId()`{.docutils .literal}: Get internal database id, return 0 otherwise

-   `getExternalId()`{.docutils .literal}: Get external unique id

-   `getName()`{.docutils .literal}: Get group name



Kanboard will use the external id to sync with the local database.



Group Backend Provider Interface[¶](#group-backend-provider-interface "Ссылка на этот заголовок")

-------------------------------------------------------------------------------------------------



Interface to implement: `Kanboard\Core\Group\GroupBackendProviderInterface`{.docutils .literal}.



This interface requires only one method: `find($input)`{.docutils .literal}. The argument `$input`{.docutils .literal} is the text entered from the user interface.



This method must return a list of `GroupProviderInterface`{.docutils .literal}, this is the result of the search.



Backend Registration from Plugins[¶](#backend-registration-from-plugins "Ссылка на этот заголовок")

---------------------------------------------------------------------------------------------------



In the method `initialize()`{.docutils .literal} of your plugin register your custom backend like that:



    $groupManager->register(new MyCustomLdapBackendGroupProvider($this->container));



Examples[¶](#examples "Ссылка на этот заголовок")

-------------------------------------------------



-   [Group providers included in Kanboard (LDAP and Database)](https://github.com/fguillot/kanboard/tree/master/app/Group)



### [Оглавление](index.markdown)



-   [Custom Group Providers](#)

    -   [Group Provider Workflow](#group-provider-workflow)

    -   [Group Provider Interface](#group-provider-interface)

    -   [Group Backend Provider Interface](#group-backend-provider-interface)

    -   [Backend Registration from Plugins](#backend-registration-from-plugins)

    -   [Examples](#examples)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

