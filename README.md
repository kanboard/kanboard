Kanboard
========

[![Build Status](https://travis-ci.org/kanboard/kanboard.svg?branch=master)](https://travis-ci.org/kanboard/kanboard)

Kanboard is project management software that focuses on the Kanban methodology.

- Official website: <https://kanboard.org/>
- [List of features](https://kanboard.org/#features)
- [Change Log](https://github.com/kanboard/kanboard/blob/master/ChangeLog)
- [Forum](https://kanboard.discourse.group/)
- Official documentation: <https://docs.kanboard.org/>
    - [Requirements](https://docs.kanboard.org/en/latest/admin_guide/requirements.html)
    - [Installation instructions](https://docs.kanboard.org/en/latest/admin_guide/installation.html)
    - [Upgrade to a new version](https://docs.kanboard.org/en/latest/admin_guide/upgrade.html)
    - [Use Kanboard with Docker](https://docs.kanboard.org/en/latest/admin_guide/docker.html)

Credits
-------

- Main developer: Frédéric Guillot
- [Contributors](https://github.com/kanboard/kanboard/graphs/contributors)
- Distributed under [MIT License](https://github.com/kanboard/kanboard/blob/master/LICENSE)

## Docker commands

To run this project locally, use the following command:

```
docker run --name kanboard -p8088:80 kanboard/kanboard
```

Log into `http://localhost:8088` and use the credentials `admin:admin` to log in for the first time.  You can create extra users then.