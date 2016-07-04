Custom Routes[¶](#custom-routes "Ссылка на этот заголовок")

===========================================================



When URL rewriting is enabled, you can define custom routes from your plugins.



Define new routes[¶](#define-new-routes "Ссылка на этот заголовок")

-------------------------------------------------------------------



Routes are handled by the class `Kanboard\Core\Http\Route`{.docutils .literal}.



New routes can be added by using the method `addRoute($path, $controller, $action, $plugin)`{.docutils .literal}, here an example:



    $this->route->addRoute('/my/custom/route', 'myController', 'myAction', 'myplugin');



When the end-user go to the URL `/my/custom/route`{.docutils .literal}, the method `Kanboard\Plugin\Myplugin\Controller\MyController::myAction()`{.docutils .literal} will be executed.



The first character of the controller and the plugin name will converted in uppercase with the function `ucfirst()`{.docutils .literal}.



You can also define routes with variables:



    $this->route->addRoute('/my/route/:my_variable', 'myController', 'myAction', 'myplugin');



The colon prefix `:`{.docutils .literal}, define a variable. For example `:my_variable`{.docutils .literal} declare a new variable named `my_variable`{.docutils .literal}.



To fetch the value of the variable you can use the method `getStringParam()`{.docutils .literal} or `getIntegerParam()`{.docutils .literal} from the class `Kanboard\Core\Http\Request`{.docutils .literal}:



If we have the URL `/my/route/foobar`{.docutils .literal}, the value of `my_variable`{.docutils .literal} is `foobar`{.docutils .literal}:



    $this->request->getStringParam('my_variable'); // Return foobar



Generate links based on the routing table[¶](#generate-links-based-on-the-routing-table "Ссылка на этот заголовок")

-------------------------------------------------------------------------------------------------------------------



From templates, you have to use the helper `Kanboard\Helper\Url`{.docutils .literal}.



### Generate a HTML link[¶](#generate-a-html-link "Ссылка на этот заголовок")



    <?= $this->url->link('My link', 'mycontroller', 'myaction', array('plugin' => 'myplugin')) ?>



Will generate this HTML:



    <a href="/my/custom/route">My link</a>



### Generate only the attribute `href`{.docutils .literal}:[¶](#generate-only-the-attribute-href "Ссылка на этот заголовок")



    <?= $this->url->href('My link', 'mycontroller', 'myaction', array('plugin' => 'myplugin')) ?>



HTML output:



    /my/custom/route



HTML output when URL rewriting is not enabled:



    ?controller=mycontroller&amp;action=myaction&amp;plugin=myplugin



### Generate redirect link:[¶](#generate-redirect-link "Ссылка на этот заголовок")



From a controller, if you need to perform a redirection:



    $this->url->to('mycontroller', 'myaction', array('plugin' => 'myplugin'));



Generate:



    ?controller=mycontroller&action=myaction&plugin=myplugin



### [Оглавление](index.markdown)



-   [Custom Routes](#)

    -   [Define new routes](#define-new-routes)

    -   [Generate links based on the routing table](#generate-links-based-on-the-routing-table)

        -   [Generate a HTML link](#generate-a-html-link)

        -   [Generate only the attribute `href`{.docutils .literal}:](#generate-only-the-attribute-href)

        -   [Generate redirect link:](#generate-redirect-link)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

