<?php

namespace LangPilot;

use Illuminate\Support\ServiceProvider;

class LangPilotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TranslationService::class, fn(): \LangPilot\TranslationService => new TranslationService());
        $this->commands([
            Console\Commands\ListMissingTrans::class,
            Console\Commands\SyncTranslations::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lang-pilot.php', 'lang-pilot');
    }
}
