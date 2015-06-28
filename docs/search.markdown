Advanced Search Syntax
======================

Kanboard use a simple query language for advanced search.

Example of query
----------------

This example will returns all tasks assigned to me with a due date for tomorrow and that have a title that contains "my title":

```
assigne:me due:tomorrow my title
```

Search by assignee
------------------

Attribute: **assignee**

Query with the full name:

```
assignee:"Frederic Guillot"
```

Query with the username:

```
assignee:fguillot
```

Multiple assignee lookup:

```
assignee:user1 assignee:"John Doe"
```

Kanboard will search tasks assigned to the "user1" or "John Doe".

Query for unassigned tasks:

```
assignee:nobody
```

Query for my assigned tasks

```
assignee:me
```

Search by color
---------------

Attribute: **color**

Query to search by color id:

```
color:blue
```

Query to search by color name:

```
color:"Deep Orange"
```

Search by due date
------------------

Attribute: **due**

Query to search tasks due today:

```
due:today
```

Query to search tasks due tomorrow:

```
due:tomorrow
```

Query to search tasks due yesterday:

```
due:yesterday
```

Query to search tasks due with the exact date:

```
due:2015-06-29
```

The date must use the ISO8601 format: **YYYY-MM-DD**.

Operators supported:

- Greater than: **due:>2015-06-29**
- Lower than: **due:<2015-06-29**
- Greater than or equal: **due:>=2015-06-29**
- Lower than or equal: **due:<=2015-06-29**

