<?php

namespace Awcodes\Matinee\Providers\Contracts;

interface MatineeProvider
{
    public function getDomains(): array;

    public function getOptions(): array;

    public function convertUrl(array $options = []): string;
}
