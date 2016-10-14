Configuración del proyecto
==========================

Ir al menú **Ajustes**, a continuación, seleccione **Configuración del proyecto ** a la izquierda.

! [Configuración del proyecto] (https://kanboard.net/screenshots/documentation/project-settings.png)

### columnas por defecto para nuevos proyectos

Puede cambiar los nombres de las columnas por defecto aquí.
Es útil si siempre crea proyectos con las mismas columnas.

Cada nombre de columna debe estar separado por una coma.

De forma predeterminada, Kanboard utilizar esos nombres de columna: la Pila, Listo, productos en curso y hecho.

### categorías predeterminadas para nuevos proyectos

Las categorías no son globales para la aplicación, pero unido a un proyecto.
Cada proyecto puede tener diferentes categorías.

Sin embargo, si cree siempre las mismas categorías para todos sus proyectos, se puede definir aquí la lista de categorías para crear automáticamente.

### Permitir sólo una subtarea en curso al mismo tiempo para un usuario

Cuando se activa esta opción, el usuario puede trabajar con una sola subtarea en el momento.

Si otra subtarea tiene el estado "en curso", el usuario verá este cuadro de diálogo:

! [Restricción usuario Subtarea] (https://kanboard.net/screenshots/documentation/subtask-user-restriction.png)

### Dispara automáticamente el tiempo de seguimiento subtarea

- Si está activado, cuando un estado de subtarea se cambia a "en curso", el temporizador se iniciará automáticamente.
- Desactivar esta opción si no utiliza el tiempo de seguimiento.

### Incluyen tareas cerrado en el diagrama de flujo acumulado

- Si está activado, las tareas cerradas serán incluidas en el diagrama de flujo acumulativo.
- Si está desactivada, sólo se incluirán las tareas abiertas.
- Esta opción afecta a la columna "total" de la tabla "project_daily_column_stats"
