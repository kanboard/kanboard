Registering new helpers[¶](#registering-new-helpers "Ссылка на этот заголовок")

===============================================================================



Helper skeleton:



    <?php



    namespace Kanboard\Plugin\MyPlugin\Helper\MyHelper;



    use Kanboard\Core\Base;



    class MyHelper extends Base

    {

        public function doSomething()

        {

            return 'foobar';

        }

    }



Register your helper class:



    $this->helper->register('myHelper', '\Kanboard\Plugin\MyPlugin\Helper\MyHelper');



Using your helper from a template:



    <p>

        <?= $this->myHelper->doSomething() ?>

    </p>



Using your helper from another class:



    $this->helper->myHelper->doSomething();



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

