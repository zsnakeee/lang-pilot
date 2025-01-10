<?php

use Illuminate\Support\Facades\File;
use LangPilot\TranslationService;

it('exports translations', function () {
    $t = [
        'Hello' => 'مرحبا',
        'Welcome' => 'أهلا بك',
    ];

    $temp_dir = base_path('tests/lang');
    config(['lang-pilot.lang_path' => $temp_dir]);
    File::ensureDirectoryExists($temp_dir);

    $service = new TranslationService();
    $service
        ->setLocale('ar')
        ->exportToJSON($t);

    expect(File::exists("$temp_dir/ar.json"))
        ->toBeTrue()
        ->and(File::get("$temp_dir/ar.json"))
        ->toContain('"Hello": "مرحبا"')
        ->toContain('"Welcome": "أهلا بك"');

    File::deleteDirectory($temp_dir);
});


it('translates keys using default driver', function () {
    $service = new TranslationService();
    $translations = $service
        ->setLocale('ar')
        ->setTranslations([
            'Hello World',
            'Goodbye',
        ])
        ->translate();

    expect($translations)
        ->toHaveCount(2)
        ->and($translations)
        ->toContain('مرحبا بالعالم')
        ->toContain('وداعا');
});


it('translate app keys', function () {

    File::shouldReceive('allFiles')
        ->andReturn([
            new SplFileInfo(base_path('app/example.php')),
            new SplFileInfo(base_path('resources/views/example.blade.php')),
        ]);


    File::shouldReceive('getFilenameWithoutExtension')
        ->andReturn('example');

    File::shouldReceive('get')
        ->andReturnUsing(function ($file) {
            if ($file == base_path('app/example.php')) {
                return "__('Hello World')";
            }


            if ($file == base_path('resources/views/example.blade.php')) {
                return "@lang('Goodbye') __('Register')";
            }

            if ($file == config('lang-pilot.lang_path') . '/ar.json') {
                return json_encode([
                    'Hello World' => 'مرحبا بالعالم',
                ], JSON_UNESCAPED_UNICODE);
            }
        });

    File::shouldReceive('exists')
        ->andReturn(true);

    File::shouldReceive('put')
        ->withArgs(function ($path, $content) {
            // Optionally, you can assert on the arguments here
            expect($path)
                ->toContain('lang/ar.json')
                ->and($content)
                ->toContain('مرحبا بالعالم')
                ->toContain('سجل')
                ->toContain('وداعا');
            return true; // Ensure arguments match your expectations
        })
        ->andReturnTrue();


    $service = new TranslationService();
    $service
        ->setLocale('ar')
        ->exportToJSON();
});




