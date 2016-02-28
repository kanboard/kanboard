How to run Kanboard with Docker?
================================

Kanboard can run easily with [Docker](https://www.docker.com).

The image size is approximately **50MB** and contains:

- [Alpine Linux](http://alpinelinux.org/)
- The [process manager S6](http://skarnet.org/software/s6/)
- Nginx
- PHP-FPM

The Kanboard cronjob is also running everyday at midnight.
URL rewriting is enabled in the included config file.

When the container is running, the memory utilization is around **20MB**.

Use the stable version
----------------------

To fetch the latest stable release of Kanboard use the tag **stable**:

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:stable
```

Use the development version (automated build)
---------------------------------------------

Every new commit on the repository trigger a new build on the [Docker Hub](https://registry.hub.docker.com/u/kanboard/kanboard/).

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest
```

The tag **latest** is the **development version** of Kanboard, use at your own risk.

Build your own Docker image
---------------------------

There is a `Dockerfile` in the Kanboard repository to build your own image.
Clone the Kanboard repository and run the following command:

```bash
docker build -t youruser/kanboard:master .
```

or

```bash
make docker-image
```

To run your container in background on the port 80:

```bash
docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master
```

Volumes
-------

You can attach 2 volumes to your container:

- Data folder: `/var/www/kanboard/data`
- Plugins folder: `/var/www/kanboard/plugins`

Use the flag `-v` to mount a volume on the host machine like described in [official Docker documentation](https://docs.docker.com/engine/userguide/containers/dockervolumes/).

Upgrade your container
----------------------

- Pull the new image
- Remove the old container
- Restart a new container with the same volumes

Environment variables
---------------------

The list of environment variables is available on [this page](env.markdown).

Config files
------------

- The container already include a custom config file located at `/var/www/kanboard/config.php`.
- You can store your own config file on the data volume: `/var/www/kanboard/data/config.php`.

References
----------

- [Official Kanboard images](https://registry.hub.docker.com/u/kanboard/kanboard/)
- [Docker documentation](https://docs.docker.com/)
- [Dockerfile stable version](https://github.com/kanboard/docker)
- [Dockerfile dev version](https://github.com/fguillot/kanboard/blob/master/Dockerfile)
