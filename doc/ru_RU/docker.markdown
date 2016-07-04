Как запустить Канборд с Docker?[¶](#how-to-run-kanboard-with-docker "Ссылка на этот заголовок")

===============================================================================================



Канборд можно легко запустить с [Docker](https://www.docker.com).



Размер образа, приблизительно, **50MB** содержит:



-   [Alpine Linux](http://alpinelinux.org/)

-   The [process manager S6](http://skarnet.org/software/s6/)

-   Nginx

-   PHP-FPM



Канборд запускает фоновые задачи каждый день в полночь. Переписывание URL (URL rewriting) включено в базовой конфигурации.



Когда контейнер запущен, использование памяти около **20MB**.



Использование стабильной версии[¶](#use-the-stable-version "Ссылка на этот заголовок")

--------------------------------------------------------------------------------------



Для получения последней стабильной версии Канборда используйте тег **stable**:



    docker pull kanboard/kanboard

    docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:stable



Использование разрабатываемой версии (автоматической сборки)[¶](#use-the-development-version-automated-build "Ссылка на этот заголовок")

----------------------------------------------------------------------------------------------------------------------------------------



Каждый новый коммит в репозитории вызывает новую сборку в [Docker Hub](https://registry.hub.docker.com/u/kanboard/kanboard/).



    docker pull kanboard/kanboard

    docker run -d --name kanboard -p 80:80 -t kanboard/kanboard:latest



Используя **разрабатываемую версию** Канборда с тегом **latest**, вы принимаете на себя все риски нестабильной версии.



Создание своего образа Docker[¶](#build-your-own-docker-image "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------



Для сборки своего образа, в репозитории Канборда имеется `Dockerfile`{.docutils .literal}. Склонируйте репозиторий Канборда и выполните следующую команду:



    docker build -t youruser/kanboard:master .



или



    make docker-image



Для запуска вашего контейнера в фоновом режиме на порту 80:



    docker run -d --name kanboard -p 80:80 -t youruser/kanboard:master



Тома[¶](#volumes "Ссылка на этот заголовок")

--------------------------------------------



Вы можете прикрепить 2 тома к вашему контейнеру:



-   Каталог с данными: `/var/www/kanboard/data`{.docutils .literal}



-   Католог с плагинами: `/var/www/kanboard/plugins`{.docutils .literal}



Используйте опцию `-v`{.docutils .literal} для монтирования тома на удаленной машине как описано в [официальной документации Docker](https://docs.docker.com/engine/userguide/containers/dockervolumes/).



Обновление вашего контейнера[¶](#upgrade-your-container "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------



-   Загрузите новый образ



-   Удалите старый контейнер



-   Перезапустите новый контейнер с теми же томами



Переменные окружения[¶](#environment-variables "Ссылка на этот заголовок")

--------------------------------------------------------------------------



Список переменных окружения доступен на [этой странице](env.markdown).



Файлы конфигурации[¶](#config-files "Ссылка на этот заголовок")

---------------------------------------------------------------



-   Контейнер уже содержит конфигурационный файл расположенный в `/var/www/kanboard/config.php`{.docutils .literal}.



-   Вы можете сохранить свой конфиг файл в томе с данными: `/var/www/kanboard/data/config.php`{.docutils .literal}.



Ссылки[¶](#references "Ссылка на этот заголовок")

-------------------------------------------------



-   [Официальные образы Канборд](https://registry.hub.docker.com/u/kanboard/kanboard/)



-   [Документация Docker](https://docs.docker.com/)



-   [Стабильная версия Dockerfile](https://github.com/kanboard/docker)



-   [Разрабатываемая версия Dockerfile](https://github.com/fguillot/kanboard/blob/master/Dockerfile)



### [Оглавление](index.markdown)



-   [Как запустить Канборд с Docker?](#)

    -   [Использование стабильной версии](#use-the-stable-version)

    -   [Использование разрабатываемой версии (автоматической сборки)](#use-the-development-version-automated-build)

    -   [Создание своего образа Docker](#build-your-own-docker-image)

    -   [Тома](#volumes)

    -   [Обновление вашего контейнера](#upgrade-your-container)

    -   [Переменные окружения](#environment-variables)

    -   [Файлы конфигурации](#config-files)

    -   [Ссылки](#references)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

