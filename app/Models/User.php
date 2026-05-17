<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int         $id
 * @property string      $full_name
 * @property string      $email
 * @property string|null $phone
 * @property string      $password
 * @property string      $role
 * @property string|null $avatar
 * @property string|null $reset_code
 * @property string|null $otp_code
 * @property string|null $google_id
 * @property string|null $facebook_id
 * @property bool        $is_new_user
 * @property \Carbon\Carbon|null $reset_expire
 * @property \Carbon\Carbon|null $otp_expire
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin'          => $this->role === 'admin',
            'hotel-partner'  => $this->role === 'hotel_partner' && $this->partnerProfile?->status === 'active',
            default          => false,
        };
    }

    public function getFilamentName(): string
    {
        return $this->full_name ?? $this->email;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name', 'email', 'phone', 'password', 'role',
        'avatar', 'reset_code', 'reset_expire', 'otp_code', 'otp_expire',
        'google_id', 'facebook_id', 'is_new_user', 'email_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_new_user' => 'boolean',
            'reset_expire' => 'datetime',
            'otp_expire' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPartner(): bool
    {
        return $this->role === 'hotel_partner';
    }

    public function partnerProfile()
    {
        return $this->hasOne(HotelPartnerProfile::class);
    }

    public function managedHotel()
    {
        return $this->hasOne(Hotel::class, 'partner_user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteHotels()
    {
        return $this->belongsToMany(Hotel::class, 'favorites');
    }

    public function supportRequests()
    {
        return $this->hasMany(SupportRequest::class);
    }
}
