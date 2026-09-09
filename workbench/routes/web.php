<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Page;

Route::get('/', fn (): Factory | View => view('pages.show', [
    'page' => Page::query()->firstOrFail(),
]));
