Синтаксис расширенного поиска[¶](#advanced-search-syntax "Ссылка на этот заголовок")

====================================================================================



В Канборде используется простой язык запросов для расширенного поиска. Вы можете искать задачи, комментарии, подзадачи, ссылки, но только активные.



Пример запроса[¶](#example-of-query "Ссылка на этот заголовок")

---------------------------------------------------------------



В этом примере показываются как отобразить задачи назначенные мне с датой окончания завтра и название содержит “my title”:



    assigne:me due:tomorrow my title



Глобальный поиск[¶](#global-search "Ссылка на этот заголовок")

--------------------------------------------------------------



### Поиск по id задачи или названию задачи[¶](#search-by-task-id-or-title "Ссылка на этот заголовок")



-   Поиск задачи по id: `#123`{.docutils .literal}



-   Поиск по id задачи и названию задачи: `123`{.docutils .literal}



-   Поиск по названию задачи: `любые слова и цифры`{.docutils .literal}, но не должны содержать атрибуты поиска



### Поиск по статусу[¶](#search-by-status "Ссылка на этот заголовок")



Атрибут: **status**



-   Запрос на поиск открытых задач: `status:open`{.docutils .literal}



-   Запрос на поиск закрытых задач: `status:closed`{.docutils .literal}



### Поиск по испольнителю[¶](#search-by-assignee "Ссылка на этот заголовок")



Атрибут: **assignee**



-   Поиск по полному имени испольнителя: `assignee:"Петр Иванов"`{.docutils .literal}



-   Поиск исполнителя по имени пользователя: `assignee:pivanov`{.docutils .literal}



-   Отбор нескольких испольнителей: `assignee:tsemenov assignee:"Петр Иванов"`{.docutils .literal}



-   Поиск задач без исполнителя: `assignee:nobody`{.docutils .literal}



-   Поиск задач назначенных мне: `assignee:me`{.docutils .literal}



### Поиск по создателю задач[¶](#search-by-task-creator "Ссылка на этот заголовок")



Атрибут: **creator**



-   Отбор задач созданных мной: `creator:me`{.docutils .literal}



-   Отбор задач которые создал Петр Иванов: `creator:"Петр Иванов"`{.docutils .literal}



-   Отбор задач созданных пользователем с id \#1: `creator:1`{.docutils .literal}



### Поиск по исполнителю подзадач[¶](#search-by-subtask-assignee "Ссылка на этот заголовок")



Атрибут: **subtask:assignee**



-   Например: `subtask:assignee:"Петр Иванов"`{.docutils .literal}



### Поиск по цвету[¶](#search-by-color "Ссылка на этот заголовок")



Атрибут: **color**



-   Отбор по цвету с id blue: `color:blue`{.docutils .literal}



-   Отбор по названию цвета: `color:"Deep Orange"`{.docutils .literal}



### Отбор по “Сделать до”[¶](#search-by-the-due-date "Ссылка на этот заголовок")



Атрибут: **due**



-   Поиск задач со сроком испольнения до сегодня: `due:today`{.docutils .literal}



-   Поиск задач со сроком исполнения завтра: `due:tomorrow`{.docutils .literal}



-   Поиск задач со сроком исполнения вчера: `due:yesterday`{.docutils .literal}



-   Поиск задач с конкретной датой исполнения: `due:2016-06-29`{.docutils .literal}



Дата должна быть в формате ISO 8601: **YYYY-MM-DD**.



Все строковые форматы поддерживаемые функцией `strtotime()`{.docutils .literal} допустимы. Например, `next Thursday`{.docutils .literal}, `-2 days`{.docutils .literal}, `+2 months`{.docutils .literal}, `tomorrow`{.docutils .literal} и т.д.



Операторы сравнения с датой:



-   Старше чем: **due:\>2015-06-29**



-   Моложе чем: **due:\<2015-06-29**



-   Старше чем или равно: **due:\>=2015-06-29**



-   Моложе чем или равно: **due:\<=2015-06-29**



### Поиск по дате изменения[¶](#search-by-modification-date "Ссылка на этот заголовок")



Атрибут: **modified** или **updated**



Формат даты такой же как и у “Сделать до”



Отфильтровать недавно измененные задачи: `modified:recently`{.docutils .literal}.



Этот запрос использует тоже значение что и в настройках Доски - “Время подсвечивания задачи”.



### Поиск по дате создания[¶](#search-by-creation-date "Ссылка на этот заголовок")



Атрибут: **created**



Работает также как и поиск по дате изменения.



### Поиск по описанию[¶](#search-by-description "Ссылка на этот заголовок")



Атрибут: **description** or **desc**



Например: `description:"здесь пишем тескт для поиска"`{.docutils .literal}



### Поиск по внешним ссылкам[¶](#search-by-external-reference "Ссылка на этот заголовок")



Например: нужно найти задачу, которая содержит ссылку на id или название другой задачи.



-   `ref:1234`{.docutils .literal} или `reference:TICKET-1234`{.docutils .literal}



### Поиск по категории[¶](#search-by-category "Ссылка на этот заголовок")



Атрибут: **category**



-   Найти задачи с указанной категорией: `category:"Важные запросы"`{.docutils .literal}



-   Найти задачи, которые содержать указанные категории: `category:"Ошибки" category:"Изменения"`{.docutils .literal}



-   Найти задачи без категорий: `category:none`{.docutils .literal}



### Поиск проектов[¶](#search-by-project "Ссылка на этот заголовок")



Атрибут: **project**



-   Поиск задач по имени проекта: `project:"Какой-то проект"`{.docutils .literal}



-   Поиск задач по id проекта: `project:23`{.docutils .literal}



-   Поиск задач в нескольких проектах: `project:"Проект A" project:"Проект B"`{.docutils .literal}



### Поиск в колонках[¶](#search-by-columns "Ссылка на этот заголовок")



Атрибут: **column**



-   Поиск задач в указанной колонке: `column:"В работе"`{.docutils .literal}



-   Поиск задач в нескольких колонках: `column:"Невыполненные заказы" column:ready`{.docutils .literal}



### Поиск в Дорожках[¶](#search-by-swim-lane "Ссылка на этот заголовок")



Атрибут: **swimlane**



-   Поиск задач в указанной Дорожке: `swimlane:"Версия 42"`{.docutils .literal}



-   Поиск задач в базовой Дорожке: `swimlane:default`{.docutils .literal}



-   Поиск задач в нескольких Дорожках: `swimlane:"Версия 1.2" swimlane:"Версия 1.3"`{.docutils .literal}



### Поиск ссылки на задачу[¶](#search-by-task-link "Ссылка на этот заголовок")



Атрибут: **link**



-   Поиск задач содержащих ссылку: `link:"это веха задачи "`{.docutils .literal}



-   Поиск задач по нескольким ссылкам: `link:"веха задачи " link:"относится к"`{.docutils .literal}



### Поиск по комментарию[¶](#search-by-comment "Ссылка на этот заголовок")



Атрибут: **comment**



-   Найти комментарии, которые содержат указанное название: `comment:"Какое-то название"`{.docutils .literal}



Поиск активности задач[¶](#activity-stream-search "Ссылка на этот заголовок")

-----------------------------------------------------------------------------



### Поиск событий по названию задачи[¶](#search-events-by-task-title "Ссылка на этот заголовок")



Атрибут: **title** или без ничего (по умолчанию)



-   Например: `title:"My task"`{.docutils .literal}



-   Поиск задачи по id: `#123`{.docutils .literal}



### Поиск событий по статусу задачи[¶](#search-events-by-task-status "Ссылка на этот заголовок")



Атрибут: **status**



### Поиск событий по создателю[¶](#search-by-event-creator "Ссылка на этот заголовок")



Атрибут: **creator**



### Поиск событий по дате создания[¶](#search-by-event-creation-date "Ссылка на этот заголовок")



Атрибут: **created**



### Поиск событий по проекту[¶](#search-events-by-project "Ссылка на этот заголовок")



Атрибут: **project**



### [Оглавление](index.markdown)



-   [Синтаксис расширенного поиска](#)

    -   [Пример запроса](#example-of-query)

    -   [Глобальный поиск](#global-search)

        -   [Поиск по id задачи или названию задачи](#search-by-task-id-or-title)

        -   [Поиск по статусу](#search-by-status)

        -   [Поиск по испольнителю](#search-by-assignee)

        -   [Поиск по создателю задач](#search-by-task-creator)

        -   [Поиск по исполнителю подзадач](#search-by-subtask-assignee)

        -   [Поиск по цвету](#search-by-color)

        -   [Отбор по “Сделать до”](#search-by-the-due-date)

        -   [Поиск по дате изменения](#search-by-modification-date)

        -   [Поиск по дате создания](#search-by-creation-date)

        -   [Поиск по описанию](#search-by-description)

        -   [Поиск по внешним ссылкам](#search-by-external-reference)

        -   [Поиск по категории](#search-by-category)

        -   [Поиск проектов](#search-by-project)

        -   [Поиск в колонках](#search-by-columns)

        -   [Поиск в Дорожках](#search-by-swim-lane)

        -   [Поиск ссылки на задачу](#search-by-task-link)

        -   [Поиск по комментарию](#search-by-comment)

    -   [Поиск активности задач](#activity-stream-search)

        -   [Поиск событий по названию задачи](#search-events-by-task-title)

        -   [Поиск событий по статусу задачи](#search-events-by-task-status)

        -   [Поиск событий по создателю](#search-by-event-creator)

        -   [Поиск событий по дате создания](#search-by-event-creation-date)

        -   [Поиск событий по проекту](#search-events-by-project)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/ext-search.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/ext-search.txt)

