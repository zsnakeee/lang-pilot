<?php

namespace LangPilot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use LangPilot\Rules\Locale;
use LangPilot\TranslationService;

class ListMissingTrans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang-pilot:missing {locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List missing translations';

    /**
     * Execute the console command.
     */
    public function handle(TranslationService $translationService): void
    {
        $locale = $this->argument('locale');
        $error = $this->validatePrompt($locale, [
            'locale' => ['required', new Locale()],
        ]);

        if ($error) {
            $this->error($error);
            return;
        }

        $translations = $translationService->setLocale($locale)->getMissingTransKeys();
        $arr = array_map(fn($key) => [$key], $translations);
        $title = 'Missing Translations ' . Str::upper($locale) . ' (' . count($translations) . ')';
        $this->table([$title], $arr);
    }
}
