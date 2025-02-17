<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Modules\Media\Image\Imagy;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentTransformer extends JsonResource
{
    const STATUS = [
        '',
        'InReview',
        'Approved',
        'Rejected'
    ];

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->imagy = app(Imagy::class);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'filename' => $this->resource->filename,
            'path' => $this->getPath(),
            'zone' => $this->resource->pivot->zone,
            'name' => ucwords(str_replace("_", " ", $this->resource->pivot->zone)),
            'status' => $this->resource->pivot->status,
            'status_text' => self::STATUS[$this->resource->pivot->status],
            'reason' => $this->resource->pivot->reason ?? null,
            'thumb' => $this->imagy->getThumbnail($this->resource->path, 'mediumThumb'),
            'updated_at' => Carbon::parse($this->resource->pivot->updated_at)->format('M d, Y h:i A')
        ];
    }

    private function getPath()
    {
        if ($this->resource->isFolder()) {
            return $this->resource->path->getRelativeUrl();
        }

        return (string)$this->resource->path;
    }
}
