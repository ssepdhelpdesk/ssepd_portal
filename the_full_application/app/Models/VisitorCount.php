<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorCount extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'visit_date', 'visit_time'];
}
