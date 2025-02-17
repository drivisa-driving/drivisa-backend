<?php

namespace Modules\Media\Entities;

use Illuminate\Database\Eloquent\Model;

class FileTranslation extends Model
{
    const STATUS = [
        'pending' => 1,
        'approve' => 2,
        'reject' => 3
    ];
    public $timestamps = false;
    protected $fillable = ['description', 'alt_attribute', 'keywords', 'status'];
    protected $table = 'media__file_translations';
}
