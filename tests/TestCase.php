<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a fake Vite manifest for testing
        if (!file_exists(public_path('build'))) {
            mkdir(public_path('build'), 0755, true);
        }
        
        if (!file_exists(public_path('build/manifest.json'))) {
            file_put_contents(public_path('build/manifest.json'), json_encode([
                'resources/css/app.css' => [
                    'file' => 'assets/app.css',
                    'src' => 'resources/css/app.css',
                    'isEntry' => true
                ],
                'resources/js/app.js' => [
                    'file' => 'assets/app.js',
                    'src' => 'resources/js/app.js',
                    'isEntry' => true
                ]
            ]));
        }
    }
}
