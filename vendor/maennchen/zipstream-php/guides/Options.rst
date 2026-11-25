Available options
===============

Here is the full list of options available to you. You can also have a look at
``src/Option/Archive.php`` file.

First, an instance of ``ZipStream\Option\Archive`` needs to be created, and
after that you use setters methods to modify the values.

.. code-block:: php
    use ZipStream\ZipStream;
    use ZipStream\Option\Archive as ArchiveOptions;

    require_once 'vendor/autoload.php';

    $opt = new ArchiveOptions();

    // Define output stream (argument is of type resource)
    $opt->setOutputStream($fd);

    // Set the deflate level (default is 6; use -1 to disable it)
    $opt->setDeflateLevel(6);

    // Add a comment to the zip file
    $opt->setComment('This is a comment.');

    // Size, in bytes, of the largest file to try and load into memory (used by addFileFromPath()).  Large files may also be compressed differently; see the 'largeFileMethod' option.
    $opt->setLargeFileSize(30000000);

    // How to handle large files.  Legal values are STORE (the default), or DEFLATE. Store sends the file raw and is significantly faster, while DEFLATE compresses the file and is much, much slower. Note that deflate must compress the file twice and is extremely slow.
    $opt->setLargeFileMethod(ZipStream\Option\Method::STORE());
    $opt->setLargeFileMethod(ZipStream\Option\Method::DEFLATE());

    // Send http headers (default is false)
    $opt->setSendHttpHeaders(false);

    // HTTP Content-Disposition.  Defaults to 'attachment', where FILENAME is the specified filename. Note that this does nothing if you are not sending HTTP headers.
    $opt->setContentDisposition('attachment');

    // Set the content type (does nothing if you are not sending HTTP headers)
    $opt->setContentType('application/x-zip');

    // Set the function called for setting headers. Default is the `header()` of PHP
    $opt->setHttpHeaderCallback('header');

    // Enable streaming files with single read where general purpose bit 3 indicates local file header contain zero values in crc and size fields, these appear only after file contents in data descriptor block. Default is false. Set to true if your input stream is remote (used with addFileFromStream()).
    $opt->setZeroHeader(false);

    // Enable reading file stat for determining file size. When a 32-bit system reads file size that is over 2 GB, invalid value appears in file size due to integer overflow. Should be disabled on 32-bit systems with method addFileFromPath if any file may exceed 2 GB. In this case file will be read in blocks and correct size will be determined from content. Default is true.
    $opt->setStatFiles(true);

    // Enable zip64 extension, allowing very large archives (> 4Gb or file count > 64k)
    // default is true
    $opt->setEnableZip64(true);

    // Flush output buffer after every write
    // default is false
    $opt->setFlushOutput(true);

    // Now that everything is set you can pass the options to the ZipStream instance
    $zip = new ZipStream('example.zip', $opt);
