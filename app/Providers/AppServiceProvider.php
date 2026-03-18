<?php

namespace App\Providers;

use App\Services\DatabaseTranslationLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Replace Laravel's file-based translator with our DB-backed one.
        // DB values win over PHP lang files; PHP files remain as fallback.
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new DatabaseTranslationLoader(
                $app['files'],
                $app['path.lang']
            );
        });
    }
}
