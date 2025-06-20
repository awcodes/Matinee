<?php

declare(strict_types=1);

use Awcodes\Matinee\Matinee;
use Awcodes\Matinee\Tests\Fixtures\CustomProvider;
use Awcodes\Matinee\Tests\Fixtures\TestComponent;
use Awcodes\Matinee\Tests\Fixtures\TestForm;
use Filament\Schemas\Schema;

use function Pest\Livewire\livewire;

it('can render the form component', function () {
    livewire(TestComponent::class)
        ->assertSchemaComponentExists('video')
        ->assertSee('matinee-component');
});

it('can force show the preview', function () {
    $field = (new Matinee('video'))
        ->container(Schema::make(TestForm::make()))
        ->showPreview();

    expect($field->shouldShowPreview())->toBeTrue();
});

it('can use custom providers', function () {
    $field = (new Matinee('video'))
        ->container(Schema::make(TestForm::make()))
        ->providers([CustomProvider::class]);

    $url = 'https://custom.com/123456';

    expect($field->getProviders())->toContain(CustomProvider::class)
        ->and($field->getProvider($url))
        ->toBeInstanceOf(CustomProvider::class)
        ->and($field->getProvider($url)->convertUrl())->toBe('https://www.custom.com/embed/custom?');
});
