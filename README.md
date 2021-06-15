# PHP-Fox Framework

This is the repository for the PHP-Fox framework.


## Installation

You should use one of the framework templates instead of installing this package, however if you want to create your own template - please follow these instructions:


## Desired API

The code below depicts how we would like our framework API to work, providing a simple and clean developer experience

```php
<?php

declare(strict_types=1);

use PHPFox\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = Application::boot(
    basePath: __DIR__ . '/../', // root directory of project
);

$app->run();
```
