<?php

namespace LangPilot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use LangPilot\Rules\Locale;
use LangPilot\TranslationService;

class SyncTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang-pilot:translate {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate missing keys for a given locale.';

    /**
     * Execute the console command.
     */
    public function handle(TranslationService $translationService): void
    {
        $locale = $this->argument('locale');
        $validation = Validator::make(['locale' => $locale], [
            'locale' => ['required', new Locale()],
        ]);

        if ($validation->fails()) {
            $this->error($validation->errors()->first());
            return;
        }

        $translationService->setLocale($locale);
        $bar = $this->output->createProgressBar(count($translationService->getMissingTransKeys()));
        $bar->start();
        $bar->setFormat('verbose');

        $translations = $translationService
            ->translate();

        $bar->finish();

        $translationService->setTranslations($translations);
        $translationService->exportToJSON();

        $this->info("\nTranslated " . count($translations) . " keys for {$locale}.");
    }
}
