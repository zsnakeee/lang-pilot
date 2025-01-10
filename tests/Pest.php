<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\File;



use LangPilot\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function put_dummy_lang_keys_in_files(): void
{
    File::shouldReceive('allFiles')
        ->andReturn([
            new SplFileInfo(base_path('app/example.php')),
            new SplFileInfo(base_path('resources/views/example.blade.php')),
        ]);
    File::shouldReceive('get')
        ->andReturn("__('dummy1') @lang('dummy2') trans('dummy3')");
}
