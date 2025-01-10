<?php

declare(strict_types=1);

return [

    /*
     * The path to the language files.
     */
    'lang_path' => base_path('lang'),

    /*
     * The default driver to use.
     */
    'default' => \LangPilot\Drivers\GeminiTranslator::class,

    /*
     * The description of your app to assit in the translation process.
     */
    'description' => '',

    /*
     * The drivers to use.
     */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],
];
