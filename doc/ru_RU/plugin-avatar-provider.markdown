Adding a new Avatar Provider[¶](#adding-a-new-avatar-provider "Ссылка на этот заголовок")

=========================================================================================



Registration[¶](#registration "Ссылка на этот заголовок")

---------------------------------------------------------



    $this->avatarManager->register(new CustomAvatarProvider());



Interface[¶](#interface "Ссылка на этот заголовок")

---------------------------------------------------



The provider must implements the interface `Kanboard\Core\User\Avatar\AvatarProviderInterface`{.docutils .literal}:



  ------------------------------------------------------------------------

  Method                   Description

  ------------------------ -----------------------------------------------

  `render(array $user, $si Render HTML

  ze)`{.docutils           

  .literal}                



  `isActive(array $user)`{ Returns a boolean if the provider is able to

  .docutils                render something

  .literal}                

  ------------------------------------------------------------------------



The `$user`{.docutils .literal} argument is a dictionary that contains these keys:



    [

        'id' => 123,

        'username' => 'admin',

        'name' => 'Administrator',

        'email' => 'me@localhost',

    ]



### [Оглавление](index.markdown)



-   [Adding a new Avatar Provider](#)

    -   [Registration](#registration)

    -   [Interface](#interface)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

