<?php

declare(strict_types=1);


use PHPFox\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = Application::boot(
    basePath: __DIR__ . '/../',
);

$app->run();
