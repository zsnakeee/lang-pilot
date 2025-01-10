<?php

namespace LangPilot;

use Illuminate\Support\Facades\File;
use Exception;

class TranslationBase
{
    protected string $langDir;

    public function __construct()
    {
        $this->langDir = config('lang-pilot.lang_path');
    }

    /**
     * Retrieve all language keys used in the application.
     */
    public function getLangKeys(): array
    {
        $files = array_merge(File::allFiles(base_path('app')), File::allFiles(base_path('resources')));


        $langKeys = [];

        foreach ($files as $file) {
            if (in_array($file->getExtension(), ['php', 'blade.php'])) {
                $content = File::get($file);

                // Match patterns for @lang('...'), __('...'), trans('...')
                preg_match_all('/(?:__\(|trans\(|@lang\()\s*(["\'])(.*?)\1/', $content, $matches);

                // Combine results and filter out empty values
                $foundKeys = array_filter(array_merge($matches[2]));
                $langKeys = array_merge($langKeys, $foundKeys);
            }
        }

        return array_values(array_unique($langKeys));
    }

    /**
     * Check if a given language key is part of a structured lang file.
     */
    public function isPathKey(string $key, string $locale): bool
    {
        $langDir = "{$this->langDir}/{$locale}";
        $langFiles = File::exists($langDir) ? File::allFiles($langDir) : [];

        if (empty($langFiles)) {
            $commonPrefixes = ['validation', 'pagination', 'passwords', 'auth'];
            foreach ($commonPrefixes as $prefix) {
                if (str_starts_with($key, "{$prefix}.")) {
                    return true;
                }
            }
        }

        foreach ($langFiles as $file) {
            $filePrefix = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            if (str_starts_with($key, "{$filePrefix}.")) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve existing translations for a given locale.
     * @throws Exception
     */
    public function getExistingTranslations(string $locale): array
    {
        $langFilePath = "{$this->langDir}/{$locale}.json";
        $currentTranslations = json_decode(File::get($langFilePath), true);
        if ($currentTranslations === null) {
            throw new Exception("Invalid JSON in file: {$locale}.json");
        }

        return File::exists($langFilePath) ? $currentTranslations : [];
    }

    /**
     * Get missing translations based on language keys and existing translations.
     */
    public function getMissingTranslations(array $keys, string $locale): array
    {
        $existingTranslations = $this->getExistingTranslations($locale);
        $missingKeys = array_filter($keys, function ($key) use ($existingTranslations, $locale): bool {
            return !isset($existingTranslations[$key]) && !$this->isPathKey($key, $locale);
        });

        return array_values($missingKeys);
    }
}
