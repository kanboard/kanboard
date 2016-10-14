Time Tracking
=============

Time tracking la información puede estar definida a nivel de tarea o nivel de subtareas.

Task time tracking
------------------

![Task time tracking](https://kanboard.net/screenshots/documentation/task-time-tracking.png)

Las Tareas tienen dos campos:

- Tiempo estimado
- Tiempo usado

Estos valores representan las horas de trabajo y tienen que ser ajustado manualmente.

el tiempo de seguimiento subtarea
---------------------------------

![Subtask time tracking](https://kanboard.net/screenshots/documentation/subtask-time-tracking.png)

Las subtareas también tienen los campos "tiempo pasado" y "tiempo estimado".

Cuando se cambia el valor de estos campos, **los valores de seguimiento de tiempo de trabajo se actualizan automáticamente y se convierte en la suma de todos los valores de subtareas**.

Kanboard registra el tiempo entre cada cambio de estado de la subtarea en una tabla separada.

- Cambio del estado de subtarea **TODO**  a **en progreso** registra la hora de inicio
- Cambio del estado de subtarea **en curso** a **hecho** registra el tiempo del fin, sino también actualizar el tiempo de la subtarea y la tarea

El desglose de todos los registros se puede ver en la página de vista de tareas:

![Task timesheet](https://kanboard.net/screenshots/documentation/task-timesheet.png)

Para cada subtarea, el temporizador se puede detener/iniciar en cualquier momento:

![Subtask timer](https://kanboard.net/screenshots/documentation/subtask-timer.png)

- El temporizador no depende de la situación de la subtarea
- Cada vez que se inicia el temporizador de un nuevo registro se crea en la tabla de seguimiento de tiempo
- Cada vez que se detiene el reloj de la fecha de finalización se registra en la tabla de seguimiento de tiempo
- El tiempo calculado pasado se redondea al cuarto más cercano