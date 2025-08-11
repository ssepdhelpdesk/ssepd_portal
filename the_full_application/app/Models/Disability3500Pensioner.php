<?php

namespace App\Models;

class Disability3500Pensioner extends Rupees3500BaseModel
{
    protected $table = 'disability_Pensioner_Bene_With_percent_80AndAbove_24_03_2025';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
}