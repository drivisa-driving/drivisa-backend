<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Setting\Services\ParserService;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'value'
    ];

    protected static function booted()
    {
        static::saved(function () {
            $settings = static::pluck('value', 'name')->toArray();
            ParserService::parseToEnv($settings);
            $string_settings = var_export($settings, true);
            $content = "<?php return {$string_settings};";
            Storage::put("./Config/settings.php", $content);
        });
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::slug($value, "_");
    }

    public function get($key)
    {
        return static::where('name', $key)->first()?->value;
    }
}
