<?php

declare(strict_types=1);

namespace ZipStream\Option;

use MyCLabs\Enum\Enum;

/**
 * Class Version
 * @package ZipStream\Option
 *
 * @method static STORE(): Version
 * @method static DEFLATE(): Version
 * @method static ZIP64(): Version
 * @psalm-immutable
 * @psalm-template int
 * @extends Enum<int>
 */
class Version extends Enum
{
    public const STORE = 0x000A; // 1.00

    public const DEFLATE = 0x0014; // 2.00

    public const ZIP64 = 0x002D; // 4.50
}
