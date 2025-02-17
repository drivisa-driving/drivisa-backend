<?php

namespace Modules\User\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Entities\Sentinel\User;

class UserDevice extends Model
{
    use SoftDeletes, HasFactory;


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at', 'last_active'];


    public $fillable = [
        'user_id',
        'player_id',
        'identifier',
        'type',
        'os',
        'model',
        'language',
        'token',
        'tags',
        'banned',
        'last_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'player_id' => 'string',
        'identifier' => 'string',
        'type' => 'integer',
        'os' => 'string',
        'model' => 'string',
        'language' => 'string',
        'token' => 'string',
        'tags' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'nullable|integer',
        'player_id' => 'required|string',
        'identifier' => 'nullable',
        'type' => 'nullable',
        'os' => 'nullable',
        'model' => 'nullable',
        'language' => 'nullable',
        'token' => 'nullable',
        'tags' => 'nullable',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class);
    }

}
