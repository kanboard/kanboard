Authorization Architecture[¶](#authorization-architecture "Ссылка на этот заголовок")

=====================================================================================



Kanboard [supports multiple roles](roles.markdown) at the application level and at the project level.



Authorization Workflow[¶](#authorization-workflow "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



For each HTTP request:



1.  Authorize or not access to the resource based on the application access list

2.  If the resource is for a project (board, task...):

    1.  Fetch user role for this project

    2.  Grant/Denied access based on the project access map



Extending Access Map[¶](#extending-access-map "Ссылка на этот заголовок")

-------------------------------------------------------------------------



The Access List (ACL) is based on the controller class name and the method name. The list of access is handled by the class `Kanboard\Core\Security\AccessMap`{.docutils .literal}.



There are two access map: one for the application and another one for projects.



-   Application access map: `$this->applicationAccessMap`{.docutils .literal}

-   Project access map: `$this->projectAccessMap`{.docutils .literal}



Examples to define a new policy from your plugin:



    // All methods of the class MyController:

    $this->projectAccessMap->add('MyController', '*', Role::PROJECT_MANAGER);



    // All some methods:

    $this->projectAccessMap->add('MyOtherController', array('create', 'save'), Role::PROJECT_MEMBER);



Roles are defined in the class `Kanboard\Core\Security\Role`{.docutils .literal}.



The Authorization class (`Kanboard\Core\Security\Authorization`{.docutils .literal}) will check the access for each page.



### [Оглавление](index.markdown)



-   [Authorization Architecture](#)

    -   [Authorization Workflow](#authorization-workflow)

    -   [Extending Access Map](#extending-access-map)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-authorization-architecture.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-authorization-architecture.txt)

