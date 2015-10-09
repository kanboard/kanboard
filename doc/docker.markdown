How to run Kanboard with Docker?
================================

Kanboard can run easily with [Docker](https://www.docker.com).
There is a `Dockerfile` in the repository to build your own container.

Use the stable version
----------------------

To fetch the latest stable release of Kanboard use the tag **stable**:

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:stable
```

Use the development version (automated build)
---------------------------------------------

Every new commit on the repository trigger a new build on [Docker Hub](https://registry.hub.docker.com/u/kanboard/kanboard/).

```bash
docker pull kanboard/kanboard
docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest
```

The tag **latest** is the **development version** of Kanboard, use at your own risk.

Build your own Docker image
---------------------------

Clone the Kanboard repository and run the following command:

```bash
docker build -t youruser/kanboard:master .
```

To run your image in background on the port 80:

```bash
docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master
```

Store your data on a volume
---------------------------

By default Kanboard will store attachments and the Sqlite database in the directory data. Run this command to use a custom volume path:

```bash
docker run -d --name kanboard -v /your/local/data/folder:/var/www/html/data -p 80:80 -t kanboard/kanboard:master
```

References
----------

- [Official Kanboard images](https://registry.hub.docker.com/u/kanboard/kanboard/)
- [Docker documentation](https://docs.docker.com/)
- [Dockerfile stable version](https://github.com/kanboard/docker/blob/master/Dockerfile)
- [Dockerfile dev version](https://github.com/fguillot/kanboard/blob/master/Dockerfile)
