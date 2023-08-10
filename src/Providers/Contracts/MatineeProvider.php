<?php

namespace Awcodes\Matinee\Providers\Contracts;

interface MatineeProvider
{
    public function shouldShow(?string $url): bool;

    public function getOptions(): array;

    public function getAdditionalFields(): array;

    public function convertUrl(string $url, array $options = []): string;
}
