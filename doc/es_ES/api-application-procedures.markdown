API Procedimientos de Aplicación
==========================

## getVersion

- Propósito: **Obtiene la versión de la aplicación**
- Parametros: Ninguno
- Resultado: **version** (Example: 1.0.12, master)

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getVersion",
    "id": 1661138292
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "1.0.13"
}
```

## getTimezone

- Propósito: **Obtiene la zona horaria de la aplicación**
- Parametros: Ninguno
- Resultado en caso exitoso: **Timezone** (Example: UTC, Europe/Paris)
- Resultado en caso fallido: **Default timezone** (UTC)

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getTimezone",
    "id": 1661138292
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "Europe\/Paris"
}
```

## getDefaultTaskColors

- Propósito: **Obtiene todos los colores de las tareas predeterminadas**
- Parametros: Ninguno
- Resultado en caso exitoso: **Color properties**

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColors",
    "id": 2108929212
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 2108929212,
    "result": {
        "yellow": {
            "name": "Yellow",
            "background": "rgb(245, 247, 196)",
            "border": "rgb(223, 227, 45)"
        },
        "blue": {
            "name": "Blue",
            "background": "rgb(219, 235, 255)",
            "border": "rgb(168, 207, 255)"
        },
        "green": {
            "name": "Green",
            "background": "rgb(189, 244, 203)",
            "border": "rgb(74, 227, 113)"
        },
        "purple": {
            "name": "Purple",
            "background": "rgb(223, 176, 255)",
            "border": "rgb(205, 133, 254)"
        },
        "red": {
            "name": "Red",
            "background": "rgb(255, 187, 187)",
            "border": "rgb(255, 151, 151)"
        },
        "orange": {
            "name": "Orange",
            "background": "rgb(255, 215, 179)",
            "border": "rgb(255, 172, 98)"
        },
        "grey": {
            "name": "Grey",
            "background": "rgb(238, 238, 238)",
            "border": "rgb(204, 204, 204)"
        },
        "brown": {
            "name": "Brown",
            "background": "#d7ccc8",
            "border": "#4e342e"
        },
        "deep_orange": {
            "name": "Deep Orange",
            "background": "#ffab91",
            "border": "#e64a19"
        },
        "dark_grey": {
            "name": "Dark Grey",
            "background": "#cfd8dc",
            "border": "#455a64"
        },
        "pink": {
            "name": "Pink",
            "background": "#f48fb1",
            "border": "#d81b60"
        },
        "teal": {
            "name": "Teal",
            "background": "#80cbc4",
            "border": "#00695c"
        },
        "cyan": {
            "name": "Cyan",
            "background": "#b2ebf2",
            "border": "#00bcd4"
        },
        "lime": {
            "name": "Lime",
            "background": "#e6ee9c",
            "border": "#afb42b"
        },
        "light_green": {
            "name": "Light Green",
            "background": "#dcedc8",
            "border": "#689f38"
        },
        "amber": {
            "name": "Amber",
            "background": "#ffe082",
            "border": "#ffa000"
        }
    }
}
```

## getDefaultTaskColor

- Propósito: **Obtiene el color predeterminado de la tarea**
- Parametros: Ninguno
- Resultado en caso exitoso: **color_id**

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColor",
    "id": 1144775215
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 1144775215,
    "result": "yellow"
}
```

## getColorList

- Propósito: **Obtiene la lista de los colores de las tareas**
- Parametros: Ninguno
- Resultado en caso exitoso: **Dictionary of color_id => color_name**

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getColorList",
    "id": 1677051386
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 1677051386,
    "result": {
        "yellow": "Yellow",
        "blue": "Blue",
        "green": "Green",
        "purple": "Purple",
        "red": "Red",
        "orange": "Orange",
        "grey": "Grey",
        "brown": "Brown",
        "deep_orange": "Deep Orange",
        "dark_grey": "Dark Grey",
        "pink": "Pink",
        "teal": "Teal",
        "cyan": "Cyan",
        "lime": "Lime",
        "light_green": "Light Green",
        "amber": "Amber"
    }
}
```

## getApplicationRoles

- Propósito: **Obtiene los roles de la aplicación**
- Parametros: Ninguno
- Resultado: **Dictionary of role => role_name**

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getApplicationRoles",
    "id": 317154243
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 317154243,
    "result": {
        "app-admin": "Administrator",
        "app-manager": "Manager",
        "app-user": "User"
    }
}
```

## getProjectRoles

- Propósito: **Obtiene los roles del proyecto**
- Parametros: Ninguno
- Resultado: **Dictionary of role => role_name**

Ejemplo de petición

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectRoles",
    "id": 8981960
}
```

Ejemplo de respuesta

```json
{
    "jsonrpc": "2.0",
    "id": 8981960,
    "result": {
        "project-manager": "Project Manager",
        "project-member": "Project Member",
        "project-viewer": "Project Viewer"
    }
}
```
