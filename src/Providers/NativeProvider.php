<?php

namespace Awcodes\Matinee\Providers;

use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;

class NativeProvider implements Contracts\MatineeProvider
{
    use IsMatineeProvider;

    public function getId(): ?string
    {
        return 'native';
    }

    public function getDomains(): array
    {
        return [];
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => 0,
            'loop' => 0,
            'controls' => 1,
        ];
    }

    public function convertUrl(array $options = []): string
    {
        return $this->url;
    }
}
