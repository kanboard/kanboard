<?php

declare(strict_types=1);

namespace ZipStream\Option;

use MyCLabs\Enum\Enum;

/**
 * Methods enum
 *
 * @method static STORE(): Method
 * @method static DEFLATE(): Method
 * @psalm-immutable
 * @psalm-template int
 * @extends Enum<int>
 */
class Method extends Enum
{
    public const STORE = 0x00;

    public const DEFLATE = 0x08;
}
