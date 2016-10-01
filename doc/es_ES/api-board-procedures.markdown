API Procedimientos Del Tablero
===============================

## getBoard [obtener tablero]

- Propósito: **Obtener todo la información necesaria para visualizar el tablero**
- Parametros:
    - **project_id** (integer, required)
- Resultado satisfactorio : **Propiedades del tablero**
- Resultado fallido: **Lista vacía**

Ejemplo de solicitud:

```json
{
    "jsonrpc": "2.0",
    "method": "getBoard",
    "id": 827046470,
    "params": [
        1
    ]
}
```

Ejemplo de respuesta:

```json
{
    "jsonrpc": "2.0",
    "id": 827046470,
    "result": [
        {
            "id": 0,
            "name": "Default swimlane",
            "columns": [
                {
                    "id": "1",
                    "title": "Backlog",
                    "position": "1",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                },
                {
                    "id": "2",
                    "title": "Ready",
                    "position": "2",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"0",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"2",
                            "reference":"",
                            "title":"Test",
                            "description":"",
                            "date_creation":"1430870507",
                            "date_modification":"1430870507",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"yellow",
                            "project_id":"1",
                            "column_id":"2",
                            "swimlane_id":"0",
                            "owner_id":"0",
                            "creator_id":"1",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430870507",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":null,
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "3",
                    "title": "Trabajo en progreso",
                    "position": "3",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [
                        {
                            "nb_comments":"0",
                            "nb_files":"0",
                            "nb_subtasks":"1",
                            "nb_completed_subtasks":"0",
                            "nb_links":"0",
                            "id":"1",
                            "reference":"",
                            "title":"Task with comment",
                            "description":"",
                            "date_creation":"1430783188",
                            "date_modification":"1430783188",
                            "date_completed":null,
                            "date_due":"0",
                            "color_id":"red",
                            "project_id":"1",
                            "column_id":"3",
                            "swimlane_id":"0",
                            "owner_id":"1",
                            "creator_id":"0",
                            "position":"1",
                            "is_active":"1",
                            "score":"0",
                            "category_id":"0",
                            "date_moved":"1430783191",
                            "recurrence_status":"0",
                            "recurrence_trigger":"0",
                            "recurrence_factor":"0",
                            "recurrence_timeframe":"0",
                            "recurrence_basedate":"0",
                            "recurrence_parent":null,
                            "recurrence_child":null,
                            "assignee_username":"admin",
                            "assignee_name":null
                        }
                    ],
                    "nb_tasks": 1,
                    "score": 0
                },
                {
                    "id": "4",
                    "title": "Done",
                    "position": "4",
                    "project_id": "1",
                    "task_limit": "0",
                    "description": "",
                    "tasks": [],
                    "nb_tasks": 0,
                    "score": 0
                }
            ],
            "nb_columns": 4,
            "nb_tasks": 2
        }
    ]
}
```
