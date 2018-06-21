PicoDb
======

PicoDb is a minimalist database query builder for PHP.

Features
--------

- Easy to use, easy to hack, fast and very lightweight
- Supported drivers: Sqlite, Mssql, Mysql, Postgresql
- Requires only PDO
- Use prepared statements
- Handle schema migrations
- Fully unit tested on PHP 5.3, 5.4, 5.5, 5.6 and 7.0
- License: MIT

Requirements
------------

- PHP >= 5.3
- PDO extension
- Sqlite, Mssql, Mysql or Postgresql

Author
------

Frédéric Guillot

Documentation
-------------

### Installation

```bash
composer require fguillot/picodb @stable
```

### Database connection

#### Sqlite:

```php
use PicoDb\Database;

// Sqlite driver
$db = new Database(['driver' => 'sqlite', 'filename' => ':memory:']);
```

The Sqlite driver enable foreign keys by default.

#### Microsoft SQL server:

```php
// Optional attributes:
// "schema_table" (the default table name is "schema_version")

$db = new Database([
    'driver' => 'mssql',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'my_db_name',
]);
```

Optional attributes:

- schema_table

#### Mysql:

```php
$db = new Database([
    'driver' => 'mysql',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'my_db_name',
    'ssl_key' => '/path/to/client-key.pem',
    'ssl_cert' => '/path/to/client-cert.pem',
    'ssl_ca' => '/path/to/ca-cert.pem',
]);
```

Optional attributes:

- charset
- schema_table
- port
- ssl_key
- ssl_cert
- ssl_key

#### Postgres:

```php
$db = new Database([
    'driver' => 'postgres',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'my_db_name',
]);
```

Optional attributes:

- port
- schema_table

#### Connecting from an environment variable:

Let's say you have defined an environment variable:

```bash
export DATABASE_URL=postgres://user:pass@hostname:6212/db
```

PicoDb can parse automatically this URL for you:

```php
use PicoDb\UrlParser;
use PicoDb\Database;

$db = new Database(UrlParser::getInstance()->getSettings());
```

#### Connecting from a URL

```php
use PicoDb\UrlParser;
use PicoDb\Database;

$db = new Database(UrlParser::getInstance()->getSettings('postgres://user:pass@hostname:6212/db'));
```

### Execute any SQL query

```php
$db->execute('CREATE TABLE mytable (column1 TEXT)');
```

- Returns a `PDOStatement` if successful
- Returns `false` if there is a duplicate key error
- Throws a `SQLException` for other errors

### Insertion

```php
$db->table('mytable')->save(['column1' => 'test']);
```

or

```php
$db->table('mytable')->insert(['column1' => 'test']);
```

### Fetch last inserted id

```php
$db->getLastId();
```

### Transactions

```php
$db->transaction(function ($db) {
    $db->table('mytable')->save(['column1' => 'foo']);
    $db->table('mytable')->save(['column1' => 'bar']);
});
```

- Returns `true` if the callback returns null
- Returns the callback return value otherwise
- Throws an SQLException if something is wrong

or

```php
$db->startTransaction();
// Do something...
$db->closeTransaction();

// Rollback
$db->cancelTransaction();
```

### Fetch all data

```php
$records = $db->table('mytable')->findAll();

foreach ($records as $record) {
    var_dump($record['column1']);
}
```

### Updates

```php
$db->table('mytable')->eq('id', 1)->save(['column1' => 'hey']);
```

or

```php
$db->table('mytable')->eq('id', 1)->update(['column1' => 'hey']);
```

### Remove records

```php
$db->table('mytable')->lt('column1', 10)->remove();
```

### Sorting

```php
$db->table('mytable')->asc('column1')->findAll();
```

or

```php
$db->table('mytable')->desc('column1')->findAll();
```

or

```php
$db->table('mytable')->orderBy('column1', 'ASC')->findAll();
```

Multiple sorting:

