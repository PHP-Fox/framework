<?php

declare(strict_types=1);

namespace PHPFox\Parsers;


use InvalidArgumentException;
use PHPFox\Contracts\ParserContract;

class JsonParser implements ParserContract
{
    public static function parse(string $file, string $keyToReturn): array
    {
        $contents = json_decode(file_get_contents($file), true);

        if (! isset($contents[$keyToReturn])) {
            throw new InvalidArgumentException(
                message: "Invalid key [$keyToReturn] in JSON file [$file].",
            );
        }

        return $contents[$keyToReturn];
    }
}
