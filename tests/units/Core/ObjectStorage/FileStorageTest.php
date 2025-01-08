<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\ObjectStorage\FileStorage;
use Kanboard\Core\ObjectStorage\ObjectStorageException;

class FileStorageTest extends Base
{
    private $tempDir;
    private $storage;

    protected function setUp(): void
    {
        // Create temporary directory for testing
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kanboard_test_' . uniqid();
        mkdir($this->tempDir);
        $this->storage = new FileStorage($this->tempDir);
    }

    protected function tearDown(): void
    {
        // Clean up temporary directory after tests
        $this->removeDirectory($this->tempDir);
    }

    private function removeDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function testConstructorWithInvalidDirectory()
    {
        $this->expectException(ObjectStorageException::class);
        new FileStorage('/path/that/does/not/exist');
    }

    public function testPutAndGet()
    {
        $key = 'test.txt';
        $content = 'Hello World';

        $this->storage->put($key, $content);

        $this->assertFileExists($this->tempDir . DIRECTORY_SEPARATOR . $key);
        $this->assertEquals($content, $this->storage->get($key));
    }

    public function testPutAndGetWithSubdirectory()
    {
        $key = 'subdir/test.txt';
        $content = 'Hello World';

        $this->storage->put($key, $content);

        $this->assertFileExists($this->tempDir . DIRECTORY_SEPARATOR . $key);
        $this->assertEquals($content, $this->storage->get($key));
    }

    public function testGetNonExistentFile()
    {
        $this->expectException(ObjectStorageException::class);
        $this->storage->get('nonexistent.txt');
    }

    public function testMoveFile()
    {
        $sourceFile = $this->tempDir . DIRECTORY_SEPARATOR . 'source.txt';
        $key = 'destination.txt';
        file_put_contents($sourceFile, 'Test Content');

        $result = $this->storage->moveFile($sourceFile, $key);

        $this->assertTrue($result);
        $this->assertFileDoesNotExist($sourceFile);
        $this->assertFileExists($this->tempDir . DIRECTORY_SEPARATOR . $key);
        $this->assertEquals('Test Content', file_get_contents($this->tempDir . DIRECTORY_SEPARATOR . $key));
    }

    public function testRemoveFile()
    {
        $key = 'test.txt';
        $content = 'Hello World';

        $this->storage->put($key, $content);
        $this->assertTrue($this->storage->remove($key));
        $this->assertFileDoesNotExist($this->tempDir . DIRECTORY_SEPARATOR . $key);
    }

    public function testRemoveFileWithEmptyDirectory()
    {
        $key = 'subdir/test.txt';
        $content = 'Hello World';

        $this->storage->put($key, $content);
        $this->assertTrue($this->storage->remove($key));

        // Check that both file and directory are removed
        $this->assertFileDoesNotExist($this->tempDir . DIRECTORY_SEPARATOR . $key);
        $this->assertDirectoryDoesNotExist($this->tempDir . DIRECTORY_SEPARATOR . 'subdir');
    }

    public function testOutput()
    {
        $key = 'test.txt';
        $content = 'Hello World';

        $this->storage->put($key, $content);

        ob_start();
        $this->storage->output($key);
        $output = ob_get_clean();

        $this->assertEquals($content, $output);
    }

    public function testPathTraversal()
    {
        $this->expectException(ObjectStorageException::class);
        $this->storage->get('../outside.txt');
    }
}