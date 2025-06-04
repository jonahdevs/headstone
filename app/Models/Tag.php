<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'created_by',
        'deleted_by',

        'name',
        'slug',
        'description',
        'status',
    ];

    // relationships
    public function memorials(): BelongsToMany
    {
        return $this->belongsToMany(Memorial::class)
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    #[Scope]
    protected function published(Builder $query)
    {
        $query->where('status', 'published');
    }

    #[Scope]
    protected function draft(Builder $query)
    {
        $query->where('status', 'draft');
    }

    // events
    public static function booted(): void
    {
        static::creating(function ($material) {
            $material->status = $material->status ?: 'draft';
            $material->created_by = $material->created_by ?: auth()->id();
            $material->slug = $material->slug ?: Str::slug($material->name, '-');
        });

        static::updating(function ($material) {
            if ($material->isDirty('name')) {
                $material->slug = Str::slug($material->name, '-');
            }
        });

        static::deleting(function ($material) {
            $material->deleted_by = auth()->id();
        });
    }
}
