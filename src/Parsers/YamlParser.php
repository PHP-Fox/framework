<?php

declare(strict_types=1);

namespace PHPFox\Parsers;

use InvalidArgumentException;
use PHPFox\Contracts\ParserContract;
use Symfony\Component\Yaml\Yaml;

class YamlParser implements ParserContract
{
    public static function parse(string $file, string $keyToReturn): array
    {
        $contents = Yaml::parseFile(
            filename: $file,
        );

        if (! isset($contents[$keyToReturn])) {
            throw new InvalidArgumentException(
                message: "Invalid key [$keyToReturn] in YAML file [$file].",
            );
        }

        return $contents[$keyToReturn];
    }
}
