LDAP Configuration Parameters
=============================

Here is the list of available LDAP parameters:

| Parameter                       | Default value  | Description                                    |
|---------------------------------|----------------|------------------------------------------------|
| `LDAP_AUTH`                     | false          | Habilitar la autenticación LDAP                |
| `LDAP_SERVER`                   | Empty          | Nombre del servidor LDAP                          |
| `LDAP_PORT`                     | 389            | Puerto del servidor LDAP                              |
| `LDAP_SSL_VERIFY`               | true           | Validación para certificados `ldaps://` style URL  |
| `LDAP_START_TLS`                | false          | Habilitar la inicialización LDAP TLS                          |
| `LDAP_USERNAME_CASE_SENSITIVE`  | false          | Kanboard  el nombre de usuario en minúscula LDAP para evitar usuarios duplicados (la base de datos entre mayúsculas y minúsculas) |
| `LDAP_BIND_TYPE`                | anonymous      | Enlazar tipo: "anonymous", "user" o "proxy"      |
| `LDAP_USERNAME`                 | null           | Nombre de usuario LDAP para usar en modo proxie o patron de nombre de usuario  para usarlo en modo usuario |
| `LDAP_PASSWORD`                 | null           | LDAP password para usarlo en modo proxy            |
| `LDAP_USER_BASE_DN`             | Empty          | LDAP DN para usuarios (Example: "CN=Users,DC=kanboard,DC=local")           |
| `LDAP_USER_FILTER`              | Empty          | LDAP patrón a utilizar en la búsqueda de una cuenta de usuario (Ejemplo: "(&(objectClass=user)(sAMAccountName=%s))")            |
| `LDAP_USER_ATTRIBUTE_USERNAME`  | uid            | LDAP atributo para el nombre de usuario (Ejemplo: "samaccountname")            |
| `LDAP_USER_ATTRIBUTE_FULLNAME`  | cn             | LDAP atributo para el nombre completo del usuario  (Ejemplo: "displayname")         |
| `LDAP_USER_ATTRIBUTE_EMAIL`     | mail           | LDAP atributo para el email del usuario                                   |
| `LDAP_USER_ATTRIBUTE_GROUPS`    | memberof       | LDAP atributo para buscar grupos en perfil de usuario                  |
| `LDAP_USER_ATTRIBUTE_PHOTO`     | Empty          | LDAP atributo para buscar la foto del usuario (jpegPhoto o thumbnailPhoto                      |
| `LDAP_USER_ATTRIBUTE_LANGUAGE`  | Empty          | LDAP atributo para el lenguaje del usuario (preferredlanguage), el formato de idioma es aceptado "fr-FR" |
| `LDAP_USER_CREATION`            | true           | Enable automatic LDAP user creation                                |
| `LDAP_GROUP_ADMIN_DN`           | Empty          | LDAP DN para administradores (Example: "CN=Kanboard-Admins,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_MANAGER_DN`         | Empty          | LDAP DN para managers (Example: "CN=Kanboard Managers,CN=Users,DC=kanboard,DC=local")   |
| `LDAP_GROUP_PROVIDER`           | false          | Enable LDAP proveedor de grupo de permisos de proyecto |
| `LDAP_GROUP_BASE_DN`            | Empty          | LDAP Base DN por grupos   |
| `LDAP_GROUP_FILTER`             | Empty          | LDAP group filter (Example: "(&(objectClass=group)(sAMAccountName=%s*))")   |
| `LDAP_GROUP_USER_FILTER`        | Empty          | Si se define, Kanboard buscará grupos de usuarios en LDAP_GROUP_BASE_DN con este filtro, que es útil sólo para posixGroups (Ejemplo : `(&(objectClass=posixGroup)(memberUid=%s))`) |
| `LDAP_GROUP_ATTRIBUTE_NAME`     | cn             | LDAP atributo para el nombre del grupo  |

Notas:

- LDAP los atributos deben estar en minúsculas
