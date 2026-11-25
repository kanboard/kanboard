Stream Output
===============

Stream to S3 Bucket
---------------

.. code-block:: php
    use Aws\S3\S3Client;
    use Aws\Credentials\CredentialProvider;
    use ZipStream\Option\Archive;
    use ZipStream\ZipStream;

    $bucket = 'your bucket name';
    $client = new S3Client([
        'region' => 'your region',
        'version' => 'latest',
        'bucketName' => $bucket,
        'credentials' => CredentialProvider::defaultProvider(),
    ]);
    $client->registerStreamWrapper();

    $zipFile = fopen("s3://$bucket/example.zip", 'w');

    $options = new Archive();
    $options->setEnableZip64(false);
    $options->setOutputStream($zipFile);

    $zip = new ZipStream(null, $options);
    $zip->addFile('file1.txt', 'File1 data');
    $zip->addFile('file2.txt', 'File2 data');
    $zip->finish();

    fclose($zipFile);