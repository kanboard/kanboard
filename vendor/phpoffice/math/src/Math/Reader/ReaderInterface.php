<?php

namespace PhpOffice\Math\Reader;

use PhpOffice\Math\Math;

interface ReaderInterface
{
    public function read(string $content): ?Math;
}
