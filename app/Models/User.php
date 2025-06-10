<?php

namespace App\Models;

use App\Jobs\ProcessNewUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, BroadcastsEvents;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'avatar',
        'phone',
        'country',
        'city',
        'address',
        'status',

        // social
        'facebook_id',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // relationships
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function latestOrder(): HasOne
    {
        return $this->hasOne(Order::class, 'customer_id')->latestOfMany();
    }

    // Local scopes
    #[Scope]
    protected function customers(Builder $query)
    {
        $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'customer'));
    }

    #[Scope]
    protected function admins(Builder $query)
    {
        $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'admin'));
    }

    #[Scope]
    protected function managers(Builder $query)
    {
        $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'manager'));
    }

    #[Scope]
    protected function operators(Builder $query)
    {
        $query->whereHas('roles', fn(Builder $query) => $query->whereIn('name', ['admin', 'manager']));
    }

    // casts
    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? (Str::startsWith($value, 'https') ? asset($value) : asset("/storage/{$value}")) : asset('images/avatar.webp'),
        );
    }



    public function broadcastOn()
    {
        return [
            new Channel('admins'),
        ];
    }


    public function broadcastAs(string $event): string|null
    {
        return match ($event) {
            'created' => 'user.created',
            'updated' => 'user.updated',
            'deleted' => 'user.deleted',
            default => null,
        };
    }

    // events
    protected static function booted(): void
    {
        static::creating(function (self $user) {
            $user->status = $user->status ?: 'active';
        });

        static::created(function (self $user) {

            ProcessNewUser::dispatch($user);
        });

        static::deleted(function (self $user) {
            $avatar = $user->avatar;
            $formattedAvatar = Str::of($avatar)->after('/storage');
            Storage::disk('public')->delete($formattedAvatar);
        });
    }
}
