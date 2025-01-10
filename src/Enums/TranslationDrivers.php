<?php

namespace LangPilot\Enums;

use LangPilot\Contracts\TranslationDriverInterface;
use LangPilot\Drivers\GeminiTranslator;

enum TranslationDrivers: string
{
    case GEMINI = GeminiTranslator::class;

    public function getInstance(): TranslationDriverInterface
    {
        return new $this->value;
    }
}
