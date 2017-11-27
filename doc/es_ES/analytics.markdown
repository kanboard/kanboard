Analisis
=========

Cada proyecto tiene una sección de análisis . Dependiendo de cómo se está utilizando Kanboard, se puede ver estos informes :

Repartición de usuarios
-----------------------

![User repartition](../screenshots/user-repartition.png)

Esta gráfico de sectores muestra el numero de tareas abiertas asignadas por usuario.

Distribución de tareas
----------------------

![Task distribution](../screenshots/task-distribution.png)

Este gráfico de sectores da una visión general del numero de tareas abiertas por columnas.

Diagrama de flujo acumulado
---------------------------

![Cumulative flow diagram](../screenshots/cfd.png)

- Este gráfico muesta el numerto de tareas acumuladas por cada columna a través del tiempo.
- Cada día , el número total de tareas se registra para cada columna.
- Si tu quiseras excluir las tareas cerradas, deberá cambiar las configuraciones globales del proyecto

Nota: Necesitas tener al menos dos dias de datos para mirar la gráfica 


Gráfico Burn down

El gráfico burn down esta dispobible para cada proyecto.

    Esta gráfico es un represtación grafica del trabajo This chart is a graphical representation del trabajo laborado contra el tiempo.
    Kanboard usa la complejidad o historia de puntos para generar este diagrama.
    Todos los días, se calcula la suma de los puntos de la historia de cada columna .

Average time spent into each column
    This chart shows the average time spent into each column for the last 1000 tasks.

    Kanboard uses the task transitions to calculate the data.
    The time spent is calculated until the task is closed.


Promedio de avances y ciclos de tiempos


Este gráfico muestra el promedio de avances y ciclo de tiempos para las ultimas 1000 tareas fuera de tiempo. 

    El tiempo promedio es el tiempo entre la creación de la tarea y la fecha de finalización
    El tiempo de ciclo se encuentra entre la fecha de inicio de la tarea especificada y fecha de la tarea finalizada 
    Si la tarea no es cerrada, 
    If the task is not closed, el momento actual se utiliza en lugar de la fecha de finalización.

Esos indicadores se calculan y registran todos los días durante todo el proyecto.

Nota: No olvidar ejecutar todos los dias el cronjob para tener estadísticas precisas.
