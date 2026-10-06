<?php

declare(strict_types=1);

use Awcodes\Matinee\Matinee;
use Awcodes\Matinee\Tests\Fixtures\CustomProvider;
use Awcodes\Matinee\Tests\Fixtures\TestComponent;
use Awcodes\Matinee\Tests\Fixtures\TestForm;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;

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
        ->and($field->getProvider($url)->convertUrl())->toBe('https://www.custom.com/embed/123456?');
});

it('derives the label from the field name', function () {
    $field = (new Matinee('video_url'))
        ->container(Schema::make(TestForm::make()));

    expect($field->getLabel())->toBe('Video Url');
});

it('uses a custom label when one is set', function () {
    $field = (new Matinee('video'))
        ->container(Schema::make(TestForm::make()))
        ->label('Featured video');

    expect($field->getLabel())->toBe('Featured video');
});

it('accepts a closure as a custom label', function () {
    $field = (new Matinee('video'))
        ->container(Schema::make(TestForm::make()))
        ->label(fn (): string => 'Trailer');

    expect($field->getLabel())->toBe('Trailer');
});

it('keeps a stored responsive value of false', function () {
    $component = livewire(TestComponent::class);

    $component->instance()->form->fill([
        'video' => [
            'width' => '640',
            'height' => '480',
            'responsive' => false,
            'url' => 'https://vimeo.com/76979871',
            'embed_url' => 'https://player.vimeo.com/video/76979871',
        ],
    ]);

    $component->assertSchemaStateSet([
        'video.responsive' => false,
        'video.width' => '640',
        'video.height' => '480',
    ]);
});

it('defaults responsive to true when nothing is stored', function () {
    livewire(TestComponent::class)
        ->assertSchemaStateSet([
            'video.responsive' => true,
        ]);
});

it('renders a responsive embed at full width with the stored aspect ratio', function () {
    $html = Blade::render('<x-matinee::embed :data="$data" />', ['data' => [
        'width' => '16',
        'height' => '9',
        'responsive' => true,
        'embed_url' => 'https://www.youtube.com/embed/N9qZFD1NkhI',
    ]]);

    expect($html)
        ->toContain('width="160"')
        ->toContain('height="90"')
        ->toContain('aspect-ratio:16/9; width: 100%; height: auto;');
});

it('renders a fixed-size embed at its stored pixel size', function () {
    $html = Blade::render('<x-matinee::embed :data="$data" />', ['data' => [
        'width' => '640',
        'height' => '480',
        'responsive' => false,
        'embed_url' => 'https://player.vimeo.com/video/76979871',
    ]]);

    expect($html)
        ->toContain('width="640"')
        ->toContain('height="480"')
        ->not->toContain('width: 100%');
});

it('falls back to a 16:9 ratio when no size is stored', function () {
    $html = Blade::render('<x-matinee::embed :data="$data" />', ['data' => [
        'embed_url' => 'https://www.youtube.com/embed/N9qZFD1NkhI',
    ]]);

    expect($html)
        ->toContain('width="160"')
        ->toContain('height="90"')
        ->toContain('aspect-ratio:16/9;');
});

it('defaults responsive to true when the stored video is empty', function () {
    $component = livewire(TestComponent::class);

    $component->instance()->form->fill(['video' => null]);

    $component->assertSchemaStateSet([
        'video.responsive' => true,
    ]);
});

it('saves a stored responsive value of false unchanged', function () {
    $component = livewire(TestComponent::class);

    $component->instance()->form->fill([
        'video' => [
            'width' => '640',
            'height' => '480',
            'responsive' => false,
            'url' => 'https://vimeo.com/76979871',
            'embed_url' => 'https://player.vimeo.com/video/76979871',
            'options' => [],
        ],
    ]);

    expect($component->instance()->form->getState()['video']['responsive'])->toBeFalse();
});
