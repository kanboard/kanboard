Subtask Time Tracking API procedures
====================================

## hasSubtaskTimer

- Purpose: **Check if a timer is started for the given subtask and user**
- Parameters:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"hasSubtaskTimer","id":1786995697,"params":[2,4]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1786995697
}
```

## setSubtaskStartTime

- Purpose: **Start subtask timer for a user**
- Parameters:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"setSubtaskStartTime","id":1168991769,"params":[2,4]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1168991769
}
```

## setSubtaskEndTime

- Purpose: **Stop subtask timer for a user**
- Parameters:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"setSubtaskEndTime","id":1026607603,"params":[2,4]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": true,
    "id": 1026607603
}
```

## getSubtaskTimeSpent

- Purpose: **Get time spent on a subtask for a user**
- Parameters:
    - **subtask_id** (integer, required)
    - **user_id** (integer, optional)
- Result on success: **number of hours**
- Result on failure: **false**

Request example:

```json
{"jsonrpc":"2.0","method":"getSubtaskTimeSpent","id":738527378,"params":[2,4]}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "result": 1.5,
    "id": 738527378
}
```
