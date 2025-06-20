<img src="https://res.cloudinary.com/aw-codes/image/upload/w_1200,f_auto,q_auto/plugins/matinee/awcodes-matinee.jpg" alt="table repeater opengraph image" width="1200" height="auto" class="filament-hidden" style="width: 100%;" />

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awcodes/matinee.svg?style=flat-square)](https://packagist.org/packages/awcodes/matinee)
[![Total Downloads](https://img.shields.io/packagist/dt/awcodes/matinee.svg?style=flat-square)](https://packagist.org/packages/awcodes/matinee)

# Matinée

OEmbed and Video field for Filament Panel and Form Builders

## Compatibility

| Package Version | Filament Version |
|-----------------|------------------|
| 1.x             | 3.x              |
| 2.x             | 4.x              |

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
