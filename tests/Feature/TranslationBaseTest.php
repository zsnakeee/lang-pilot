<?php

use Illuminate\Support\Facades\File;
use LangPilot\TranslationBase;



it('retrieves language keys from files', function () {

    put_dummy_lang_keys_in_files();

    $base = new TranslationBase();
    $keys = $base->getLangKeys();

    expect($keys)
        ->toBeArray()
        ->toContain('dummy1')
        ->toContain('dummy2')
        ->toContain('dummy3');
});


it('detects structured lang file keys', function () {
    $base = new TranslationBase();

    expect($base->isPathKey('auth.failed', 'en'))
        ->toBeTrue()
        ->and($base->isPathKey('nonexistent.key', 'en'))
        ->toBeFalse();
});

it('retrieves existing translations from JSON files', function () {
    $data = [
        'Hello' => 'مرحبا',
        'Goodbye' => 'وداعا',
    ];

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode($data));

    $base = new TranslationBase();
    $translations = $base->getExistingTranslations('ar');
    expect($translations)->toBe($data);
});


it('retrieves missing translations', function () {

    File::shouldReceive('allFiles')->andReturn([]);
    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('get')->andReturn(json_encode([
        'Hello' => 'مرحبا'
    ]));

    $translations = [
        'Hello',
        'How are you?',
        "What's your name?",
    ];

    $base = new TranslationBase();
    $missing = $base->getMissingTranslations($translations, 'ar');

    expect($missing)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($missing)
        ->toContain('How are you?')
        ->toContain("What's your name?");
});
