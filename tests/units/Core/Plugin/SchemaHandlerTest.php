<?php

namespace KanboardTests\units\Core\Plugin;

use KanboardTests\units\Base;
use Kanboard\Core\Plugin\SchemaHandler;

class SchemaHandlerTest extends Base
{
    public function testGetSchemaVersion()
    {
        $p = new SchemaHandler($this->container);
        $this->assertEquals(0, $p->getSchemaVersion('not_found'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 1));
        $this->assertEquals(1, $p->getSchemaVersion('plugin1'));

        $this->assertTrue($p->setSchemaVersion('plugin2', 33));
        $this->assertEquals(33, $p->getSchemaVersion('plugin2'));

        $this->assertTrue($p->setSchemaVersion('plugin1', 2));
        $this->assertEquals(2, $p->getSchemaVersion('plugin1'));
    }

    public function testGetSchemaFilenameForExactDriver()
    {
        $pluginName = 'SchemaHandlerTestPlugin';
        $schemaDir = $this->createPluginSchemaDirectory($pluginName);
        file_put_contents($schemaDir.'/Sqlite.php', '<?php');
        file_put_contents($schemaDir.'/Mssql.php', '<?php');

        $this->assertSame($schemaDir.'/Sqlite.php', SchemaHandler::getSchemaFilename($pluginName, 'sqlite'));
        $this->assertTrue(SchemaHandler::hasSchema($pluginName, 'sqlite'));
    }

    public function testGetSchemaFilenameForSqlServerDriverAliases()
    {
        $pluginName = 'SchemaHandlerTestPlugin';
        $schemaDir = $this->createPluginSchemaDirectory($pluginName);
        file_put_contents($schemaDir.'/Mssql.php', '<?php');

        foreach (array('dblib', 'mssql', 'odbc') as $driver) {
            $this->assertSame($schemaDir.'/Mssql.php', SchemaHandler::getSchemaFilename($pluginName, $driver));
            $this->assertTrue(SchemaHandler::hasSchema($pluginName, $driver));
        }
    }

    public function testGetSchemaFilenamePrefersExactSqlServerAliasSchema()
    {
        $pluginName = 'SchemaHandlerTestPlugin';
        $schemaDir = $this->createPluginSchemaDirectory($pluginName);
        file_put_contents($schemaDir.'/Dblib.php', '<?php');
        file_put_contents($schemaDir.'/Mssql.php', '<?php');

        $this->assertSame($schemaDir.'/Dblib.php', SchemaHandler::getSchemaFilename($pluginName, 'dblib'));
    }

    public function testGetSchemaFilenameKeepsLegacyFallbackWhenNoSchemaExists()
    {
        $pluginName = 'SchemaHandlerMissingSchemaTestPlugin';
        $schemaDir = $this->createPluginSchemaDirectory($pluginName);

        $this->assertSame($schemaDir.'/Mysql.php', SchemaHandler::getSchemaFilename($pluginName, 'mysql'));
        $this->assertFalse(SchemaHandler::hasSchema($pluginName, 'mysql'));
    }

    public function testMigrateSchemaKeepsExistingVersionAndRunsOnlyPendingMigrations()
    {
        $pluginName = 'SchemaHandlerMigrationCompatibilityTestPlugin';
        $this->createPluginSchemaDirectory($pluginName);
        file_put_contents(
            SchemaHandler::getSchemaFilename($pluginName),
            <<<'PHP'
<?php

namespace Kanboard\Plugin\SchemaHandlerMigrationCompatibilityTestPlugin\Schema;

const VERSION = 2;

function version_1($pdo)
{
    $statement = $pdo->prepare('INSERT INTO settings (option, value) VALUES (?, ?)');
    $statement->execute(array('schema_handler_migration_v1', 'ran'));
}

function version_2($pdo)
{
    $statement = $pdo->prepare('INSERT INTO settings (option, value) VALUES (?, ?)');
    $statement->execute(array('schema_handler_migration_v2', 'ran'));
}
PHP
        );

        $this->container['db']->table('settings')->eq('option', 'schema_handler_migration_v1')->remove();
        $this->container['db']->table('settings')->eq('option', 'schema_handler_migration_v2')->remove();
        $this->container['db']->table(SchemaHandler::TABLE_SCHEMA)->eq('plugin', strtolower($pluginName))->remove();

        $handler = new SchemaHandler($this->container);
        $this->assertTrue($handler->setSchemaVersion($pluginName, 1));

        $handler->loadSchema($pluginName);

        $this->assertEquals(2, $handler->getSchemaVersion($pluginName));
        $this->assertFalse($this->container['db']->table('settings')->eq('option', 'schema_handler_migration_v1')->findOneColumn('value'));
        $this->assertSame('ran', $this->container['db']->table('settings')->eq('option', 'schema_handler_migration_v2')->findOneColumn('value'));
    }

    public function testGetSchemaVersionFailureMentionsCoreMigrations()
    {
        $this->container['db']->execute('DROP TABLE '.SchemaHandler::TABLE_SCHEMA);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Run database migrations before loading plugins');

        (new SchemaHandler($this->container))->getSchemaVersion('plugin1');
    }

    protected function tearDown(): void
    {
        $this->removePluginSchemaDirectory('SchemaHandlerTestPlugin');
        $this->removePluginSchemaDirectory('SchemaHandlerMissingSchemaTestPlugin');
        $this->removePluginSchemaDirectory('SchemaHandlerMigrationCompatibilityTestPlugin');
        parent::tearDown();
    }

    private function createPluginSchemaDirectory($pluginName)
    {
        $schemaDir = PLUGINS_DIR.'/'.$pluginName.'/Schema';
        $this->removePluginSchemaDirectory($pluginName);
        mkdir($schemaDir, 0777, true);

        return $schemaDir;
    }

    private function removePluginSchemaDirectory($pluginName)
    {
        $pluginDir = PLUGINS_DIR.'/'.$pluginName;

        if (! is_dir($pluginDir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pluginDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }

        rmdir($pluginDir);
    }
}
