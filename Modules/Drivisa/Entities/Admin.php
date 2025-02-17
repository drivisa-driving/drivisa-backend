<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Admin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'users';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('onlyadmin', function (Builder $builder) {
            $builder->where('user_type', 0);
        });
    }


}
