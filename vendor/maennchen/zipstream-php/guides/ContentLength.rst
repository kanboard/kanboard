Adding Content-Length header
=============

Adding a ``Content-Length`` header for ``ZipStream`` is not trivial since the
size is not known beforehand.

The following workaround adds an approximated header:

.. code-block:: php

    class Zip
    {
        /** @var string */
        private $name;

        private $files = [];

        public function __construct($name)
        {
            $this->name = $name;
        }

        public function addFile($name, $data)
        {
            $this->files[] = ['type' => 'addFile', 'name' => $name, 'data' => $data];
        }

        public function addFileFromPath($name, $path)
        {
            $this->files[] = ['type' => 'addFileFromPath', 'name' => $name, 'path' => $path];
        }

        public function getEstimate()
        {
            $estimate = 22;
            foreach ($this->files as $file) {
            $estimate += 76 + 2 * strlen($file['name']);
            if ($file['type'] === 'addFile') {
                $estimate += strlen($file['data']);
            }
            if ($file['type'] === 'addFileFromPath') {
                $estimate += filesize($file['path']);
            }
            }
            return $estimate;
        }

        public function finish()
        {
            header('Content-Length: ' . $this->getEstimate());
            $options = new \ZipStream\Option\Archive();
            $options->setSendHttpHeaders(true);
            $options->setEnableZip64(false);
            $options->setDeflateLevel(-1);
            $zip = new \ZipStream\ZipStream($this->name, $options);

            $fileOptions = new \ZipStream\Option\File();
            $fileOptions->setMethod(\ZipStream\Option\Method::STORE());
            foreach ($this->files as $file) {
            if ($file['type'] === 'addFile') {
                $zip->addFile($file['name'], $file['data'], $fileOptions);
            }
            if ($file['type'] === 'addFileFromPath') {
                $zip->addFileFromPath($file['name'], $file['path'], $fileOptions);
            }
            }
            $zip->finish();
            exit;
        }
    }

It only works with the following constraints:

- All file content is known beforehand.
- Content Deflation is disabled

Thanks to
`partiellkorrekt <https://github.com/maennchen/ZipStream-PHP/issues/89#issuecomment-1047949274>`_
for this workaround.