Plugin Hooks[¶](#plugin-hooks "Ссылка на этот заголовок")

=========================================================



Application Hooks[¶](#application-hooks "Ссылка на этот заголовок")

-------------------------------------------------------------------



Hooks can extend, replace, filter data or change the default behavior. Each hook is identified with a unique name, example: `controller:calendar:user:events`{.docutils .literal}



### Listen on hook events[¶](#listen-on-hook-events "Ссылка на этот заголовок")



In your `initialize()`{.docutils .literal} method you need to call the method `on()`{.docutils .literal} of the class `Kanboard\Core\Plugin\Hook`{.docutils .literal}:



    $this->hook->on('hook_name', $callable);



The first argument is the name of the hook and the second is a PHP callable.



### Hooks executed only once[¶](#hooks-executed-only-once "Ссылка на этот заголовок")



Some hooks can have only one listener:



#### model:subtask-time-tracking:calculate:time-spent[¶](#model-subtask-time-tracking-calculate-time-spent "Ссылка на этот заголовок")



-   Override time spent calculation when sub-task timer is stopped

-   Arguments:

    -   `$user_id`{.docutils .literal} (integer)

    -   `$start`{.docutils .literal} (DateTime)

    -   `$end`{.docutils .literal} (DateTime)



### Merge hooks[¶](#merge-hooks "Ссылка на этот заголовок")



“Merge hooks” act in the same way as the function `array_merge`{.docutils .literal}. The hook callback must return an array. This array will be merged with the default one.



Example to add events in the user calendar:



    class Plugin extends Base

    {

        public function initialize()

        {

            $container = $this->container;



            $this->hook->on('controller:calendar:user:events', function($user_id, $start, $end) use ($container) {

                $model = new SubtaskForecast($container);

                return $model->getCalendarEvents($user_id, $end); // Return new events

            });

        }

    }



Example to override default values for task forms:



    class Plugin extends Base

    {

        public function initialize()

        {

            $this->hook->on('controller:task:form:default', function (array $default_values) {

                return empty($default_values['score']) ? array('score' => 4) : array();

            });

        }

    }



List of merging hooks:



#### controller:task:form:default[¶](#controller-task-form-default "Ссылка на этот заголовок")



-   Override default values for task forms

-   Arguments:

    -   `$default_values`{.docutils .literal}: actual default values (array)



#### controller:calendar:project:events[¶](#controller-calendar-project-events "Ссылка на этот заголовок")



-   Add more events to the project calendar

-   Arguments:

    -   `$project_id`{.docutils .literal} (integer)

    -   `$start`{.docutils .literal} Calendar start date (string, ISO-8601 format)

    -   `$end`{.docutils .literal} Calendar\` end date (string, ISO-8601 format)



#### controller:calendar:user:events[¶](#controller-calendar-user-events "Ссылка на этот заголовок")



-   Add more events to the user calendar

-   Arguments:

    -   `$user_id`{.docutils .literal} (integer)

    -   `$start`{.docutils .literal} Calendar start date (string, ISO-8601 format)

    -   `$end`{.docutils .literal} Calendar end date (string, ISO-8601 format)



Asset Hooks[¶](#asset-hooks "Ссылка на этот заголовок")

-------------------------------------------------------



Asset hooks can be used to add a new stylesheet easily or a new JavaScript file in the layout. You can use this feature to create a theme and override all Kanboard default styles.



Example to add a new stylesheet:



    <?php



    namespace Kanboard\Plugin\Css;



    use Kanboard\Core\Plugin\Base;



    class Plugin extends Base

    {

        public function initialize()

        {

            $this->hook->on('template:layout:css', 'plugins/Css/skin.css');

        }

    }



List of asset Hooks:



-   `template:layout:css`{.docutils .literal}

-   `template:layout:js`{.docutils .literal}



Template Hooks[¶](#template-hooks "Ссылка на этот заголовок")

-------------------------------------------------------------



Template hooks allow to add new content in existing templates.



Example to add new content in the dashboard sidebar:



    $this->template->hook->attach('template:dashboard:sidebar', 'myplugin:dashboard/sidebar');



This call is usually defined in the `initialize()`{.docutils .literal} method. The first argument is name of the hook and the second argument is the template name.



Template names prefixed with the plugin name and colon indicate the location of the template.



Example with `myplugin:dashboard/sidebar`{.docutils .literal}:



-   `myplugin`{.docutils .literal} is the name of your plugin (lowercase)

-   `dashboard/sidebar`{.docutils .literal} is the template name

-   On the filesystem, the plugin will be located here: `plugins\Myplugin\Template\dashboard\sidebar.php`{.docutils .literal}

-   Templates are written in pure PHP (don’t forget to escape data)



Template names without prefix are core templates.



List of template hooks:



  ------------------------------------------------------------------------

  Hook                               Description

  ---------------------------------- -------------------------------------

  `template:analytic:sidebar`{.docut Sidebar on analytic pages

  ils                                

  .literal}                          



  `template:app:filters-helper:befor Filter helper dropdown (top)

  e`{.docutils                       

  .literal}                          



  `template:app:filters-helper:after Filter helper dropdown (bottom)

  `{.docutils                        

  .literal}                          



  `template:auth:login-form:before`{ Login page (top)

  .docutils                          

  .literal}                          



  `template:auth:login-form:after`{. Login page (bottom)

  docutils                           

  .literal}                          



  `template:board:private:task:befor Task in private board: before title

  e-title`{.docutils                 

  .literal}                          



  `template:board:private:task:after Task in private board: after title

  -title`{.docutils                  

  .literal}                          



  `template:board:public:task:before Task in public board: before title

  -title`{.docutils                  

  .literal}                          



  `template:board:public:task:after- Task in public board: after title

  title`{.docutils                   

  .literal}                          



  `template:board:task:footer`{.docu Task in board: footer

  tils                               

  .literal}                          



  `template:board:task:icons`{.docut Task in board: tooltip icon

  ils                                

  .literal}                          



  `template:config:sidebar`{.docutil Sidebar on settings page

  s                                  

  .literal}                          



  `template:config:application`{.doc Application settings form

  utils                              

  .literal}                          



  `template:config:integrations`{.do Integration page in global settings

  cutils                             

  .literal}                          



  `template:dashboard:sidebar`{.docu Sidebar on dashboard page

  tils                               

  .literal}                          



  `template:export:sidebar`{.docutil Sidebar on export pages

  s                                  

  .literal}                          



  `template:layout:head`{.docutils   Page layout `<head/>`{.docutils

  .literal}                          .literal} tag



  `template:layout:top`{.docutils    Page layout top header

  .literal}                          



  `template:layout:bottom`{.docutils Page layout footer

  .literal}                          



  `template:project:dropdown`{.docut “Actions” menu on left in different

  ils                                project views

  .literal}                          



  `template:project:header:before`{. Project filters (before)

  docutils                           

  .literal}                          



  `template:project:header:after`{.d Project filters (after)

  ocutils                            

  .literal}                          



  `template:project:integrations`{.d Integration page in projects settings

  ocutils                            

  .literal}                          



  `template:project:sidebar`{.docuti Sidebar in project settings

  ls                                 

  .literal}                          



  `template:project-user:sidebar`{.d Sidebar on project user overview page

  ocutils                            

  .literal}                          



  `template:task:layout:top`{.docuti Task layout top (after page header)

  ls                                 

  .literal}                          



  `template:task:details:top`{.docut Task summary top

  ils                                

  .literal}                          



  `template:task:details:bottom`{.do Task summary bottom

  cutils                             

  .literal}                          



  `template:task:details:first-colum Task summary first column

  n`{.docutils                       

  .literal}                          



  `template:task:details:second-colu Task summary second column

  mn`{.docutils                      

  .literal}                          



  `template:task:details:third-colum Task summary third column

  n`{.docutils                       

  .literal}                          



  `template:task:details:fourth-colu Task summary fourth column

  mn`{.docutils                      

  .literal}                          



  `template:task:dropdown`{.docutils Task dropdown menu in listing pages

  .literal}                          



  `template:task:sidebar:actions`{.d Sidebar on task page (section

  ocutils                            actions)

  .literal}                          



  `template:task:sidebar:information Sidebar on task page (section

  `{.docutils                        information)

  .literal}                          



  `template:task:form:left-column`{. Left column in task form

  docutils                           

  .literal}                          



  `template:task:form:right-column`{ Right column in task form

  .docutils                          

  .literal}                          



  `template:task:show:top`{.docutils Show task page: top

  .literal}                          



  `template:task:show:bottom`{.docut Show task page: bottom

  ils                                

  .literal}                          



  `template:task:show:before-descrip Show task page: before description

  tion`{.docutils                    

  .literal}                          



  `template:task:show:before-tasklin Show task page: before tasklinks

  ks`{.docutils                      

  .literal}                          



  `template:task:show:before-subtask Show task page: before subtasks

  s`{.docutils                       

  .literal}                          



  `template:task:show:before-timetra Show task page: before timetracking

  cking`{.docutils                   

  .literal}                          



  `template:task:show:before-attachm Show task page: before attachments

  ents`{.docutils                    

  .literal}                          



  `template:task:show:before-comment Show task page: before comments

  s`{.docutils                       

  .literal}                          



  `template:user:authentication:form “Edit authentication” form in user

  `{.docutils                        profile

  .literal}                          



  `template:user:create-remote:form` “Create remote user” form

  {.docutils                         

  .literal}                          



  `template:user:external`{.docutils “External authentication” page in

  .literal}                          user profile



  `template:user:integrations`{.docu Integration page in user profile

  tils                               

  .literal}                          



  `template:user:sidebar:actions`{.d Sidebar in user profile (section

  ocutils                            actions)

  .literal}                          



  `template:user:sidebar:information Sidebar in user profile (section

  `{.docutils                        information)

  .literal}                          

  ------------------------------------------------------------------------



Another template hooks can be added if necessary, just ask on the issue tracker.



### [Оглавление](index.markdown)



-   [Plugin Hooks](#)

    -   [Application Hooks](#application-hooks)

        -   [Listen on hook events](#listen-on-hook-events)

        -   [Hooks executed only once](#hooks-executed-only-once)

            -   [model:subtask-time-tracking:calculate:time-spent](#model-subtask-time-tracking-calculate-time-spent)

        -   [Merge hooks](#merge-hooks)

            -   [controller:task:form:default](#controller-task-form-default)

            -   [controller:calendar:project:events](#controller-calendar-project-events)

            -   [controller:calendar:user:events](#controller-calendar-user-events)

    -   [Asset Hooks](#asset-hooks)

    -   [Template Hooks](#template-hooks)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

