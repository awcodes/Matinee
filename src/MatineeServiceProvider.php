<?php

declare(strict_types=1);

namespace Awcodes\Matinee;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MatineeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'matinee';

    public static string $viewNamespace = 'matinee';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }
}
