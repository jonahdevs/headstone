<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'slug', 'description', 'status'];

    // relationships
    public function memorials(): HasMany
    {
        return $this->hasMany(Memorial::class);
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
    protected static function booted()
    {
        static::creating(function (self $category) {
            $category->slug = $category->slug ?: Str::slug($category->name, '-');
            $category->status = $category->status ?: 'draft';
            $category->created_by = $category->created_by ?: auth()->id();
        });

        static::updating(function (self $category) {
            $category->slug = !$category->isDirty('name') ?: Str::slug($category->name, '-');
        });
    }
}
