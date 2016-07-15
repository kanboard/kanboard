Развертывание Канборд на Heroku
===============================

Вы можете бесплатно испытать работу Kanboard на [Heroku](https://www.heroku.com/). Вам нужно нажать кнопку **Deploy to Heroku** и следовать руководству приведенному ниже:

[![Deploy](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy?template=https://github.com/fguillot/kanboard)



Требования[¶](#requirements "Ссылка на этот заголовок")
-------------------------------------------------------



-   Учетная запись на Heroku. Вы можете зарегистрироваться бесплатно.
-   Установленная утилита командной строки Heroku



Руководство по установке[¶](#manual-instructions "Ссылка на этот заголовок")
----------------------------------------------------------------------------


    # Get the last development version

    git clone https://github.com/fguillot/kanboard.git

    cd kanboard



    # Push the code to Heroku (You can also use SSH if git over HTTP doesn't work)

    heroku create

    git push heroku master



    # Start a new dyno with a Postgresql database

    heroku ps:scale web=1

    heroku addons:add heroku-postgresql:hobby-dev



    # Open your browser

    heroku open



Ограничения[¶](#limitations "Ссылка на этот заголовок")
-------------------------------------------------------

-   Хранилище на Heroku эфимерное. Это означает, что файлы, загружаемые через Канборд, будут отсутствовать в системе после перезагрузки. Вы можете установить плагин для хранения файлов в облаке, например [Amazon S3](https://github.com/kanboard/plugin-s3).
-   Некоторые возможности Канборда требуют, чтобы вы выполняли [запуск ежедневных фоновых задач](cronjob.markdown).




 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

