<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Tests\Fixtures\Models;

use Awcodes\Matinee\Tests\Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'video' => 'array',
    ];

    protected static function newFactory(): PageFactory
    {
        return new PageFactory;
    }
}
