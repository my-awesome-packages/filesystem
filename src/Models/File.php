<?php

namespace Awesome\Filesystem\Models;

use Awesome\Foundation\Traits\Models\AwesomeModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $guarded = [];

    public function getCloudPath(): string
    {
        $path = '/' . trim($this->path, '/');

        if (Storage::getDefaultDriver() === 'minio') {
            $path = parse_url(Storage::disk('minio')->getDriver()->publicUrl($path))['path'] ?? $path;
        }

        return $path;
    }
}
