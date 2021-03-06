<?php

declare(strict_types=1);

namespace PHPFox\Console\Commands\Openapi;

use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Minicli\Command\CommandController;
use Minicli\Output\OutputHandler;
use PHPFox\Parsers\OpenApiParser;
use RuntimeException;
use Symfony\Component\VarExporter\VarExporter;
use Throwable;

class DefaultController extends CommandController
{
    protected null|string $file = null;

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $printer = $this->getPrinter();

        $printer->info(
            content: "Starting to process openapi file...",
        );

        $this->setFile();

        if (! file_exists($this->file)) {
            throw new InvalidArgumentException(
                message: "File does not exist [$this->file], please create it first."
            );
        }

        $routes = OpenApiParser::parse(
            file: $this->file,
        );

        $this->syncRoutes(
            routes: $routes,
            printer: $printer,
        );
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     * @throws \Symfony\Component\VarExporter\Exception\ExceptionInterface
     */
    protected function syncRoutes(array $routes, OutputHandler $printer): void
    {
        $total = count($routes);
        $printer->info(
            content: "Processing [$total] routes",
        );

        // Filesystem
        $filesystem = new Filesystem(
            adapter: new LocalFilesystemAdapter(
                location: BASE_PATH,
            ),
        );

        // Check if routes file exists
        try {
            $exists = $filesystem->fileExists(
                location: '/routes/api.php',
            );
        } catch(Throwable $exception) {
            throw new RuntimeException(
                message: "Could not find file routes file.",
                code: 0,
                previous: $exception,
            );
        }

        if (! $exists) {
            // Create routes file
            $filesystem->write(
                location: '/routes/api.php',
                contents: ""
            );

            $exists = $filesystem->fileExists(
                location: '/routes/api.php',
            );
        }

        $currentRoutes = require BASE_PATH . '/routes/api.php';

        $routes = array_merge($routes, $currentRoutes);

        // write routes to file.
        $stub = $filesystem->read(
            location: '/stubs/api.stub',
        );

        $contents = strtr($stub, ['DummyArray' => VarExporter::export($routes)]);

        /**
         * @todo Overwrite file contents instead of prepending.
         */
        $filesystem->write(
            location: '/routes/api.php',
            contents: $contents,
        );
    }

    protected function setFile(): void
    {
        if ($this->hasParam(param: 'file')) {
            $this->file = BASE_PATH . $this->getParam(param: 'file');
        }

        if (is_null($this->file)) {
            $this->file = BASE_PATH . '/openapi.yml';
        }
    }
}
