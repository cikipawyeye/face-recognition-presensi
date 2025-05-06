<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
