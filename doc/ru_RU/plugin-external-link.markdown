External Link Providers[¶](#external-link-providers "Ссылка на этот заголовок")

===============================================================================



This functionality allows you to link a task to additional items stored on another system.



For example, you can link a task to:



-   Traditional web page

-   Attachment (PDF documents stored on the web, archive...)

-   Any ticketing system (bug tracker, customer support ticket...)



Each item has a type, a URL, a dependency type and a title.



By default, Kanboard includes two kinds of providers:



-   Web Link: You copy and paste a link and Kanboard will fetch the page title automatically

-   Attachment: Link to anything that is not a web page



Workflow[¶](#workflow "Ссылка на этот заголовок")

-------------------------------------------------



1.  The end-user copy and paste the URL to the form and submit

2.  If the link type is “auto”, Kanboard will loop through all providers registered until there is a match

3.  Then, the link provider returns a object that implements the interface `ExternalLinkInterface`{.docutils .literal}

4.  A form is shown to the user with all pre-filled data before to save the link



Interfaces[¶](#interfaces "Ссылка на этот заголовок")

-----------------------------------------------------



To implement a new link provider from a plugin, you need to create 2 classes that implement those interfaces:



-   `Kanboard\Core\ExternalLink\ExternalLinkProviderInterface`{.docutils .literal}

-   `Kanboard\Core\ExternalLink\ExternalLinkInterface`{.docutils .literal}



### ExternalLinkProviderInterface[¶](#externallinkproviderinterface "Ссылка на этот заголовок")



  ------------------------------------------------------------------------

  Method                  Usage

  ----------------------- ------------------------------------------------

  `getName()`{.docutils   Get provider name (label)

  .literal}               



  `getType()`{.docutils   Get link type (will be saved in the database)

  .literal}               



  `getDependencies()`{.do Get a dictionary of supported dependency types

  cutils                  by the provider

  .literal}               



  `setUserTextInput($inpu Set text entered by the user

  t)`{.docutils           

  .literal}               



  `match()`{.docutils     Return true if the provider can parse correctly

  .literal}               the user input



  `getLink()`{.docutils   Get the link found with the properties

  .literal}               

  ------------------------------------------------------------------------



### ExternalLinkInterface[¶](#externallinkinterface "Ссылка на этот заголовок")



  Method                                 Usage

  -------------------------------------- ---------------------------------

  `getTitle()`{.docutils .literal}       Get link title

  `getUrl()`{.docutils .literal}         Get link URL

  `setUrl($url)`{.docutils .literal}     Set link URL



Register a new link provider[¶](#register-a-new-link-provider "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------



In your `Plugin.php`{.docutils .literal}, just call the method `register()`{.docutils .literal} from the object `ExternalLinkManager`{.docutils .literal}:



    <?php



    namespace Kanboard\Plugin\MyExternalLink;



    use Kanboard\Core\Plugin\Base;



    class Plugin extends Base

    {

        public function initialize()

        {

            $this->externalLinkManager->register(new MyLinkProvider());

        }

    }



Examples[¶](#examples "Ссылка на этот заголовок")

-------------------------------------------------



-   Kanboard includes the default providers “WebLink” and “Attachment”



### [Оглавление](index.markdown)



-   [External Link Providers](#)

    -   [Workflow](#workflow)

    -   [Interfaces](#interfaces)

        -   [ExternalLinkProviderInterface](#externallinkproviderinterface)

        -   [ExternalLinkInterface](#externallinkinterface)

    -   [Register a new link provider](#register-a-new-link-provider)

    -   [Examples](#examples)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-external-link.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-external-link.txt)

