API de Procedimientos de tarea de enlace externo
=================================

## getExternalTaskLinkTypes

- Propósito: **Obtener todos los proveedores registrados de enlaces externos**
- Parámetros: **ninguno**
- Resultado en caso de éxito: **dict**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkTypes","id":477370568}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "auto": "Auto",
        "attachment": "Attachment",
        "file": "Local File",
        "weblink": "Web Link"
    },
    "id": 477370568
}
```

## getExternalTaskLinkProviderDependencies

- Propósito: **Obtener las dependencias disponibles para un determinado proveedor**
- Parametros:
    - **providerName** (string, required)
- Resultado en caso de éxito: **dict**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkProviderDependencies","id":124790226,"params":["weblink"]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "related": "Related"
    },
    "id": 124790226
}
```

## createExternalTaskLink

- Propósito: **Crear una nueva tarea de enlace externo**
- Parametros:
    - **task_id** (integer, required)
    - **url** (string, required)
    - **dependency** (string, required)
    - **type** (string, optional)
    - **title** (string, optional)
- Resultado en caso de éxito: **link_id**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"createExternalTaskLink","id":924217495,"params":[9,"http:\/\/localhost\/document.pdf","related","attachment"]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": 1,
    "id": 924217495
}
```

## updateExternalTaskLink

- Propósito: **Actualizar tarea de enlace externo**
- Parametros:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
    - **title** (string, required)
    - **url** (string, required)
    - **dependency** (string, required)
- Resultado en caso de éxito: **true**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{
    "jsonrpc":"2.0",
    "method":"updateExternalTaskLink",
    "id":1123562620,
    "params": {
        "task_id":9,
        "link_id":1,
        "title":"New title"
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1123562620
}
```

## getExternalTaskLinkById

- Propósito: **Obtener un enlace de tarea externo**
- Parametros:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
- Resultado en caso de éxito: **dict**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"getExternalTaskLinkById","id":2107066744,"params":[9,1]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": {
        "id": "1",
        "link_type": "attachment",
        "dependency": "related",
        "title": "document.pdf",
        "url": "http:\/\/localhost\/document.pdf",
        "date_creation": "1466965256",
        "date_modification": "1466965256",
        "task_id": "9",
        "creator_id": "0"
    },
    "id": 2107066744
}
```

## getAllExternalTaskLinks

- Propósito: **Obtener todos los enlaces externos conectados a una tarea**
- Parametros:
    - **task_id** (integer, required)
- Resultado en caso de éxito: **list of external links**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"getAllExternalTaskLinks","id":2069307223,"params":[9]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": [
        {
            "id": "1",
            "link_type": "attachment",
            "dependency": "related",
            "title": "New title",
            "url": "http:\/\/localhost\/document.pdf",
            "date_creation": "1466965256",
            "date_modification": "1466965256",
            "task_id": "9",
            "creator_id": "0",
            "creator_name": null,
            "creator_username": null,
            "dependency_label": "Related",
            "type": "Attachment"
        }
    ],
    "id": 2069307223
}
```

## removeExternalTaskLink

- Propósito: **Remover una tarea de enlace externo**
- Parametros:
    - **task_id** (integer, required)
    - **link_id** (integer, required)
- Resultado en caso de éxito: **true**
- Resultado en caso de falla: **false**

Ejemplo de solicitud:

```json
{"jsonrpc":"2.0","method":"removeExternalTaskLink","id":552055660,"params":[9,1]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 552055660
}
```
