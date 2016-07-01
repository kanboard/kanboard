LDAP Library
============

To facilitate LDAP integration, Kanboard has its own LDAP library. This
library can perform common operations.

Client
------

Class: ``Kanboard\Core\Ldap\Client``

To connect to your LDAP server easily, use this method:

.. code:: php

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

LDAP Queries
------------

Classes:

-  ``Kanboard\Core\Ldap\Query``
-  ``Kanboard\Core\Ldap\Entries``
-  ``Kanboard\Core\Ldap\Entry``

Example to query the LDAP directory:

.. code:: php


    $query = new Query($client)
    $query->execute('ou=People,dc=kanboard,dc=local', 'uid=my_user', array('cn', 'mail'));

    if ($query->hasResult()) {
        $entries = $query->getEntries(); // Return an instance of Entries
    }

Read one entry:

.. code:: php

    $firstEntry = $query->getEntries()->getFirstEntry();
    $email = $firstEntry->getFirstValue('mail');
    $name = $firstEntry->getFirstValue('cn', 'Default Name');

Read multiple entries:

.. code:: php

    foreach ($query->getEntries()->getAll() as $entry) {
        $emails = $entry->getAll('mail'); // Fetch all emails
        $dn = $entry->getDn(); // Get LDAP DN of this user

        // Check if a value is present for an attribute
        if ($entry->hasValue('mail', 'user2@localhost')) {
            // ...
        }
    }

User Helper
-----------

Class: ``Kanboard\Core\Ldap\User``

Fetch a single user in one line:

.. code:: php

    // Return an instance of LdapUserProvider
    $user = User::getUser($client, 'my_username');

Group Helper
------------

Class: ``Kanboard\Core\Ldap\Group``

Fetch groups in one line:

.. code:: php

    // Define LDAP filter
    $filter = '(&(objectClass=group)(sAMAccountName=My group*))';

    // Return a list of LdapGroupProvider
    $groups = Group::getGroups($client, $filter);

