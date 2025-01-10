<?php

namespace LangPilot;

use LangPilot\Console\Commands\ListMissingTrans;
use LangPilot\Console\Commands\SyncTranslations;

use Illuminate\Support\ServiceProvider;

class LangPilotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/lang-pilot.php', 'lang-pilot');

        $this->app->singleton(TranslationService::class, fn(): TranslationService => new TranslationService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ListMissingTrans::class,
                SyncTranslations::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/lang-pilot.php' => config_path('lang-pilot.php'),
        ], 'lang-pilot-config');
    }
}
