<?php

namespace baklysystems\smsmisr\models;

use Illuminate\Database\Eloquent\Model;

class Systemconf extends Model
{
    protected $table = 'setting';
    protected $fillable = [
      'mobile', 'smsCredit'
    ];
}
