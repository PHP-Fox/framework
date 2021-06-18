<?php

declare(strict_types=1);

namespace PHPFox;

use Closure;
use JustSteveKing\Config\Repository;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use PHPFox\Container\Container;
use PHPFox\Exceptions\ConfigLoadingException;
use PHPFox\Router\Factory\RouterFactory;
use Throwable;

class Application
{
    private static Application $instance;

    private Repository $config;

    private Router $router;

    private function __construct(
        private string $basePath,
        private Container $container,
        private bool $booted = false,
    ) {}

    public static function boot(string $basePath): Application
    {
        if (! isset(static::$instance)) {
            static::$instance = new static(
                basePath: $basePath,
                container: Container::getInstance(),
            );
        }

        $app = static::$instance;

        // load all the things
        $app->router = RouterFactory::build();
        $app->router->setStrategy(
            strategy: new JsonStrategy(
                responseFactory: new ResponseFactory(),
            ),
        );

        $app->loadConfig();
        $app->loadRoutes();
        $app->buildContainer();

        $app->booted = true;

        return $app;
    }

    public function loadConfig(): void
    {
        $files = glob($this->basePath() . 'config/*.php');

        if (empty($files)) {
            throw new ConfigLoadingException(
                message: "Could not load config files from [$this->basePath]config/, please ensure they exist."
            );
        }

        $config = [];

        foreach ($files as $file) {
            $info = pathinfo(
                path: $file,
            );

            $config[$info['filename']] = require $file;
        }

        $this->config = Repository::build(
            items: $config,
        );
    }

    public function loadRoutes(): void
    {
        $routes = require $this->basePath . 'routes/api.php';

        array_map(function ($route) {
            $this->map(
                method: $route['method'],
                path: $route['route'],
                handler: $route['handler'],
                middleware: $route['middleware']
            );
        }, $routes);
    }

    public function map(string $method, string $path, Closure|string $handler, array $middleware = []): void
    {
        $route = $this->router->map(
            method: strtoupper($method),
            path: $path,
            handler: $handler,
        );

        if (! empty($middleware)) {
            foreach ($middleware as $callable) {
                $route->middleware(
                    middleware: $callable,
                );
            }
        }
    }

    public function buildContainer(): void
    {
        /**
         * @var Container
         */
        $container = $this->container();

        $container->bind(
            abstract: 'emitter',
            concrete: SapiEmitter::class,
        );
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function config(): Repository
    {
        return $this->config;
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    public function run(): void
    {
        $request = ServerRequestFactory::fromGlobals(
            server: $_SERVER,
            files: $_FILES,
            body: $_POST,
            cookies: $_COOKIE,
            query: $_GET,
        );

        // application middleware

        try {
            $response = $this->router->dispatch(
                request: $request,
            );
        } catch (Throwable $exception) {
            $response = new Response();
            $response->withAddedHeader(
                header: 'Content-Type',
                value: 'application/api.problem+json'
            );
            $response->getBody()->write(
                string: json_encode([
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]),
            );
            $response->withStatus(
                code: 500,
            );
        }


        /**
         * @var SapiEmitter
         */
        $emitter = $this->container()->make(
            abstract: 'emitter',
        );

        $emitter->emit(
            response: $response,
        );
    }
}
