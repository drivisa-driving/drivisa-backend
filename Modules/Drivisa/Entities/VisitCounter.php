<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitCounter extends Model
{
    use HasFactory;

    protected $table = 'drivisa__visit_counter';

    protected $fillable = [
        'visit_type',
        'counter',
    ];

    /**
     * @param $type
     * @return void
     */
    public static function incrementCounter($type): void
    {
        if (in_array($type, ['page', 'ios-app', 'android-app'])) {
            self::where('visit_type', $type)->increment('counter');
        }
    }
}
