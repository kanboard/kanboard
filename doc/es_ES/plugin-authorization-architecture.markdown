Arquitecuta de autorización
===========================

Kanboard [soporta multiples roles](roles.markdown) a nivel de aplicación y a nivel de proyecto.

Workflow de autorización
--------------------------

Para cada solicitud HTTP:

1. Autorizar o no el acceso a los recursos en base a la lista de acceso a las aplicaciones
2. Si el recurso es para un projecto (board, tarea...):
    1. Extrae los roles de usuario para este proyecto
    2. Permitir/Denegar accesos basados en el mapa de acceso del proyecto

Extendiendo mapa de accesos
---------------------------

Lista de accesos (ACL) se basa en el nombre de clase del controlador y el nombre del método
La lista de acceso está a cargo de la clase `Kanboard\Core\Security\AccessMap`.

Hay dos mapa de acceso: una para la aplicación y la otra para un proyecto.

- Acceso al mapa de aplicación : `$this->applicationAccessMap`
- Acceso al mapa del proyecto: `$this->projectAccessMap`

Ejemplos para definir una nueva política para tu plugin:

```php
// Todos los metodos de la clase  MyController:
$this->projectAccessMap->add('MyController', '*', Role::PROJECT_MANAGER);

// Todos los metodos:
$this->projectAccessMap->add('MyOtherController', array('create', 'save'), Role::PROJECT_MEMBER);
```

Los roles estan defidos en la clase  `Kanboard\Core\Security\Role`.

Clase de autorización (`Kanboard\Core\Security\Authorization`) comprobará el acceso de cada página.
