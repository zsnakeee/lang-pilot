<?php

namespace LangPilot;

use LangPilot\Contracts\TranslationDriverInterface;
use Illuminate\Support\Facades\File;
use LangPilot\Enums\TranslationDrivers;

class TranslationService extends TranslationBase
{
    protected string $locale;

    protected array $translations;

    protected string $langFilePath;

    protected string $langDir;

    protected TranslationDriverInterface $driver;

    public function __construct()
    {
        $this->driver = new (config('lang-pilot.default'));
    }

    public function setDriver(TranslationDrivers $driver): static
    {
        $this->driver = $driver->getInstance();
        return $this;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        $this->langDir = config('lang-pilot.lang_path');
        $this->langFilePath = "{$this->langDir}/{$locale}.json";

        if ($this->translations === []) {
            $this->translations = $this->getMissingTranslations($this->getLangKeys(), $locale);
        }

        return $this;
    }

    public function setTranslations(array $translations): static
    {
        $this->translations = $translations;

        return $this;
    }

    public function getMissingTransKeys(): array
    {
        return $this->getMissingTranslations($this->translations, $this->locale);
    }

    public function getExistingTransArray(): array
    {
        $this->getExistingTranslations($this->locale);
    }

    public function translate(): array
    {
        return $this->driver->translate($this->translations, $this->locale);
    }

    public function exportToJSON(array $translations = []): void
    {
        if ($translations === []) {
            $translations = $this->translate();
        }


        if (!File::exists($this->langDir)) {
            File::makeDirectory($this->langDir, 0755, true);
        }

        $outputTranslations = $this->getExistingTranslations($this->locale) + $translations;
        File::put($this->langFilePath, json_encode($outputTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // Dynamically call methods on the driver instance
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this->driver, $method)) {
            $this->driver->{$method}(...$arguments);
            return $this;
        }

        throw new \BadMethodCallException("Method {$method} does not exist on the driver.");
    }
}
