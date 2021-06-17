<?php

declare(strict_types=1);

use PHPFox\Parsers\JsonParser;
use PHPFox\Parsers\OpenApiParser;
use PHPFox\Parsers\YamlParser;

require __DIR__ . '/vendor/autoload.php';

//$paths = YamlParser::parse(
//    file: __DIR__ . '/openapi.yml',
//    keyToReturn: 'paths',
//);

$paths = OpenApiParser::parse(
    file: __DIR__ . '/openapi.yml',
);

ray($paths);
