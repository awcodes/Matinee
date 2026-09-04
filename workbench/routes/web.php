<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Page;

Route::get('/', function () {
    return view('pages.show', [
        'page' => Page::query()->firstOrFail(),
    ]);
});
