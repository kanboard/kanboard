How to run Kanboard with Docker?
================================

Kanboard can run easily with [Docker](https://www.docker.com).

The image size is approximately **50MB** and contains:

- [Alpine Linux](http://alpinelinux.org/)
- The [process manager S6](http://skarnet.org/software/s6/)
- Nginx
- PHP 7

The Kanboard cronjob is also running everyday at midnight.
URL rewriting is enabled in the included config file.

When the container is running, the memory utilization is around **30MB**.

Use the latest release version
------------------------------

From the list of tags on the [Docker Hub](https://hub.docker.com/r/kanboard/kanboard/), choose the version that you would like to install.

For example, to install the version 1.1.1:

```bash
docker pull kanboard/kanboard:v1.1.1
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:v1.1.1
```

Use the development version (automated build)
---------------------------------------------

Every new commit on the repository trigger a new build on the [Docker Hub](https://hub.docker.com/r/kanboard/kanboard/).

```bash
docker pull kanboard/kanboard:latest
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest
```

The tag **latest** is the **development version** of Kanboard and represents the master branch, use at your own risk.

Build your own Docker image
---------------------------

Clone the Kanboard repository and run the following command:

```bash
make docker-image
```

Volumes
-------

You can attach 2 volumes to your container:

- Data folder: `/var/www/app/data`
- Plugins folder: `/var/www/app/plugins`

Use the flag `-v` to mount a volume on the host machine like described in [official Docker documentation](https://docs.docker.com/engine/userguide/containers/dockervolumes/).

There is also a `docker-compose.yml` file in the repository.

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

- The container already include a custom config file located at `/var/www/app/config.php`.
- You can store your own config file on the data volume: `/var/www/app/data/config.php`.
- You must restart the container to take into account the new parameters of your custom config file.

Notes
-----

- [Official Kanboard images](https://hub.docker.com/r/kanboard/kanboard/)
- [Docker documentation](https://docs.docker.com/)
- Since Kanboard > v1.1.0, the tag "stable" is not used anymore
- To send emails, you must use the SMTP method or a plugin like Mailgun/Sendgrid/Postmark
