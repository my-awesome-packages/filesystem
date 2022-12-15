<?php

namespace Awesome\Filesystem\Models;

use Awesome\Foundation\Traits\Models\AwesomeModel;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $incrementing = false;
    protected $keyType = 'uuid';
    protected $guarded = [];
}
