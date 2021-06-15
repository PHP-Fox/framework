<?php

use JustSteveKing\Config\Repository;
use PHPFox\Application;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

beforeEach(
    closure: function () {
        $this->app = Application::boot(
            basePath: __DIR__ . '/../',
        );
    },
);

it('can boot the application', function () {
    assertInstanceOf(
        expected: Application::class,
        actual: $this->app,
    );
});

it('can return the base path of our project', function () {
    assertEquals(
        expected: __DIR__ . '/../',
        actual: $this->app->basePath(),
    );
});

it('can only create one instance', function () {
    assertSame(
        expected: $this->app,
        actual: Application::boot(basePath: __DIR__ . '/../'),
    );
});

it('will always be booted once the boot() method is ran', function () {
    assertTrue(
        condition: $this->app->isBooted(),
    );
});

it('can load our initial configuration', function () {
    assertInstanceOf(
        expected: Repository::class,
        actual: $this->app->config(),
    );

    assertEquals(
        expected: 'PHP Fox',
        actual: $this->app->config()->get(key: 'app.name'),
    );
});
