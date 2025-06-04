<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'deleted_by',
        'question',
        'response',
        'status',
    ];

    // scopes
    #[Scope()]
    protected function published(Builder $query)
    {
        $query->where('status', 'published');
    }

    #[Scope]
    protected function draft(Builder $query)
    {
        $query->where('status', 'draft');
    }

    // relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // events
    protected static function booted(): void
    {
        static::creating(function (self $faq) {
            $faq->status = $faq->status ?: 'draft';
            $faq->created_by = $faq->created_by ?: auth()->id();
        });

        static::deleting(function (self $faq) {
            $faq->deleted_by = auth()->id();
        });
    }
}