```php
$db->table('mytable')->asc('column1')->desc('column2')->findAll();
```

### Limit and offset

```php
$db->table('mytable')->limit(10)->offset(5)->findAll();
```

### Fetch only some columns

```php
$db->table('mytable')->columns('column1', 'column2')->findAll();
```

### Fetch only one column

Many rows:

```php
$db->table('mytable')->findAllByColumn('column1');
```

One row:

```php
$db->table('mytable')->findOneColumn('column1');
```

### Custom select

```php
$db->table('mytable')->select(1)->eq('id', 42)->findOne();
```

### Distinct

```php
$db->table('mytable')->distinct('columnA')->findOne();
```

### Group by

```php
$db->table('mytable')->groupBy('columnA')->findAll();
```

### Count

```php
$db->table('mytable')->count();
```

### Sum

```php
$db->table('mytable')->sum('columnB');
```

### Sum column values during update

Add the value 42 to the existing value of the column "mycolumn":

```php
$db->table('mytable')->sumColumn('mycolumn', 42)->update();
```

### Increment column

Increment a column value in a single query:

```php
$db->table('mytable')->eq('another_column', 42)->increment('my_column', 2);
```

### Decrement column

Decrement a column value in a single query:

```php
$db->table('mytable')->eq('another_column', 42)->decrement('my_column', 1);
```

### Exists

Returns true if a record exists otherwise false.

```php
$db->table('mytable')->eq('column1', 12)->exists();
```

### Left joins

```php
// SELECT * FROM mytable LEFT JOIN my_other_table AS t1 ON t1.id=mytable.foreign_key
$db->table('mytable')->left('my_other_table', 't1', 'id', 'mytable', 'foreign_key')->findAll();
```

or

```php
// SELECT * FROM mytable LEFT JOIN my_other_table ON my_other_table.id=mytable.foreign_key
$db->table('mytable')->join('my_other_table', 'id', 'foreign_key')->findAll();
```

### Equals condition

```php
$db->table('mytable')
   ->eq('column1', 'hey')
   ->findAll();
```

### IN condition

```php
$db->table('mytable')
       ->in('column1', ['hey', 'bla'])
       ->findAll();
```

### IN condition with subquery

```php
$subquery = $db->table('another_table')->columns('column2')->eq('column3', 'value3');

$db->table('mytable')
       ->columns('column_5')
       ->inSubquery('column1', $subquery)
       ->findAll();
```

### Like condition

Case-sensitive (only Mysql and Postgres):

```php
$db->table('mytable')
   ->like('column1', '%Foo%')
   ->findAll();
```

Not case-sensitive:

```php
$db->table('mytable')
   ->ilike('column1', '%foo%')
   ->findAll();
```

### Lower than condition

```php
$db->table('mytable')
   ->lt('column1', 2)
   ->findAll();
```

### Lower than or equal condition

```php
$db->table('mytable')
   ->lte('column1', 2)
   ->findAll();
```

### Greater than condition

```php
$db->table('mytable')
   ->gt('column1', 3)
   ->findAll();
```

### Greater than or equal condition

```php
$db->table('mytable')
    ->gte('column1', 3)
    ->findAll();
```

### IS NULL condition

```php
$db->table('mytable')
   ->isNull('column1')
   ->findAll();
```

### IS NOT NULL condition

```php
$db->table('mytable')
   ->notNull('column1')
   ->findAll();
```

### Multiple conditions

Add conditions are joined by a `AND`.

```php
$db->table('mytable')
    ->like('column2', '%mytable')
    ->gte('column1', 3)
    ->findAll();
```

How to make a OR condition:

```php
$db->table('mytable')
    ->beginOr()
    ->like('column2', '%mytable')
    ->gte('column1', 3)
    ->closeOr()
    ->eq('column5', 'titi')
    ->findAll();
```

### Debugging

Log generated queries:

```php
$db->getStatementHandler()->withLogging();
```

