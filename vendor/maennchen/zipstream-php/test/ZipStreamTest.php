<?php

declare(strict_types=1);

namespace ZipStreamTest;

use GuzzleHttp\Psr7\Response;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ZipArchive;
use ZipStream\File;
use ZipStream\Option\Archive as ArchiveOptions;
use ZipStream\Option\File as FileOptions;
use ZipStream\Option\Method;
use ZipStream\Stream;
use ZipStream\ZipStream;

/**
 * Test Class for the Main ZipStream CLass
 */
class ZipStreamTest extends TestCase
{
    public function testFileNotFoundException(): void
    {
        $this->expectException(\ZipStream\Exception\FileNotFoundException::class);
        // Get ZipStream Object
        $zip = new ZipStream();

        // Trigger error by adding a file which doesn't exist
        $zip->addFileFromPath('foobar.php', '/foo/bar/foobar.php');
    }

    public function testFileNotReadableException(): void
    {
        // create new virtual filesystem
        $root = vfsStream::setup('vfs');
        // create a virtual file with no permissions
        $file = vfsStream::newFile('foo.txt', 0)->at($root)->setContent('bar');
        $zip = new ZipStream();
        $this->expectException(\ZipStream\Exception\FileNotReadableException::class);
        $zip->addFileFromPath('foo.txt', $file->url());
    }

    public function testDostime(): void
    {
        // Allows testing of protected method
        $class = new ReflectionClass(File::class);
        $method = $class->getMethod('dostime');
        $method->setAccessible(true);

        $this->assertSame($method->invoke(null, 1416246368), 1165069764);

        // January 1 1980 - DOS Epoch.
        $this->assertSame($method->invoke(null, 315532800), 2162688);

        // January 1 1970 -> January 1 1980 due to minimum DOS Epoch.  @todo Throw Exception?
        $this->assertSame($method->invoke(null, 0), 2162688);
    }

    public function testAddFile(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.txt', 'test' . DIRECTORY_SEPARATOR . 'sample.txt'], $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testAddFileUtf8NameComment(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = 'árvíztűrő tükörfúrógép.txt';
        $content = 'Sample String Data';
        $comment =
            'Filename has every special characters ' .
            'from Hungarian language in lowercase. ' .
            'In uppercase: ÁÍŰŐÜÖÚÓÉ';

        $fileOptions = new FileOptions();
        $fileOptions->setComment($comment);

        $zip->addFile($name, $content, $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame([$name], $files);
        $this->assertStringEqualsFile($tmpDir . '/' . $name, $content);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);
        $this->assertSame($comment, $zipArch->getCommentName($name));
    }

