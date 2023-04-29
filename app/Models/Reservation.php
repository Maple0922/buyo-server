<?php

namespace App\Models;

use App\Utils\Random;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * properties:
 *
 * @property integer $id
 * @property string $name
 * @property Carbon $start
 * @property Carbon $end
 * @property string $passcode
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */

class Reservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'start',
        'end',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (!$reservation->id) {
                $id = self::generateId($reservation);
                $reservation->id = $id;
            }
        });
    }

    public static function generateId(): string
    {
        while (true) {
            $id = Random::string(5, Random::NUMBERS);
            if (!Reservation::find($id)) break;
        }
        return $id;
    }
}
