API Application Procedures
==========================

## getVersion

- Purpose: **Get the application version**
- Parameters: none
- Result: **version** (Example: 1.0.12, master)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getVersion",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "1.0.13"
}
```

## getTimezone

- Purpose: **Get the application timezone**
- Parameters: none
- Result on success: **Timezone** (Example: UTC, Europe/Paris)
- Result on failure: **Default timezone** (UTC)

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getTimezone",
    "id": 1661138292
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1661138292,
    "result": "Europe\/Paris"
}
```

## getDefaultTaskColors

- Purpose: **Get all default task colors**
- Parameters: None
- Result on success: **Color properties**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColors",
    "id": 2108929212
}
```

Response example:

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

- Purpose: **Get default task color**
- Parameters: None
- Result on success: **color_id**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getDefaultTaskColor",
    "id": 1144775215
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1144775215,
    "result": "yellow"
}
```

## getColorList

- Purpose: **Get the list of task colors**
- Parameters: none
- Result on success: **Dictionary of color_id => color_name**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getColorList",
    "id": 1677051386
}
```

Response example:

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

- Purpose: **Get the application roles**
- Parameters: none
- Result: **Dictionary of role => role_name**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getApplicationRoles",
    "id": 317154243
}
```

Response example:

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

- Purpose: **Get the project roles**
- Parameters: none
- Result: **Dictionary of role => role_name**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectRoles",
    "id": 8981960
}
```

Response example:

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