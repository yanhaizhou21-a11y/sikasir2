<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get avatar URL with fallback
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        
        // Default avatar using UI Avatars API (optimized size)
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&background=e17f12&color=fff&size=64";
    }

    /**
     * Get user statistics
     */
    public function getStatisticsAttribute()
    {
        $stats = [
            'total_transactions' => $this->transactions()->count(),
            'total_revenue' => $this->transactions()->sum('total'),
            'today_transactions' => $this->transactions()->whereDate('created_at', today())->count(),
            'today_revenue' => $this->transactions()->whereDate('created_at', today())->sum('total'),
            'this_month_transactions' => $this->transactions()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'this_month_revenue' => $this->transactions()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];

        return $stats;
    }
}