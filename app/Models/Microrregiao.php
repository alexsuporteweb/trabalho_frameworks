<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Microrregiao extends Model
{
    protected $table = 'microrregioes';
    protected $guarded = ['_token', 'id'];
    protected static $ignoreChangedAttributes = ['created_at', 'updated_at'];

    use HasFactory;
}