Mesure each query time:

```php
$db->getStatementHandler()->withStopWatch();
```

Get the number of queries executed:

```php
echo $db->getStatementHandler()->getNbQueries();
```

Get log messages:

```php
print_r($db->getLogMessages());
```

### Large objects (LOBs)

Insert a file:

```php
$db->largeObject('my_table')->insertFromFile('blobColumn', '/path/to/file', array('id' => 'something'));
```

Insert from a stream:

```php
$db->largeObject('my_table')->insertFromStream('blobColumn', $fd, array('id' => 'something'));
```

Fetch a large object as a stream (Postgres only):

```php
$fd = $db->largeObject('my_table')->eq('id', 'something')->findOneColumnAsStream('blobColumn');
```

Fetch a large object as a string:

```php
echo $db->largeObject('my_table')->eq('id', 'something')->findOneColumnAsString('blobColumn');
```

Drivers:

- Postgres
    - Column type: `bytea`
- Sqlite and Mysql
    - Column type: `BLOB`
    - PDO do no not supports the stream feature (returns a string instead)

### Hashtable (key/value store)

How to use a table as a key/value store:

```php
$db->execute(
     'CREATE TABLE mytable (
         column1 TEXT NOT NULL UNIQUE,
         column2 TEXT default NULL
     )'
);

$db->table('mytable')->insert(['column1' => 'option1', 'column2' => 'value1']);
```

Add/Replace some values:

```php
$db->hashtable('mytable')
   ->columnKey('column1')
   ->columnValue('column2')
   ->put(['option1' => 'new value', 'option2' => 'value2']));
```

Get all values:

```php
$result = $db->hashtable('mytable')->columnKey('column1')->columnValue('column2')->get();
print_r($result);

Array
(
    [option2] => value2
    [option1] => new value
)
```

or

```php
$result = $db->hashtable('mytable')->getAll('column1', 'column2');
```

Get a specific value:

```php
$db->hashtable('mytable')
   ->columnKey('column1')
   ->columnValue('column2')
   ->put(['option3' => 'value3']);

$result = $db->hashtable('mytable')
             ->columnKey('column1')
             ->columnValue('column2')
             ->get('option1', 'option3');

print_r($result);

Array
(
    [option1] => new value
    [option3] => value3
)
```

### Schema migrations

#### Define a migration

- Migrations are defined in simple functions inside a namespace named "Schema".
- An instance of PDO is passed to first argument of the function.
- Function names has the version number at the end.

Example:

```php
namespace Schema;

function version_1($pdo)
{
    $pdo->exec('
        CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            name TEXT UNIQUE,
            email TEXT UNIQUE,
            password TEXT
        )
    ');
}


function version_2($pdo)
{
    $pdo->exec('
        CREATE TABLE tags (
            id INTEGER PRIMARY KEY,
            name TEXT UNIQUE
        )
    ');
}
```

#### Run schema update automatically

- The method `check()` execute all migrations until the version specified
- If an error occurs, the transaction is rollbacked
- Foreign keys checks are disabled if possible during the migration

Example:

```php
$last_schema_version = 5;

$db = new PicoDb\Database(array(
    'driver' => 'sqlite',
    'filename' => '/tmp/mydb.sqlite'
));

if ($db->schema()->check($last_schema_version)) {

    // Do something...
}
else {

    die('Unable to migrate database schema.');
}
```

### Use a singleton to handle database instances

Setup a new instance:

```php
PicoDb\Database::setInstance('myinstance', function() {

    $db = new PicoDb\Database(array(
        'driver' => 'sqlite',
        'filename' => DB_FILENAME
    ));

    if ($db->schema()->check(DB_VERSION)) {
        return $db;
    }
    else {
        die('Unable to migrate database schema.');
    }
});
```

Get this instance anywhere in your code:

```php
PicoDb\Database::getInstance('myinstance')->table(...)
```
