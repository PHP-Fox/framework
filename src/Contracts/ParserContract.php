<?php

declare(strict_types=1);

namespace PHPFox\Contracts;

interface ParserContract
{
    public static function parse(string $file, string $keyToReturn): array;
}
