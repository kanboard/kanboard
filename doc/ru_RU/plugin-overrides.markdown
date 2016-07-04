Plugin Overrides[¶](#plugin-overrides "Ссылка на этот заголовок")

=================================================================



Override HTTP Content Security Policy[¶](#override-http-content-security-policy "Ссылка на этот заголовок")

-----------------------------------------------------------------------------------------------------------



If you would like to replace the default HTTP Content Security Policy header, you can use the method `setContentSecurityPolicy()`{.docutils .literal}:



    <?php



    namespace Kanboard\Plugin\Csp;



    use Kanboard\Core\Plugin\Base;



    class Plugin extends Base

    {

        public function initialize()

        {

            $this->setContentSecurityPolicy(array('script-src' => 'something'));

        }

    }



Template Overrides[¶](#template-overrides "Ссылка на этот заголовок")

---------------------------------------------------------------------



Any templates defined in the core can be overridden. For example, you can redefine the default layout or change email notifications.



Example of template override:



    $this->template->setTemplateOverride('header', 'theme:layout/header');



The first argument is the original template name and the second argument the template to use as replacement.



You can still use the original template using the “kanboard:” prefix:



    <?= $this->render('kanboard:header') ?>



### [Оглавление](index.markdown)



-   [Plugin Overrides](#)

    -   [Override HTTP Content Security Policy](#override-http-content-security-policy)

    -   [Template Overrides](#template-overrides)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

