API Procedimientos de archivo de proyecto
===========================

## createProjectFile

- Propósito: **Crea y sube un nuevo proyecto adjunto**
- Parametros:
    - **project_id** (integer, required)
    - **filename** (integer, required)
    - **blob** File content encoded in base64 (string, required)
- Resultado en caso exitoso: **file_id**
- Resultado en caso fallido: **false**
- Nota: **El tamaño maximo del archivo depende de tu configuración PHP, este metodo no debe usarse para subir archivos grandes**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "createProjectFile",
    "id": 94500810,
    "params": [
        1,
        "My file",
        "cGxhaW4gdGV4dCBmaWxl"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 94500810,
    "result": 1
}
```

## getAllProjectFiles

- Propósito: **Obtiene todos los archivos adjuntos a un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **list of files**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllProjectFiles",
    "id": 1880662820,
    "params": {
        "project_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 1880662820,
    "result": [
        {
            "id": "1",
            "name": "My file",
            "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
            "is_image": "0",
            "project_id": "1",
            "date": "1432509941",
            "user_id": "0",
            "size": "15",
            "username": null,
            "user_name": null
        }
    ]
}
```

## getProjectFile

- Propósito: **Obtiene información de archivo**
- Parametros:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Resultado en caso exitoso: **file properties**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "getProjectFile",
    "id": 318676852,
    "params": [
        "42",
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 318676852,
    "result": {
        "id": "1",
        "name": "My file",
        "path": "1\/1\/0db4d0a897a4c852f6e12f0239d4805f7b4ab596",
        "is_image": "0",
        "project_id": "1",
        "date": "1432509941",
        "user_id": "0",
        "size": "15"
    }
}
```

## downloadProjectFile

- Propósito: **Descarga el contenido del archivo de proyecto (Codificado en base64)**
- Parametros:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Resultado en caso exitoso: **base64 encoded string**
- Resultado en caso fallido: **empty string**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "downloadProjectFile",
    "id": 235943344,
    "params": [
        "1",
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 235943344,
    "result": "cGxhaW4gdGV4dCBmaWxl"
}
```

## removeProjectFile

- Propósito: **Elimina un archivo asociado a un proyecto**
- Parametros:
    - **project_id** (integer, required)
    - **file_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeProjectFile",
    "id": 447036524,
    "params": [
        "1",
        "1"
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 447036524,
    "result": true
}
```

## removeAllProjectFiles

- Propósito: **Elimina todos los archivos asociados a un proyecto**
- Parametros:
    - **project_id** (integer, required)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{
    "jsonrpc": "2.0",
    "method": "removeAllProjectFiles",
    "id": 593312993,
    "params": {
        "project_id": 1
    }
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 593312993,
    "result": true
}
```
