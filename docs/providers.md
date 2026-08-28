---
title: Providers
description: How Matinée matches a URL to a provider, and how to write and register your own.
---

# Providers

A provider is the piece that knows how to turn a page URL for a video service into an embeddable URL, and which options that service accepts.

## Built-in providers

Matinée registers two providers on every field.

**YouTube** handles `youtube.com` and `youtu.be`, including `/shorts/` URLs, and offers these options:

| Option | Default | Effect |
|---|---|---|
| `controls` | `1` | Whether the player chrome is shown. |
| `nocookie` | `0` | When enabled, embeds from `youtube-nocookie.com` instead of `youtube.com`. |
| `start` | `00:00:00` | Where playback begins. Written as `HH:MM:SS` and converted to seconds in the embed URL. |

**Vimeo** handles `vimeo.com` and offers `autoplay`, `loop`, `show_title`, `byline` and `portrait`, all defaulting to `0`.

Options appear in the field as a key-value editor, pre-filled with the provider's defaults as soon as a URL is recognised. Changing them regenerates the embed URL.

## How a URL is matched

Matinée parses the host out of the pasted URL and looks it up against the domains each provider declares. The match is on the registrable domain, so `https://www.youtube.com/watch?v=…` and `https://youtube.com/watch?v=…` both resolve to the YouTube provider.

If no provider claims the host, the field fails validation rather than storing an unusable embed URL.

## Writing a custom provider

A provider implements the `MatineeProvider` contract and uses the `IsMatineeProvider` trait. The contract requires three methods; the trait supplies the constructor helpers and holds the URL being converted in `$this->url`.

```php
use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;
use Awcodes\Matinee\Providers\Contracts\MatineeProvider;
use Illuminate\Support\Str;

class CustomProvider implements MatineeProvider
{
    use IsMatineeProvider;

    public function getDomains(): array
    {
        return [
            'custom.com',
        ];
    }

    public function getOptions(): array
    {
        return [
            'controls' => 1,
            'title' => 0,
        ];
    }

    public function convertUrl(?array $options = []): string
    {
        $id = Str::of($this->url)->after('custom.com/');

        return 'https://www.custom.com/embed/' . $id . '?' . http_build_query($options);
    }
}
```

`getDomains()` must return at least one domain. Providers are matched by the URL's host, so a provider that declares no domains can never be selected.

`convertUrl()` receives the current options from the field and returns the URL that will be stored as `embed_url`. It is called every time the URL or the options change, so it should be a pure transformation of `$this->url` and the options it is given.

> [!WARNING]
> The built-in providers are registered after your own, so a custom provider that claims `youtube.com` or `vimeo.com` will be overridden rather than take precedence. Custom providers should declare domains the built-ins do not handle.

## Registering a provider

Pass it to the field:

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->providers([CustomProvider::class])
```

Or register it for every Matinée field at once, from the `register()` method of a service provider:

```php
use Awcodes\Matinee\Matinee;

public function register(): void
{
    Matinee::configureUsing(function (Matinee $matinee): void {
        $matinee->providers([CustomProvider::class]);
    });
}
```

`providers()` takes class names, not instances, and replaces any list set previously — the built-in YouTube and Vimeo providers are always added on top of whatever you pass.
