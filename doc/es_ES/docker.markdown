Como ejecutar Kanboard con Docker?
================================

Kanboard puede funcionar facilmente con [Docker] (https://www.docker.com).

El tamaño de la imagen es aproximadamente de **70MB** y contiene:

- [Alpine Linux](http://alpinelinux.org/)
- The [process manager S6](http://skarnet.org/software/s6/)
- Nginx
- PHP 7

La tarea programada de Kanboard tambien esta todos los dias a la medianoche.
La reescritura de la URL esta activada e incluida en el archivo de configuracion.

Cuando el contenedor esta funcionando, la memoria ocupa alrededor de **30MB**.


Utilice la versión estable
----------------------

Para buscar la ultima versión estable de Kanboard use la etiqueta **estable**:

```bash---terminal
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:stable
```

Utilice la versión de desarollo (build automatico)
---------------------------------------------

Cada commit nuevo en el repositorio crea una nueva construccion en la [Docker Hub](https://registry.hub.docker.com/u/kanboard/kanboard/).


```bash---terminal
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest
```

La etiqueta **ultimo** es la **version de desarrollo** de Kanboard, uselo bajo su propio riesgo.

Construya su propia imagen  acoplable
---------------------------

Hay un `Dockerfile` en el repositorio de Kanboard para construir su imagen acoplable.
Clone el repositorio de Kanboard y ejecute el siguiente camando.

```bash---terminal
docker build -t youruser/kanboard:master .
```

o

```bash---terminal
make docker-image
```

Para ejecutar su contenedor en el fondo del puerto 80:

```bash--terminal
docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master
```

Volúmenes
-------

Usted puede adjuntar dos volúmenes de su contenedor:

- Carpeta de datos: `/var/www/app/data`
- Complementos de carpeta: `/var/www/app/plugins`


Use el indicador `-v` par montar un volumen en el ordenador central como se describe en [official Docker documentation](https://docs.docker.com/engine/userguide/containers/dockervolumes/).


Actualizar contenedor
----------------------

-Ponga la nueva imagen
-Elimine el nuevo contenedor
-Reinicie el nuevo contenedor con algunos volúmenes

Variables de entorno
---------------------

La lista de las varibles de entorno esta disponible en [esta pagina](env.markdown).

Archivos configurados
------------

- El contenedor ya incluye un archivo de configuracion personalizado `/var/www/app/config.php`.
- Puede almacenar su propio archivo de configuracion de volumen de datos`/var/www/app/data/config.php`.


Referencias
----------

- [Official Kanboard images](https://registry.hub.docker.com/u/kanboard/kanboard/)
- [Docker documentation](https://docs.docker.com/)
- [Dockerfile stable version](https://github.com/kanboard/docker)
- [Dockerfile dev version](https://github.com/kanboard/kanboard/blob/master/Dockerfile)
- [Imagenes oficiales de Kanboard](https://registry.hub.docker.com/u/kanboard/kanboard/)
- [Documentacionde Docker](https://docs.docker.com/)
- [Dockerfile version estable](https://github.com/kanboard/docker)
- [Dockerfile version dev](https://github.com/kanboard/kanboard/blob/master/Dockerfile)
