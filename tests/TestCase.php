<?php

namespace Tests;

use KaueF\Structura\Providers\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ConsoleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        $config = require __DIR__.'/../config/structura.php';
        $app['config']->set('structura', $config);
    }

    /**
     * Assert that the generated file contains valid PHP code.
     */
    protected function assertValidPhp(string $path): void
    {
        $this->assertTrue(file_exists($path), "File does not exist at path: {$path}");
        
        exec('php -l ' . escapeshellarg($path) . ' 2>&1', $output, $returnVar);
        
        $this->assertEquals(
            0,
            $returnVar,
            "Failed asserting that generated file is valid PHP.\nFile: {$path}\nOutput: " . implode("\n", $output)
        );
    }
}
