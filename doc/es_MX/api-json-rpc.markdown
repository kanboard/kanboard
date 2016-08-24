Json-RPC API
============

Usuario y aplicacion API
------------------------

Existen dos tipos de acceso a la API:

### API de Aplicacion

- Acceso a la API con el usuario "jsonrpc" y el token disponible en ajustes
- Acceso a todos los procedimientos
- Sin comprobacion de permisos
- No existe sesion de usuario en el servidor
- Ejemplo de posibles clientes: Herramientas para migrar/importar datos, crear tareas desde otros sistemas, etc.

### API de Usuario

- Acceso a la API con las credenciales de usuario (Nombre de usuario y contraseña)
- Acceso a un conjunto restringido de procedimientos
- Los permisos del proyecto son comprobados
- Una sesion de usuario es creada en el servidor
- Ejemplo de posibles clientes: Aplicacion de escritotrio / dispositivos moviles, utilidad linea de commandos, etc.

Seguridad
---------

- Siempre usa HTTPS con un certificado valido.
- Si haces una aplicacion movil, es tu trabajo almacenar de forma segura las credenciales de usuario en el dispositivo
- Despues de tres fallas de autenticación en la API de usuario, el usuario final tiene que desbloquear su cuenta utilizando el formulario de inicio de sesión
- La autenticación de dos factores aun no está disponible a través de la API

Protocolo
---------

Kanboard utilizar el protocolo JSON-RPC para interactuar con programas externos.
JSON-RPC es un protocolo de llamada a procedimiento remoto codificado en JSON. Casi lo mismo que XML-RPC, pero con el formato JSON.

Utilizamos  [version 2 of the protocol](http://www.jsonrpc.org/specification). Usted debe llamar a la API con una petición HTTP POST.

Kanboard soporta requerimientos por lotes, por lo que pueden hacer varias llamadas a la API en una sola petición HTTP. Es particularmente útil para los clientes móviles con una mayor latencia de la red.

Uso
---

- [Authentication](api-authentication.markdown)
- [Examples](api-examples.markdown)
- [Application](api-application-procedures.markdown)
- [Projects](api-project-procedures.markdown)
- [Project Permissions](api-project-permission-procedures.markdown)
- [Boards](api-board-procedures.markdown)
- [Columns](api-column-procedures.markdown)
- [Swimlanes](api-swimlane-procedures.markdown)
- [Categories](api-category-procedures.markdown)
- [Automatic Actions](api-action-procedures.markdown)
- [Tasks](api-task-procedures.markdown)
- [Subtasks](api-subtask-procedures.markdown)
- [Subtask Time Tracking](api-subtask-time-tracking-procedures.markdown)
- [Task Files](api-task-file-procedures.markdown)
- [Project Files](api-project-file-procedures.markdown)
- [Links](api-link-procedures.markdown)
- [Internal Task Links](api-internal-task-link-procedures.markdown)
- [External Task Links](api-external-task-link-procedures.markdown)
- [Comments](api-comment-procedures.markdown)
- [Users](api-user-procedures.markdown)
- [Groups](api-group-procedures.markdown)
- [Group Members](api-group-member-procedures.markdown)
- [Me](api-me-procedures.markdown)