    public function testAddFileUtf8NameNonUtfComment(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = 'á.txt';
        $content = 'any';
        $comment = mb_convert_encoding('á', 'ISO-8859-2', 'UTF-8');

        // @see https://libzip.org/documentation/zip_file_get_comment.html
        //
        // mb_convert_encoding hasn't CP437.
        // nearly CP850 (DOS-Latin-1)
        $guessComment = mb_convert_encoding($comment, 'UTF-8', 'CP850');

        $fileOptions = new FileOptions();
        $fileOptions->setComment($comment);

        $zip->addFile($name, $content, $fileOptions);
        $zip->finish();
        fclose($stream);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);
        $this->assertSame($guessComment, $zipArch->getCommentName($name));
        $this->assertSame($comment, $zipArch->getCommentName($name, ZipArchive::FL_ENC_RAW));
    }

    public function testAddFileNonUtf8NameUtfComment(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = mb_convert_encoding('á.txt', 'ISO-8859-2', 'UTF-8');
        $content = 'any';
        $comment = 'á';

        // @see https://libzip.org/documentation/zip_get_name.html
        //
        // mb_convert_encoding hasn't CP437.
        // nearly CP850 (DOS-Latin-1)
        $guessName = mb_convert_encoding($name, 'UTF-8', 'CP850');

        $fileOptions = new FileOptions();
        $fileOptions->setComment($comment);

        $zip->addFile($name, $content, $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);

        $this->assertNotSame([$name], $files);
        $this->assertSame([$guessName], $files);
        $this->assertStringEqualsFile($tmpDir . '/' . $guessName, $content);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);
        $this->assertSame($guessName, $zipArch->getNameIndex(0));
        $this->assertSame($name, $zipArch->getNameIndex(0, ZipArchive::FL_ENC_RAW));
        $this->assertSame($comment, $zipArch->getCommentName($guessName));
    }

    public function testAddFileWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $zip->addFile('sample.txt', 'Sample String Data', $fileOptions);
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');
        $zip->finish();
        fclose($stream);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $sample12 = $zipArch->statName('test/sample.txt');
        $this->assertSame($sample1['comp_method'], Method::STORE);
        $this->assertSame($sample12['comp_method'], Method::DEFLATE);

        $zipArch->close();
    }

    public function testAddFileFromPath(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'Sample String Data');
        fclose($streamExample);
        $zip->addFileFromPath('sample.txt', $tmpExample);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'More Simple Sample Data');
        fclose($streamExample);
        $zip->addFileFromPath('test/sample.txt', $tmpExample);

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.txt', 'test' . DIRECTORY_SEPARATOR . 'sample.txt'], $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testAddFileFromPathWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'Sample String Data');
        fclose($streamExample);
        $zip->addFileFromPath('sample.txt', $tmpExample, $fileOptions);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'More Simple Sample Data');
        fclose($streamExample);
        $zip->addFileFromPath('test/sample.txt', $tmpExample);

        $zip->finish();
        fclose($stream);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $this->assertSame(Method::STORE, $sample1['comp_method']);

        $sample2 = $zipArch->statName('test/sample.txt');
        $this->assertSame(Method::DEFLATE, $sample2['comp_method']);

        $zipArch->close();
    }

    public function testAddLargeFileFromPath(): void
    {
        $methods = [Method::DEFLATE(), Method::STORE()];
        $falseTrue = [false, true];
        foreach ($methods as $method) {
            foreach ($falseTrue as $zeroHeader) {
                foreach ($falseTrue as $zip64) {
                    if ($zeroHeader && $method->equals(Method::DEFLATE())) {
                        continue;
                    }
                    $this->addLargeFileFileFromPath($method, $zeroHeader, $zip64);
                }
            }
        }
    }

    public function testAddFileFromStream(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        // In this test we can't use temporary stream to feed data
        // because zlib.deflate filter gives empty string before PHP 7
        // it works fine with file stream
        $streamExample = fopen(__FILE__, 'rb');
        $zip->addFileFromStream('sample.txt', $streamExample);
//        fclose($streamExample);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $streamExample2 = fopen('php://temp', 'wb+');
        fwrite($streamExample2, 'More Simple Sample Data');
        rewind($streamExample2); // move the pointer back to the beginning of file.
        $zip->addFileFromStream('test/sample.txt', $streamExample2, $fileOptions);
//        fclose($streamExample2);

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.txt', 'test' . DIRECTORY_SEPARATOR . 'sample.txt'], $files);

        $this->assertStringEqualsFile(__FILE__, file_get_contents($tmpDir . '/sample.txt'));
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testAddFileFromStreamWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $streamExample = fopen('php://temp', 'wb+');
        fwrite($streamExample, 'Sample String Data');
        rewind($streamExample); // move the pointer back to the beginning of file.
        $zip->addFileFromStream('sample.txt', $streamExample, $fileOptions);
//        fclose($streamExample);

        $streamExample2 = fopen('php://temp', 'bw+');
        fwrite($streamExample2, 'More Simple Sample Data');
        rewind($streamExample2); // move the pointer back to the beginning of file.
        $zip->addFileFromStream('test/sample.txt', $streamExample2);
//        fclose($streamExample2);

        $zip->finish();
        fclose($stream);

        $zipArch = new ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $this->assertSame(Method::STORE, $sample1['comp_method']);

        $sample2 = $zipArch->statName('test/sample.txt');
        $this->assertSame(Method::DEFLATE, $sample2['comp_method']);

        $zipArch->close();
    }

    public function testAddFileFromPsr7Stream(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $body = 'Sample String Data';
        $response = new Response(200, [], $body);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $zip->addFileFromPsr7Stream('sample.json', $response->getBody(), $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.json'], $files);
        $this->assertStringEqualsFile($tmpDir . '/sample.json', $body);
    }

    public function testAddFileFromPsr7StreamWithOutputToPsr7Stream(): void
    {
        [$tmp, $resource] = $this->getTmpFileStream();
        $psr7OutputStream = new Stream($resource);

        $options = new ArchiveOptions();
        $options->setOutputStream($psr7OutputStream);

        $zip = new ZipStream(null, $options);

        $body = 'Sample String Data';
        $response = new Response(200, [], $body);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $zip->addFileFromPsr7Stream('sample.json', $response->getBody(), $fileOptions);
        $zip->finish();
        $psr7OutputStream->close();

        $tmpDir = $this->validateAndExtractZip($tmp);
        $files = $this->getRecursiveFileList($tmpDir);

        $this->assertSame(['sample.json'], $files);
        $this->assertStringEqualsFile($tmpDir . '/sample.json', $body);
    }

    public function testAddFileFromPsr7StreamWithFileSizeSet(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $body = 'Sample String Data';
        $fileSize = strlen($body);
        // Add fake padding
        $fakePadding = "\0\0\0\0\0\0";
        $response = new Response(200, [], $body . $fakePadding);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());
        $fileOptions->setSize($fileSize);
        $zip->addFileFromPsr7Stream('sample.json', $response->getBody(), $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.json'], $files);
        $this->assertStringEqualsFile($tmpDir . '/sample.json', $body);
    }

    public function testCreateArchiveWithFlushOptionSet(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setFlushOutput(true);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.txt', 'test' . DIRECTORY_SEPARATOR . 'sample.txt'], $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testCreateArchiveWithOutputBufferingOffAndFlushOptionSet(): void
    {
        // WORKAROUND (1/2): remove phpunit's output buffer in order to run test without any buffering
        ob_end_flush();
        $this->assertSame(0, ob_get_level());

        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setFlushOutput(true);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);
        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');

        // WORKAROUND (2/2): add back output buffering so that PHPUnit doesn't complain that it is missing
        ob_start();
    }

    /**
     * @return array
     */
    protected function getTmpFileStream(): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        $stream = fopen($tmp, 'wb+');

        return [$tmp, $stream];
    }

    /**
     * @param string $tmp
     * @return string
     */
    protected function validateAndExtractZip($tmp): string
    {
        $tmpDir = $this->getTmpDir();

        $zipArch = new ZipArchive();
        $res = $zipArch->open($tmp);

        if ($res !== true) {
            $this->fail("Failed to open {$tmp}. Code: $res");

            return $tmpDir;
        }

        $this->assertSame(0, $zipArch->status);
        $this->assertSame(0, $zipArch->statusSys);

        $zipArch->extractTo($tmpDir);
        $zipArch->close();

        return $tmpDir;
    }

    protected function getTmpDir(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        unlink($tmp);
        mkdir($tmp) or $this->fail('Failed to make directory');

        return $tmp;
    }

    /**
     * @param string $path
     * @return string[]
     */
    protected function getRecursiveFileList(string $path): array
    {
        $data = [];
        $path = (string)realpath($path);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $pathLen = strlen($path);
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            if (!is_dir($filePath)) {
                $data[] = substr($filePath, $pathLen + 1);
            }
        }

        sort($data);

        return $data;
    }

    protected function addLargeFileFileFromPath($method, $zeroHeader, $zip64): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setLargeFileMethod($method);
        $options->setLargeFileSize(5);
        $options->setZeroHeader($zeroHeader);
        $options->setEnableZip64($zip64);

        $zip = new ZipStream(null, $options);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        for ($i = 0; $i <= 10000; $i++) {
            fwrite($streamExample, sha1((string)$i));
            if ($i % 100 === 0) {
                fwrite($streamExample, "\n");
            }
        }
        fclose($streamExample);
        $shaExample = sha1_file($tmpExample);
        $zip->addFileFromPath('sample.txt', $tmpExample);
        unlink($tmpExample);

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertSame(['sample.txt'], $files);

        $this->assertSame(sha1_file($tmpDir . '/sample.txt'), $shaExample, "SHA-1 Mismatch Method: {$method}");
    }
}
