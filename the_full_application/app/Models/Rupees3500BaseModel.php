<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Rupees3500BaseModel extends Model
{
    protected $connection = 'rupees_3500';
    protected $guarded = [''];
}
