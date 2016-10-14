API Procedimientos de seguimiento de tiempo de subtarea
====================================

## hasSubtaskTimer

- Propósito: **Revisa si un temporizador esta iniciado para determinada subtarea y usuario**
- Parametros:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{"jsonrpc":"2.0","method":"hasSubtaskTimer","id":1786995697,"params":[2,4]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1786995697
}
```

## setSubtaskStartTime

- Propósito: **Inicia temporizador de subtarea para un usuario**
- Parametros:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{"jsonrpc":"2.0","method":"setSubtaskStartTime","id":1168991769,"params":[2,4]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1168991769
}
```

## setSubtaskEndTime

- Propósito: **Detiene el temporizador de subtarea para un usuario**
- Parametros:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Resultado en caso exitoso: **true**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{"jsonrpc":"2.0","method":"setSubtaskEndTime","id":1026607603,"params":[2,4]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1026607603
}
```

## getSubtaskTimeSpent

- Propósito: **Obtiene el tiempo dedicado en una subtarea para un usuario**
- Parametros:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Resultado en caso exitoso: **number of hours**
- Resultado en caso fallido: **false**

Ejemplo de petición:

```json
{"jsonrpc":"2.0","method":"getSubtaskTimeSpent","id":738527378,"params":[2,4]}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "result": 1.5,
    "id": 738527378
}
```
