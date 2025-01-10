<?php

namespace LangPilot\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class Locale implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->isValidLocale($value)) {
            $fail("The {$attribute} must be a valid locale.");
        }
    }

    public function isValidLocale(string $locale): bool
    {
        return in_array($locale, [
            'af', 'ar', 'bg', 'ca', 'cs', 'cy', 'da', 'de', 'el',
            'en', 'es', 'et', 'eu', 'fa', 'fi', 'fr', 'gl', 'he',
            'hi', 'hr', 'hu', 'id', 'is', 'it', 'ja', 'km', 'ko',
            'la', 'lt', 'lv', 'mn', 'nb', 'nl', 'nn', 'pa', 'pl',
            'pt', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'th', 'tr',
            'uk', 'vi', 'zh',
        ]);
    }
}
