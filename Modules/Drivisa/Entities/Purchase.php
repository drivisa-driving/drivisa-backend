<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    const TYPE = [
        'lesson' => 1,
        'BDE' => 2,
        'Package' => 3,
        'g2_test' => 4,
        'g_test' => 5,
        'reschedule' => 6,
        'Bonus' => 7,
        'Bonus_BDE' => 8,
    ];

    protected $table = 'drivisa__purchases';

    protected $fillable = [
        'purchaseable_id',
        'purchaseable_type',
        'transaction_id',
        'type',
        'trainee_id'
    ];

    public function purchaseable()
    {
        return $this->morphTo();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
