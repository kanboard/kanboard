Plugin: Agregar trasporte de email
==================================

Por default Kanboard soporta 3 estadares de trasporte de email:

- Mail (PHP mail function)
- Smtp
- Sendmail command

Con la API del plugin tu puedes agregar un driver para cualquier proveedor de email.
Por ejemplo, nuestro plugin puede agregar un trasporte de email para un proveedor que usa un API HTTP.

Implementación
--------------

Nuestro plugin dede implementgar la interface `Kanboard\Core\Mail\ClientInterface` y extiende desde `Kanboard\Core\Base`.
El único método que necesita para implementar es `sendEmail()`:

```php
interface ClientInterface
{
    /**
     * Send a HTML email
     *
     * @access public
     * @param  string  $email
     * @param  string  $name
     * @param  string  $subject
     * @param  string  $html
     * @param  string  $author
     */
    public function sendEmail($email, $name, $subject, $html, $author);
}
```

Para registrar el nuevo trasporte de email, usa el metodo `setTransport($transport, $class)` desde la clase `Kanboard\Core\Mail\Client`:

```php
$this->emailClient->setTransport('myprovider', '\Kanboard\Plugin\MyProvider\MyEmailHandler');
```

EL segundo argumento contiene el absoluto namespace de tu clase especifica

Ejemplos de plugins para trasporte de email
----------------------------------

- [Sendgrid](https://github.com/kanboard/plugin-sendgrid)
- [Mailgun](https://github.com/kanboard/plugin-mailgun)
- [Postmark](https://github.com/kanboard/plugin-postmark)
