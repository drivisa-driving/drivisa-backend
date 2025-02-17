<?php

namespace Modules\Drivisa\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationIndicator extends Model
{
    use SoftDeletes;

    protected $table = 'drivisa__evaluation_indicators';
    protected $fillable = ['title', 'description', 'points', 'created_by'];
}
