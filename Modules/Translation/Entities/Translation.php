<?php

namespace Modules\Translation\Entities;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use Translatable;

    public $translatedAttributes = ['value'];
    protected $table = 'translation__translations';
    protected $fillable = ['key', 'value'];
}
