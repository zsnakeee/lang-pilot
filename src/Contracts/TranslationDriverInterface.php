<?php

namespace LangPilot\Contracts;

interface TranslationDriverInterface
{
    public function translate(array $translations, string $locale): array;
}
