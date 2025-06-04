<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Memorial extends Model
{
    /** @use HasFactory<\Database\Factories\MemorialFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'deleted_by',
        'category_id',

        'title',
        'slug',
        'sku',
        'price',
        'sale_price',
        'color',
        'dimensions',
        'weight',
        'description',
        'estimated_delivery_time',
        'stock',
        'image',
        'status',
        'published_on',
    ];

    // accessors
    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? (Str::startsWith($value, 'https') ? asset($value) : asset("storage/{$value}")) : asset('images/landscape.png'),
        );
    }

    // relationships
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Memorial::class, 'memorial_order')
            ->withPivot([
                'quantity',
                'price',
                'discount',
                'shipping_fee',
                'tax',
                'total',
                'font',
                'first_name',
                'last_name',
                'epitaph',
                'DOB',
                'DOD',
                'instructions',
                'image',
                'status',
            ])
            ->withTimestamps();
    }

    // scopes
    #[Scope]
    protected function published(Builder $query)
    {
        $query->where('status', 'published');
    }

    protected function draft(Builder $query)
    {
        $query->where('status', 'draft');
    }

    // events

    protected static function booted(): void
    {
        static::created(function (self $memorial) {
            $memorial->slug = $memorial->slug ?: Str::slug($memorial->title, '-');
            $memorial->published_on = $memorial->status === 'published' ? now() : null;
            $memorial->created_by = $memorial->created_by ?: auth()->check();
            $memorial->save();
        });

        static::updated(function (self $memorial) {
            $memorial->slug = $memorial->isDirty('slug') ?: Str::slug($memorial->title, '-');
            if ($memorial->isDirty('status') && $memorial->status === 'published') {
                $memorial->published_on = now();
            }
            $memorial->sku = $memorial->sku ?: 'MHS-' . date('Y') . '-' . $memorial->id;
            $memorial->save();
        });

        static::deleting(function (self $memorial) {
            $memorial->deleted_by = auth()->id();
        });
    }
}
