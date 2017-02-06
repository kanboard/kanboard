API Link Procedures
===================

## getAllLinks

- Purpose: **Get the list of possible relations between tasks**
- Parameters: none
- Result on success: **List of links**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getAllLinks",
    "id": 113057196
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 113057196,
    "result": [
        {
            "id": "1",
            "label": "relates to",
            "opposite_id": "0"
        },
        {
            "id": "2",
            "label": "blocks",
            "opposite_id": "3"
        },
        {
            "id": "3",
            "label": "is blocked by",
            "opposite_id": "2"
        },
        {
            "id": "4",
            "label": "duplicates",
            "opposite_id": "5"
        },
        {
            "id": "5",
            "label": "is duplicated by",
            "opposite_id": "4"
        },
        {
            "id": "6",
            "label": "is a child of",
            "opposite_id": "7"
        },
        {
            "id": "7",
            "label": "is a parent of",
            "opposite_id": "6"
        },
        {
            "id": "8",
            "label": "targets milestone",
            "opposite_id": "9"
        },
        {
            "id": "9",
            "label": "is a milestone of",
            "opposite_id": "8"
        },
        {
            "id": "10",
            "label": "fixes",
            "opposite_id": "11"
        },
        {
            "id": "11",
            "label": "is fixed by",
            "opposite_id": "10"
        }
    ]
}
```

## getOppositeLinkId

- Purpose: **Get the opposite link id of a task link**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **link_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getOppositeLinkId",
    "id": 407062448,
    "params": [
        2
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 407062448,
    "result": "3"
}
```

## getLinkByLabel

- Purpose: **Get a link by label**
- Parameters:
    - **label** (integer, required)
- Result on success: **link properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkByLabel",
    "id": 1796123316,
    "params": [
        "blocks"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1796123316,
    "result": {
        "id": "2",
        "label": "blocks",
        "opposite_id": "3"
    }
}
```

## getLinkById

- Purpose: **Get a link by id**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **link properties**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "getLinkById",
    "id": 1190238402,
    "params": [
        4
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1190238402,
    "result": {
        "id": "4",
        "label": "duplicates",
        "opposite_id": "5"
    }
}
```

## createLink

- Purpose: **Create a new task relation**
- Parameters:
    - **label** (integer, required)
    - **opposite_label** (integer, optional)
- Result on success: **link_id**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "createLink",
    "id": 1040237496,
    "params": [
        "foo",
        "bar"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 1040237496,
    "result": 13
}
```

## updateLink

- Purpose: **Update a link**
- Parameters:
    - **link_id** (integer, required)
    - **opposite_link_id** (integer, required)
    - **label** (string, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "updateLink",
    "id": 2110446926,
    "params": [
        "14",
        "12",
        "boo"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2110446926,
    "result": true
}
```

## removeLink

- Purpose: **Remove a link**
- Parameters:
    - **link_id** (integer, required)
- Result on success: **true**
- Result on failure: **false**

Request example:

```json
{
    "jsonrpc": "2.0",
    "method": "removeLink",
    "id": 2136522739,
    "params": [
        "14"
    ]
}
```

Response example:

```json
{
    "jsonrpc": "2.0",
    "id": 2136522739,
    "result": true
}
```
