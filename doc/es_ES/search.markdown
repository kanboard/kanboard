Sintaxis de busquedas avanzadas
======================

Kanboard utiliza un lenguaje de consulta simple para la búsqueda avanzada.
Puedes buscar en las tareas, comentarios, enlaces subtareas, sino también en el flujo de actividad.

Example of query
----------------
En este ejemplo se devolverá todas las tareas asignadas a mí con una fecha de vencimiento para mañana y un título que contiene "mi título":

```
assigne:me due:tomorrow mi titulo
```

Busqueda Global
-------------

### Busqueda por id de tarea o titulo

- Busqueda por task id: `#123`
- Busqueda task id and task title: `123`
- Busqueda by task title: algo que no coincide con ningún atributo de búsqueda

### Busqueda por estatus

Atributo: **status**

- Query para buscar tareas abiertas: `status:open`
- Query para buscar tareas cerradas: `status:closed`

### Busqueda por asingando

Atributo: **assignee**

- Query con nombre completo: `assignee:"Frederic Guillot"`
- Query con el usuario: `assignee:fguillot`
- Busqueda de asignación multiple `assignee:user1 assignee:"John Doe"`
- Query para tareas sin asingnar: `assignee:nobody`
- Query para tereas asignadas a mi : `assignee:me`

### Busqueda por creador de tarea

Atributo: **creator**

- Tareas creadas por mi: `creator:me`
- Tareas creadas por John Doe: `creator:"John Doe"`
- Tareas creadas por el usuario con el id #1: `creator:1`

### Busquedas por subtareas asignadas

Atributo: **subtask:assignee**

- Ejemplo: `subtask:assignee:"John Doe"`

### Busqueda por color

Atributo **color**

- Query para busqueda por color id: `color:blue`
- Query para busqueda por color nombre: `color:"Deep Orange"`

### Búsqueda por la fecha de vencimiento

Atributo: **due**

- Busquedas de tareas de vencimiento de hoy: `due:today`
- Busquedas de tareas de vencimiento de mañana: `due:tomorrow`
- Busquedas de tareas de vencimiento de ayer: `due:yesterday`
- Busquedas de tareas con exactitud de fecha: `due:2015-06-29`

La fecha usa solamente el formato ISO 8601 : **YYYY-MM-DD**.

Todas las codenas soportadas por la funcion`strtotime()` son soportadas, por ejemplo `next Thursday`, `-2 days`, `+2 months`, `tomorrow`, etc.

Operadores soportados con una fecha:

- Mayor que: **due:>2015-06-29**
- Menor que: **due:<2015-06-29**
- Mayor que o igual que: **due:>=2015-06-29**
- Menor que o igual que: **due:<=2015-06-29**

### Busqueda por fecha de modificación

Atributo: **modified** or **updated**

Los formatos de fecha es la misma que la fecha de vencimiento.

También hay un filtro de tareas recientemente modificadas: `modified:recently`.

Esta consulta utilizará el mismo valor que tiene el periodo del tablero configurado.

### Busqueda por fecha de creación

Atributo: **created**

Funciona en la misma forma que las consultas fecha de modificación.

### Busqueda por descripcion

Atributo: **description** or **desc**

Ejemplo: `description:"text search"`

### Search by external reference

The task reference is an external id of your task, by example a ticket number from another software.

- Find tasks with a reference: `ref:1234` or `reference:TICKET-1234`

### Busquedas por categoria

Atributo: **category**

- Busqueda de tareas por una categoria especifica : `category:"Feature Request"`
- Busqueda de todas las tareas que tienen varias categorias: `category:"Bug" category:"Improvements"`
- Busqueda de tareas que no tienen asignada una tarea: `category:none`

### Busqueda por proyecto

Atributo: **project**

- Busquedas de tareas por nombre de proyecto: `project:"My project name"`
- Busquedas de tareas por id de proyecto: `project:23`
- Busquedas de tareas por varios proyectos: `project:"My project A" project:"My project B"`

### Busqueda por columnas

Atributo : **column**

- Busqueda de tareas por nombre de columna: `column:"Work in progress"`
- Busqueda de tareas por varias columnas: `column:"Backlog" column:ready`

### Busqueda por swim-lane

Atributo: **swimlane**

- Busqueda por swim-lane: `swimlane:"Version 42"`
- Busqueda de tareas en la swim-lane por defaul: `swimlane:default`
- Busqueda de tareas en varios swim-lanes: `swimlane:"Version 1.2" swimlane:"Version 1.3"`

### Busqueda por link de tarea

Atributo: **link**

- Busqueda por nombre de link: `link:"is a milestone of"`
- Busqueda de tareas en varios enlace: `link:"is a milestone of" link:"relates to"`

### Busquedas por comentarios

Atributo: **comment**

- Buscar comentarios que contengan este titulo: `comment:"My comment message"`

### Busquedas por etiquetas

Atributo: **tag**

- Ejemplo: `tag:"My tag"`

Busqueda por trafico de actividad
---------------------------------

### busqueda de eventos por titulo de tarea

Atributo: **title** o nada (default)

- Ejemplo: `title:"My task"`
- Busqueda por id de tarea: `#123`

### Busqueda por estatus de tarea

Atributo: **status**

### Busqueda por creador de evento

Atributo: **creator**

### Busqueda por fecha de creación

Atributo: **created**

### Busqueda de eventos por proyecto

Atributo: **project**
