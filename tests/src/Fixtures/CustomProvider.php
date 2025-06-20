<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Tests\Fixtures;

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
        return 'https://www.custom.com/embed/'.$this->getId().'?'.http_build_query($options);
    }
}
