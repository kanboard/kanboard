Personalizar grupos de proveedores
==================================

Kanboard es capaz de cargar grupos de un sistema externo.
Esta caracteristica es principalmente usada para permisos de proyectos.

Los **Projects Managers** pueden permitir el acceso a un proyecto para un grupo
El usuario final **end-user** utilizara una caja de autocomplementar y busquedas para un grupo

Cada vez que se ejecuta una consulta de grupo , todos los proveedores de grupo registrados se ejecutan .

Flujo de trabajo del Grupo de Proveedores **Workflow**
-----------------------------------------------------

1. El usuario final **end-user** comienza a escribir el nombre del grupo y el campo se autocompleta
2. EL `GroupManager` la clase ejecuta la consulta para todos los proveedores de grupo registrados
3. Los resultados son fusionados y regresados a la interface de usuario
4. Antes de seleccionar un grupo, a información del grupo se sincronizan con la base de datos local si es necesario

Interface del grupo de proveedores
----------------------------------

interface a implementar: `Kanboard\Core\Group\GroupProviderInterface`.

Las clases que implementa esta interface abstracta del grupo de información, solo hay 3 metodos:

- `getInternalId()`: Obtiene el id interno de la base de datos, de otra manera regresa 0
- `getExternalId()`: Obtiene un id unico externo
- `getName()`: Obtiene el nombre de grupo

Kanboard utilizará el ID externo para sincronizar con la base de datos local.

Interface Backend del grupo de proveedores
------------------------------------------
Interface a implementar: `Kanboard\Core\Group\GroupBackendProviderInterface`.

Esta interface requiere solo un metodo: `find($input)`.
El argumento `$input` es el texto introducido desde la interfaz de usuario.

Este metodo debe regresar una lista de `GroupProviderInterface`, es es el resultado de la busqueda.

Backend de registro desde Plugins
---------------------------------

En el metodo `initialize()` de su plugins registrado se puede personalizar el backend :

```php
$groupManager->register(new MyCustomLdapBackendGroupProvider($this->container));
```

Ejemplos
--------

- [Kanboard incluye el grupo de provedores (LDAP y base de datos)](https://github.com/kanboard/kanboard/tree/master/app/Group)
