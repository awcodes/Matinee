<?php

declare(strict_types=1);

use Awcodes\Focus\Card;
use Awcodes\Focus\Enums\Size;
use Awcodes\Focus\Screenshot;
use Awcodes\Focus\ScreenshotSuite;

/*
 * Documentation screenshots for Matinée, generated with awcodes/focus from the Workbench (run `composer build`
 * first). The Workbench seeds three fixed pages with stored video payloads: a responsive YouTube embed, a fixed-size
 * Vimeo embed, and a privacy-enhanced YouTube embed with a start time.
 */

// Embeds are cross-origin player iframes. Each one is answered with a neutral poster page from
// workbench/fixtures/embed, named after the video's ID, so no real player ever loads.
$embed = function (string $url): string {
    $id = basename((string) parse_url($url, PHP_URL_PATH));
    $file = __DIR__ . "/workbench/fixtures/embed/{$id}.html";

    if (! is_file($file)) {
        throw new RuntimeException("No embed fixture for [{$url}].");
    }

    return $file;
};

// The awcodes card templates frame each screenshot at 1400x816.
$card = [1400, 816];

return ScreenshotSuite::make()
    ->fixture('https://www.youtube.com/embed/**', $embed)
    ->fixture('https://www.youtube-nocookie.com/embed/**', $embed)
    ->fixture('https://player.vimeo.com/video/**', $embed)
    ->screenshots([
        // The field is taller than the default viewport. The taller viewport keeps all of it below the sticky top bar.
        Screenshot::make('field')
            ->viewportSize(1280, 1400)
            ->visit('/admin/pages/1/edit')
            ->within('[data-focus="video-field"] iframe', fn (Screenshot $screenshot) => $screenshot
                ->waitFor('[data-focus="poster"]'))
            ->focus('[data-focus="video-field"]'),

        // The Vimeo page is stored with `responsive` false, but the field hydrates a false toggle as true, so the
        // toggle is switched off the way an editor would, which sets 640x480 pixels.
        Screenshot::make('fixed-size')
            ->viewportSize(1280, 1400)
            ->visit('/admin/pages/2/edit')
            ->click('[data-focus="video-field"] button[role="switch"]')
            ->waitFor('[data-focus="video-field"] button[role="switch"][aria-checked="false"]')
            ->within('[data-focus="video-field"] iframe', fn (Screenshot $screenshot) => $screenshot
                ->waitFor('[data-focus="poster"]'))
            ->focus('[data-focus="video-field"]'),

        // The Blade component on a front-end page: a responsive embed fills the width at the stored aspect ratio.
        Screenshot::make('embed')
            ->visit('/pages/matinee-demo')
            ->within('[data-focus="embed-page"] iframe', fn (Screenshot $screenshot) => $screenshot
                ->waitFor('[data-focus="poster"]'))
            ->focus('[data-focus="embed-page"]'),

        // The share-image source, shaped to the card templates' screenshot slots. The two-up templates show it dark
        // in slot 1 and light in slot 2, so it is captured in both themes.
        Screenshot::make('card-field')
            ->viewportSize(...$card)
            ->visit('/admin/pages/1/edit')
            ->within('[data-focus="video-field"] iframe', fn (Screenshot $screenshot) => $screenshot
                ->waitFor('[data-focus="poster"]'))
            ->scrollIntoView('[data-focus="video-field"] iframe')
            ->viewport(),
    ])
    ->cardTemplates('https://github.com/awcodes/focus-templates/tree/v2.1.0/dist')
    ->cards([
        // Open Graph and the GitHub social preview share one 2400x1260 template; GitHub crops 30px top and bottom.
        Card::make('social')
            ->template('two-up-wide')
            ->title('Matinée')
            ->screenshots(['card-field', 'card-field'])
            ->sizes([Size::OpenGraph, Size::GitHubSocial]),

        // The Filament plugin directory's 2560x1440 thumbnail.
        Card::make('thumbnail')
            ->template('two-up')
            ->title('Matinée')
            ->screenshots(['card-field', 'card-field'])
            ->sizes([Size::Filament]),

        // Unbranded 16:9 image for aw.codes, which adds its own heading: the same screenshots, no text or logo.
        Card::make('plain')
            ->template('two-up-plain')
            ->screenshots(['card-field', 'card-field'])
            ->sizes([[2560, 1440]])
            ->scale(1),
    ]);
