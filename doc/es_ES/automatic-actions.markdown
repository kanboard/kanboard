Acciones automaticas
=================

Para minimizar la interacción de usuario, Kanboard soporta acciones automatizadas.

Cada acción automatica es definida como:

- Un evento para escuchar
- Accion enlazada a este evento
- Eventualmente hay algunos parametros para definir

Cada proyecto tiene un conjunto diferente de acciones automáticas, el panel de configuración esta localizado en la página de listado de proyectos, basta con hacer clic en el enlace **Automatic actions**.

Agregar una nueva acción
----------------

Hacer clic en el enlace **Add a new automatic action**.

![Automatique action](screenshots/automatic-action-creation.png)

- Seleccionar una acción
- Entonces, seleccionar un evento
- Y finalmente, definir los parametros

Lista de acciones disponibles
-------------------------

- Crear un comentario desde un proveedor externo
- Agregar un comentario de registro al mover la tarea entre las columnas
- Asignar automáticamente una categoría basada en un color
- Cambiar la categoría en función de una etiqueta externa
- Asignar automáticamente una categoría basada en un enlace
- Asignar automáticamente un color basado en una categoría
- Asignar un color cuando la tarea es movida a una columna específica
- Cambiar el color de tarea cuando se usa un enlace tarea específico
- Asignar un color a un usuario especifico
- Asignar la tarea a la persona que hace la acción
- Asignar la tarea a la persona que hace la acción cuando la columna es cambiada
- Asignar la tarea a un usuario específico
- Cambia la asignación basada en un usuario externo
- Cerrar la tarea
- Cerrar una tarea en una columna especifica
- Crear una tarea desde un proveedor externo
- Duplicar la tarea a otro proyecto
- Enviar una tarea por correo electrónico a alguien
- Mover la tarea a otro proyecto
- Mover la tarea a otra columna cuando se asigna a un usuario
- Mover la tarea a otra columna cuando la categoría es cambiada
- Mover la tarea a otra columna cuando la asignación es limpiada
- Abrir una tarea
- Actualizar automáticamente la fecha de inicio

Ejemplos
--------

Aqui hay algunos ejemplos utilizados en la vida real:

### Cuando muevo una tarea a la columna "Done", cerrará automáticamente esta tarea

- Elegir acción: **Close a task in a specific column**
- Elegir el evento: **Move a task to another column**
- Definir parametros de acción: **Column = Done** (Esta es la columna destino)

### Cuando muevo una tarea a la columna "To be validated", asignar esta tarea a un usuario specifico

- Choose the action: **Assign the task to a specific user**
- Elegir el evento: **Move a task to another column**
- Definir parametros de acción: **Column = To be validated** and **User = Bob** (Bob es nuestro tester)

### Cuando muevo una tarea a la columna "Work in progress", asignar esta tarea al usuario actual

- Elegir acción: **Assign the task to the person who does the action when the column is changed**
- Elegir el evento: **Move a task to another column**
- Definir parametros de acción: **Column = Work in progress**

### Cuando una tarea es completada, duplicar esta tarea a otros proyectos

Digamos que tenemos dos proyectos "Customer orders" y "Production", una vez que la orden es validada, cambiar al proyecto "Production".

- Elegir acción: **Duplicate the task to another project**
- Elegir el evento: **Closing a task**
- Definir parametros de accións: **Column = Validated** and **Project = Production**

### Cuando una tarea es movida a la ultima columna, mover exactamente la misma tarea a otro proyecto

Digamos que tenemos dos proyectos "Ideas" y "Development", una idea es validada, canbiarla al proyecto "Development".

- Elegir acción: **Move the task to another project**
- Elegir el evento: **Move a task to another column**
- Definir parametros de accións: **Column = Validated** and **Project = Development**

### Quiero asignar automáticamente un color para el usuario Bob

- Elegir acción: **Assign a color to a specific user**
- Elegir el evento: **Task assignee change**
- Definir parametros de accións: **Color = Green** and **Assignee = Bob**

### Quiero asignar a color automáticamente a la categoría definida "Feature Request"

- Elegir acción: **Assign automatically a color based on a category**
- Elegir el evento: **Task creation or modification**
- Definir parametros de accións: **Color = Blue** and **Category = Feature Request**

### Quiero fijar la fecha de inicio de forma automática cuando la tarea es movida a la columna "Work in progress"

- Elegir acción: **Automatically update the start date**
- Elegir el evento: **Move a task to another column**
- Definir parametros de accións: **Column = Work in progress**
