---
title: Usage
description: Add the Matinée field to a form, control the preview, and render the stored video on the front end.
---

# Usage

## Adding the field

Pass the name of the JSON column you prepared in [Installation](installation.md):

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
```

The field renders as a fieldset containing a URL input, width and height inputs, a responsive toggle, and a key-value editor for the provider's options.

As the URL is typed, Matinée looks up a provider for its domain and fills in the embed URL and that provider's default options. A URL that no provider recognises fails validation with "There is no provider for this URL." — see [Providers](providers.md) for how domains are matched.

By default the label is derived from the field's name, so `Matinee::make('video')` is labelled "Video" and `Matinee::make('video_url')` is labelled "Video Url". Set your own with `label()`, which accepts a string or a closure:

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->label('Featured video')
```

Use `hiddenLabel()` to suppress the label entirely. Matinée also supports Filament's column spanning, via `columnSpan()` and `columnSpanFull()`.

## Making the field required

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->required()
```

`required()` accepts a boolean or a closure, and applies to the URL, width and height inputs together — Matinée is a composite field, so its inner inputs are what actually carry the rule.

## Showing the preview

Once a valid embed URL exists, the field offers a "Show preview" toggle that plays the video inline in the form. The preview starts collapsed. To have it open by default:

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->showPreview()
```

This sets the preview's initial state only — the toggle button is always available once a video has been resolved, and the editor can still close it. `showPreview()` accepts a boolean or a closure.

## Rendering the video

You are free to render the stored data however you like, but Matinée ships a Blade component for convenience:

```blade
<x-matinee::embed :data="$data" />
```

Pass it the field's stored value — for the example above, `$page->video`. The component renders nothing at all when the data is empty, so it is safe to use on a record that has no video.

Any extra attributes you put on the component are forwarded to the underlying `<iframe>`, which is how you add playback permissions or your own classes:

```blade
<x-matinee::embed
    :data="$page->video"
    class="rounded-lg"
    allow="fullscreen; picture-in-picture"
    allowfullscreen="true"
/>
```

When `responsive` is true, the component sizes the iframe to 100% width and derives its `aspect-ratio` from the stored width and height — so `16` and `9` mean a 16:9 ratio rather than 16 by 9 pixels. When `responsive` is false, the stored width and height are used as pixel dimensions.

### The stored data

The value Matinée writes to your column takes this shape:

```json
{
    "width": "16",
    "height": "9",
    "responsive": true,
    "url": "https:\/\/www.youtube.com\/watch?v=N9qZFD1NkhI",
    "embed_url": "https:\/\/www.youtube.com\/embed\/N9qZFD1NkhI?controls=1&start=0",
    "options": {
        "controls": "1",
        "nocookie": "0",
        "start": "00:00:00"
    }
}
```

`url` is what the editor pasted and `embed_url` is what the provider generated from it — the embed URL is regenerated whenever the URL or the options change, so you should read `embed_url` rather than building one yourself.

> [!NOTE]
> New fields default to a width of `16`, a height of `9`, and `responsive` set to true.
