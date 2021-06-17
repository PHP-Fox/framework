<?php

declare(strict_types=1);

namespace PHPFox\Parsers;

class OpenApiParser
{
    public static array $verbs = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'head', 'trace',
    ];

    public static function parse(string $file): array
    {
        $paths = static::getPaths(
            file: $file,
        );

        $routes = [];

        foreach ($paths as $key => $path) {
            foreach ($path as $verb => $item) {
                if (in_array($verb, static::$verbs)) {
                    $routes[] = [
                        'method' => $verb,
                        'route' => $key,
                        'name' => $item['operationId'] ?? null,
                    ];
                }
            }
        }

        return $routes;
    }

    public static function getPaths(string $file): array
    {
        $info = pathinfo($file);

        return match ($info['extension']) {
            'json' => JsonParser::parse(
                file: $file,
                keyToReturn: 'paths',
            ),
            'yml', 'yaml' => YamlParser::parse(
                file: $file,
                keyToReturn: 'paths',
            ),
        };
    }
}
