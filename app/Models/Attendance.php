<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'check_in', 'check_out'];

    #[Scope]
    protected function userId(Builder $query, ?string $userId): void
    {
        if (empty($userId)) {
            return;
        }
        
        $query->where('user_id', $userId);
    }

    #[Scope]
    protected function since(Builder $query, ?string $since): void
    {
        if (empty($since)) {
            return;
        }
        
        $query->where('check_in', '>=', Carbon::parse($since)->startOfDay());
    }

    #[Scope]
    protected function until(Builder $query, ?string $until): void
    {
        if (empty($until)) {
            return;
        }
        
        $query->where('check_in', '<=', Carbon::parse($until)->endOfDay());
    }

    protected function time(): Attribute
    {
        return Attribute::make(
            fn () => $this->created_at->isoFormat('dddd, DD MMMM YYYY [pukul] HH:mm [WIB]'),
        );
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
