<?php

namespace PhpOffice\Math\Writer;

use PhpOffice\Math\Math;

interface WriterInterface
{
    public function write(Math $math): string;
}
