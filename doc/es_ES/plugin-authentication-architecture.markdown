Arquitectura de autenticación
=============================

Kanboard provee una flexible y conectable arquitectura de autenticación.

Por default, el usuario puede autenticarse con multiple metodos:

- Autenticación por usuario y password (Base de datos local y LDAP)
- Autenticación OAuth2 
- Autenticación Reverse-Proxy
- Autenticación basada en Cookie  (Recuerdame)

Además, despues de una autenticación satisfactoria un puede hacerse post de autenticación Two-Factor .
Kanboard sopoarta nativamente el standart TOTP.

Interfaces de autenticación
----------------------------

Para tener un sistema conectable, los drivers de autenticación deben implementar un conjunto de interfaces

| Interface                                | Rol                                                          |
|------------------------------------------|------------------------------------------------------------------|
| AuthenticationProviderInterface          | Interface base para otras interfaces de autenticación            |
| PreAuthenticationProviderInterface       | The user is already authenticated al alcanzar la aplicación, Usualmente los servidores web definen algunas variables de entorno |
| PasswordAuthenticationProviderInterface  | El metodo de autenticación que usa el username y password provienen del formulario de login |
| OAuthAuthenticationProviderInterface     | Proveedores OAuth2   |
| PostAuthenticationProviderInterface      | Drivers de autenticación Two-Factor ,pide el código a confirmar      |
| SessionCheckProviderInterface            | Los proveedores que son capaces de comprobar si la sesión de usuario es válida        |

### Ejemplos de autenticación de proveedores:

- Database por default metodos a implementar `PasswordAuthenticationProviderInterface` y `SessionCheckProviderInterface`
- Reverse-Proxy metodos a implementar `PreAuthenticationProviderInterface` y `SessionCheckProviderInterface`
- Google metodos a implementar `OAuthAuthenticationProviderInterface`
- LDAP metodos a implementar `PasswordAuthenticationProviderInterface`
- RememberMe cookie metodos a implementar `PreAuthenticationProviderInterface`
- Two-Factor TOTP metodos a implementar `PostAuthenticationProviderInterface`

flujo de trabajo de autenticación ** Workflow **
------------------------------------------------

Para cada peticion HTTP:

1. Si la sesión de usuario esta abierta, ejecuta el registro de proveedores que implementa`SessionCheckProviderInterface`
2. Ejecuta todos los proveedores que implementa `PreAuthenticationProviderInterface`
3. Si el usuario final hace un submit al formulario del login, Los proveedores que implementa `PasswordAuthenticationProviderInterface` are executed
4. Si el usuario final quiere usar OAuth2, el selecciona el proveedor a ejecutar
5. Despues de una autenticación satisfactoria, el ultimo registro utilizará `PostAuthenticationProviderInterface`
6. Sincronizar la información del usuario si es necesario

Este workflow es manejado por la clase `Kanboard\Core\Security\AuthenticationManager`.

Eventos disparados:

- `AuthenticationManager::EVENT_SUCCESS`: autenticación satisfactoria
- `AuthenticationManager::EVENT_FAILURE`: autenticación fallida

Cada vez que se produce un evento de fallo , el contador de intentos fallidos se incrementa.

La cuenta de usuario se puede bloquear para el período de tiempo configurado y un captcha puede ser mostrado para evitar ataques de fuerza bruta .

Interface de usuario del proveedor
---------------------------------

Cuando la autenticación es satisfactoria, la `AuthenticationManager` pedura la información del usuario para que el driver llame al metodo `getUser()`.
Este metodo debe regresar un objeto que implementa la interface `Kanboard\Core\User\UserProviderInterface`.

Esta clase abstracta reune la información dede otro sistema.

Ejemplos :

- `DatabaseUserProvider` proporciona información para un usuario interno
- `LdapUserProvider` para un usuario LDAP
- `ReverseProxyUserProvider` para un usuario Reverse-Proxy
- `GoogleUserProvider` represtan un usuario de Google

Los métodos para la interface del proveedor de Usuario:

- `isUserCreationAllowed()`: Regresa true para permitir la creación automática de usuarios
- `getExternalIdColumn()`: Obtener Identificación del nombre de la columna externa (google_id, github_id, gitlab_id...)
- `getInternalId()`: Obtener el id interno de la base de datos
- `getExternalId()`: Obtener el id externo(Unique id)
- `getRole()`: Obtener el rol de usuario
- `getUsername()`: Obtener en nombre de usuario ** username **
- `getName()`: Obtener nombre completo del usuario
- `getEmail()`: Obtener el correo electronico del usuario
- `getExternalGroupIds()`: Obtiene los ids externos del grupo, automáticamente sincroniza la membresia del grupo y la presenta
- `getExtraAttributes()`: Obtiene los atributos extras para ser mostrados a el usuario durante la sincronización local

No es obligatorio que el metodo devuelva un valor.

Sincronización de un usuario local
----------------------------------

La información del usuario puede ser sincronizada automáticamente con la base de datos local.

- Si el metodo`getInternalId()` regresa un valor no realiza la sincronización
- Los metodos `getExternalIdColumn()` y `getExternalId()` debe regresar un valor para sincronizar el usuario
- Las propiedades que regresan un ** String ** vacios no se sincronizan
