<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class DiscountUser extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'discount_id',
        'discount_amount',
        'discount_type',
        'total_discount',
        'discount_used_name',
        'type',
        'type_id',
        'main_amount',
        'after_discount'
    ];
    protected $casts = [
        'discount_amount' => 'float',
        'total_discount' => 'float',
        'main_amount' => 'float',
        'after_discount' => 'float',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }     public function trainee(){
        return $this->belongsTo(Trainee::class,'user_id');
    }
    public function discount(){
        return $this->belongsTo(Discount::class,'discount_id');
    }   public function allDiscount(){
        return $this->belongsTo(Discount::class,'discount_id')->withTrashed();
    }
}
