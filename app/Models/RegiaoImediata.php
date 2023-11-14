<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegiaoImediata extends Model
{
    protected $table = 'regioes_imediatas';
    protected $guarded = ['_token', 'id'];
    protected static $ignoreChangedAttributes = ['created_at', 'updated_at'];

    use HasFactory;
}
