LDAP Library[¶](#ldap-library "Ссылка на этот заголовок")

=========================================================



To facilitate LDAP integration, Kanboard has its own LDAP library. This library can perform common operations.



Client[¶](#client "Ссылка на этот заголовок")

---------------------------------------------



Class: `Kanboard\Core\Ldap\Client`{.docutils .literal}



To connect to your LDAP server easily, use this method:



    use Kanboard\Core\Ldap\Client as LdapClient;

    use Kanboard\Core\Ldap\ClientException as LdapException;



    try {

        $client = LdapClient::connect();



        // Get native LDAP resource

        $resource = $client->getConnection();



        // ...



    } catch (LdapException $e) {

        // ...

    }



LDAP Queries[¶](#ldap-queries "Ссылка на этот заголовок")

---------------------------------------------------------



Classes:



-   `Kanboard\Core\Ldap\Query`{.docutils .literal}

-   `Kanboard\Core\Ldap\Entries`{.docutils .literal}

-   `Kanboard\Core\Ldap\Entry`{.docutils .literal}



Example to query the LDAP directory:



    $query = new Query($client)

    $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('cn', 'mail'));



    if ($query->hasResult()) {

        $entries = $query->getEntries(); // Return an instance of Entries

    }



Read one entry:



    $firstEntry = $query->getEntries()->getFirstEntry();

    $email = $firstEntry->getFirstValue('mail');

    $name = $firstEntry->getFirstValue('cn', 'Default Name');



Read multiple entries:



    foreach ($query->getEntries()->getAll() as $entry) {

        $emails = $entry->getAll('mail'); // Fetch all emails

        $dn = $entry->getDn(); // Get LDAP DN of this user



        // Check if a value is present for an attribute

        if ($entry->hasValue('mail', 'user2@localhost')) {

            // ...

        }

    }



User Helper[¶](#user-helper "Ссылка на этот заголовок")

-------------------------------------------------------



Class: `Kanboard\Core\Ldap\User`{.docutils .literal}



Fetch a single user in one line:



    // Return an instance of LdapUserProvider

    $user = User::getUser($client, 'my_username');



Group Helper[¶](#group-helper "Ссылка на этот заголовок")

---------------------------------------------------------



Class: `Kanboard\Core\Ldap\Group`{.docutils .literal}



Fetch groups in one line:



    // Define LDAP filter

    $filter = '(&(objectClass=group)(sAMAccountName=My group*))';



    // Return a list of LdapGroupProvider

    $groups = Group::getGroups($client, $filter);



### [Оглавление](index.markdown)



-   [LDAP Library](#)

    -   [Client](#client)

    -   [LDAP Queries](#ldap-queries)

    -   [User Helper](#user-helper)

    -   [Group Helper](#group-helper)



 



 



 



 



 



 



[Русская документация Kanboard](http://kanboard.ru/doc/)

