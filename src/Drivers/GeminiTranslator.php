<?php

namespace LangPilot\Drivers;

use Illuminate\Support\Facades\Http;
use LangPilot\Contracts\TranslationDriverInterface;
use LangPilot\Enums\GeminiModels;

class GeminiTranslator implements TranslationDriverInterface
{
    /**
     * @var string
     * your app description
     */
    protected string $description;

    /**
     * @var GeminiModels
     * the model to use for translation
     */
    protected $model = GeminiModels::GEMINI_15_FLASH;

    /**
     * @var string
     * the api key for the gemini
     */
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('lang-pilot.gemini.api_key');
        $this->description = config('lang-pilot.description');
    }

    public function translate(array $translations, string $locale): array
    {
        $prompt = "Translate the following list of Translations into $locale $this->description and keep this as key and translated string as value. Translations = " . json_encode($translations, JSON_UNESCAPED_UNICODE);
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model->value}:generateContent?key={$this->apiKey}";
        $response = Http::timeout(60)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ]);

        $json = $response->json();
        $text = $json['candidates'][0]['content']['parts'][0]['text'];

        return json_decode($text, true);
    }

    public function setModel(GeminiModels $model): static
    {
        $this->model = $model;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }


}
