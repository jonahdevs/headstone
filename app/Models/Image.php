<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    protected $fillable = [
        'imageable_id',
        'imageable_type',
        'path',
        'alt',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    // accessors
    public function path(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? (Str::startsWith($value, 'https') ? asset($value) : asset('storage/' . $value)) : null,
        );
    }

    // events
    protected static function booted(): void
    {
        static::deleted(function (self $image) {
            $path = $image->path;
            $formattedPath = Str::of($path)->after('/storage');
            Storage::disk('public')->delete($formattedPath);
        });
    }
}
