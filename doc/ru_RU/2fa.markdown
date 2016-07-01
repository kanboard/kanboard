Двух-уровневая аутентификация:[¶](#two-factor-authentication "Ссылка на этот заголовок")

========================================================================================



Любой пользователь может включить [двух-уровневую аутентификацию](http://en.wikipedia.org/wiki/Two_factor_authentication). После успешного входа, разовый код (6 знаков) запрашивается у пользователя для получения доступа в Канборд.



Этот код присылается в программу на вашем смартфоне.



Канборд использует [Time-based One-time Password Algorithm](http://en.wikipedia.org/wiki/Time-based_One-time_Password_Algorithm) основанный на [RFC 6238](http://tools.ietf.org/html/rfc6238).



Имеется много программ совместимых со стандартной системой TOTP. Например, вы можете использовать эти приложения, бесплатные и с открытым исходным кодом:



-   [Google Authenticator](https://github.com/google/google-authenticator/) (Android, iOS, Blackberry)

-   [FreeOTP](https://fedorahosted.org/freeotp/) (Android, iOS)

-   [OATH Toolkit](http://www.nongnu.org/oath-toolkit/) (Command line utility on Unix/Linux)



Эти системы могут работать офлайн и вам не нужно иметь мобильную связь.



Настройка[¶](#setup "Ссылка на этот заголовок")

-----------------------------------------------



1.  Перейдите в пользовательский профиль



2.  Слева нажмите **Двухфакторная авторизация** и поставте галочку в чекбоке



3.  Секретный ключ сгенерируется для вас



![2FA](https://kanboard.net/screenshots/documentation/2fa.png)



Рисунок. Двухуровневая аутентификация.



-   Вы должны сохранить секретный ключ в вашей TOTP программе. Если вы используете сматрфон, то просто сосканируйте QR код с помощью FreeOTP или Google Authenticator.



-   Каждый раз, когда вы будете входить в Канборд, будет запрашиваться новый код



-   Не забудьте протестировать ваше устройство, перед тем как закрыть вашу сессию



Новый секретный ключ генерируется каждый раз при включении/выключении этой возможности.



### [Оглавление](index.markdown)



-   [Двух-уровневая аутентификация:](#)

    -   [Настройка](#setup)



### Related Topics



-   [Documentation overview](index.markdown)



### Эта страница



-   [Исходный текст](_sources/2fa.txt)



### Быстрый поиск



Введите слова для поиска или имя модуля, класса или функции.



©2016, Kanboard.ru. | Powered by [Sphinx 1.3.3](http://sphinx-doc.org/) & [Alabaster 0.7.8](https://github.com/bitprophet/alabaster) | [Page source](_sources/2fa.txt)

