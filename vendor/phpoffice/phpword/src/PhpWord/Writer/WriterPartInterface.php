<?php

namespace PhpOffice\PhpWord\Writer;

interface WriterPartInterface
{
    public function setParentWriter(AbstractWriter $parentWriter): void;

    public function write(): string;
}
