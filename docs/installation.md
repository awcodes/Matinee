---
title: Installation
description: Install Matinée, register its styles with your theme, and prepare your model to store the field's value.
---

# Installation

## Requiring the package

Install the package via Composer:

```bash
composer require awcodes/matinee
```

The service provider is registered automatically through package discovery, so there is nothing to add to `config/app.php`.

## Registering the styles

Matinée's field is rendered from Blade views in the package, so Tailwind needs to know to scan them when it builds your CSS.

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament documentation](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

Once you have a custom theme, add the package's views to your theme's CSS file — or to your application's CSS file if you are using the standalone form builder:

```css
@source '../../../../vendor/awcodes/matinee/resources/**/*.blade.php';
```

Adjust the relative path if your CSS file does not live in the default theme location.

## Preparing your model

Matinée stores everything it captures — the original URL, the generated embed URL, the dimensions and the provider options — as JSON in a single column. Cast that column to an array or JSON on your model, or the field will not hydrate correctly:

```php
protected $casts = [
    'video' => 'array', // or 'json'
];
```

The column itself should be a `json` (or `text`) column in your migration. The attribute name you cast here is the same name you pass to `Matinee::make()`.

## Publishing views and translations

Neither step is required, but both are available if you need to customise the field.

To override the field's markup or the embed component:

```bash
php artisan vendor:publish --tag=matinee-views
```

To override or extend the field's labels — Matinée ships English, Spanish and Italian:

```bash
php artisan vendor:publish --tag=matinee-translations
```

Matinée has no configuration file; everything is configured on the field itself. See [Usage](usage.md).
