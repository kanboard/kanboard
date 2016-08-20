Переопределение URL
===================



Канборд может работать и с переопределенными URL и с простыми.



-   Пример переопределенного URL: `/board/123`



-   По другому: `?controller=board&action=show&project_id=123`



Если вы используете Канборд с Apache и включенным mode rewrite, красивые URL будут использоваться автоматически. В случае, если вы получаете ошибку “404 Not Found”, то возможно надо внести изменения в DocumentRoot:



    <Directory /var/www/kanboard/>

        AllowOverride FileInfo Options=All,MultiViews AuthConfig

    </Directory>



URL ярлыки[¶](#url-shortcuts "Ссылка на этот заголовок")
--------------------------------------------------------



-   Перейти к задаче \#123: **/t/123**



-   Перейти на доску в проект \#2: **/b/2**



-   Перейти в календарь проекта \#5: **/c/5**



-   Перейти к просмотру списком проекта \#8: **/l/8**



-   Перейти к настройкам проекта для проекта id \#42: **/p/42**



Настройка[¶](#configuration "Ссылка на этот заголовок")
-------------------------------------------------------



По умолчанию, Канборд проверяет включен ли в Apache mode rewrite.



Для исключения автоматической проверки переопределения URL на веб сервере, вы должны включить эту опцию в вашем конфигурационном фале:



    define('ENABLE_URL_REWRITE', true);



Когда константа имеет значение `true`:



-   Сгенерированные из утилиты командной строки URL будут также преобразованы



-   Если вы используете другой веб сервер вместо Apache, например Nginx или Microsoft IIS, вы можете сами настроить переопределение URL



Примечание: Канборд всегда использует URL по “старинке”, если данная константа не настроена. Эта настройка опциональна.



Пример настройки Nginx[¶](#nginx-configuration-example "Ссылка на этот заголовок")
----------------------------------------------------------------------------------



В разделе `server`, вашего конфигурационного файла Nginx, вы можете использовать этот пример:



    index index.php;



    location / {

        try_files $uri $uri/ /index.php$is_args$args;



        # If Kanboard is under a subfolder

        # try_files $uri $uri/ /kanboard/index.php;

    }



    location ~ \.php$ {

        try_files $uri =404;

        fastcgi_split_path_info ^(.+\.php)(/.+)$;

        fastcgi_pass unix:/var/run/php5-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

        fastcgi_index index.php;

        include fastcgi_params;

    }



    # Deny access to the directory data

    location ~* /data {

        deny all;

        return 404;

    }



    # Deny access to .htaccess

    location ~ /\.ht {

        deny all;

        return 404;

    }



В конфигурационном файле Канборда `config.php`:



    define('ENABLE_URL_REWRITE', true);



Адаптируйте пример приведенный выше к вашей конфигурации.



Пример настройки IIS[¶](#iis-configuration-example "Ссылка на этот заголовок")
------------------------------------------------------------------------------



Создайте web.config в каталоге где установлен Канборд:



    <?xml version="1.0" encoding="UTF-8"?>

    <configuration>

        <system.webServer>

            <rewrite>

                <rules>

                    <rule name="Imported Rule 1" stopProcessing="true">

                        <match url="^" ignoreCase="false" />

                        <conditions logicalGrouping="MatchAll">

                            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />

                        </conditions>

                        <action type="Rewrite" url="index.php" appendQueryString="true" />

                    </rule>

                </rules>

            </rewrite>

        </system.webServer>

    </configuration>



В конфигурационном файле Канборда `config.php`:



    define('ENABLE_URL_REWRITE', true);



Адаптируйте пример приведенный выше к вашей конфигурации.



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

