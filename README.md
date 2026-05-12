# Matinée

OEmbed and Video field for Filament Panel and Form Builders.

[![Latest Version](https://img.shields.io/github/release/awcodes/matinee.svg?style=flat-square&color=blue&label=Release)](https://github.com/awcodes/matinee/releases)
[![MIT Licensed](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/matinee.svg?style=flat-square&color=blue&label=Downloads)](https://packagist.org/packages/awcodes/matinee)
[![GitHub Repo stars](https://img.shields.io/github/stars/awcodes/matinee?style=flat-square&color=blue&label=Stars)](https://github.com/awcodes/matinee/stargazers)

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| 1.x             | 3.x              |
| 2.x             | 4.x              |
| 3.x             | 5.x              |

<!-- [docs_start] -->

## Installation

You can install the package via composer:

```bash
composer require awcodes/matinee
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme add the plugin's views to your theme css file or your app's css file if using the standalone packages.

```css
@source '../../../../vendor/awcodes/matinee/resources/**/*.blade.php';
```

## Preparing your model

Matinée stores its content as JSON data in a single column on your model. So, it is vital that you cast the column to an array or json object in your model.

```php
protected $casts = [
    'video' => 'array', // or 'json'
];
```

## Usage

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
```

## Forcing the preview to show

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->showPreview()
```

## Custom Providers

Matinée comes with a Provider for YouTube and Vimeo, but you can add your own by creating a class and passing it into the `providers` modifier on the field.

```php
use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;
use Awcodes\Matinee\Providers\Contracts\MatineeProvider;

class CustomProvider implements MatineeProvider
{
    use IsMatineeProvider;

    public function getId(): ?string
    {
        return 'custom';
    }

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
        return 'https://www.custom.com/embed/' . $this->getId() . '?' . http_build_query($options);
    }
}
```

Then you can use it by passing it into the `providers` modifier on the field instance or globally in the `register` method of a service provider with the `configureUsing()` method.

```php
use Awcodes\Matinee\Matinee;

Matinee::make('video')
    ->providers([CustomProvider::class])
```

## Rendering the video

You are free to render the video in any way you see fit, but Matinée comes with a blade component you can use for convenience.

```blade
<x-matinee::embed :data="$data" />
```

The stored data will take the following shape:

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

<!-- [docs_end] -->

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Adam Weston](https://github.com/awcodes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
