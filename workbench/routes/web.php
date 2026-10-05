<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Page;

$show = fn (?string $slug = null): Factory | View => view('pages.show', [
    'page' => Page::query()
        ->when($slug, fn (Builder $query) => $query->where('slug', $slug))
        ->firstOrFail(),
]);

Route::get('/', $show);
Route::get('/pages/{slug}', $show);
