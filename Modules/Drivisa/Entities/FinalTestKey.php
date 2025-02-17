<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;

class FinalTestKey extends Model
{
    protected $table = 'drivisa__final_test_keys';
    protected $fillable = [
        'title',
        'parent_id'
    ];    

    public function finalTestResult()
    {
        return $this->hasMany(FinalTestResult::class);
    }
    
}
