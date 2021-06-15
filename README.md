# PHP-Fox Framework

<!-- BADGES_START -->
[![Latest Version][badge-release]][packagist]
[![PHP Version][badge-php]][php]
![tests](https://github.com/PHP-Fox/framework/workflows/run-tests/badge.svg)

[badge-release]: https://img.shields.io/packagist/v/phpfox/framework.svg?style=flat-square&label=release
[badge-php]: https://img.shields.io/packagist/php-v/phpfox/framework.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/phpfox/framework.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/phpfox/framework
[php]: https://php.net
[downloads]: https://packagist.org/packages/phpfox/framework
<!-- BADGES_END -->

This is the repository for the PHP-Fox framework, a simple and lightweight PHP micro-framework designed for APIs.


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

// Routes are preloaded
// Config is preloaded
// Container definitions are preloaded

$app->run();
```
