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



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/plugin-helpers.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/plugin-helpers.txt)

