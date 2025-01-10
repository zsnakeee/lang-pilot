<?php

namespace LangPilot\Enums;

enum GeminiModels: string
{
    case GEMINI_20_FLASH = 'gemini-2.0-flash-exp';
    case GEMINI_15_PRO = 'gemini-1.5-pro';
    case GEMINI_15_FLASH = 'gemini-1.5-flash';
}
