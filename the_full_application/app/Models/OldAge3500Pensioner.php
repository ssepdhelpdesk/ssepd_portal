<?php

namespace App\Models;

class OldAge3500Pensioner extends Rupees3500BaseModel
{
    protected $table = 'oldAge_Pensioner_Beneficiaries_WithAge_80AndAbove_04_03_2025';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}