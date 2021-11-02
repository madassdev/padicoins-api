<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $fillable = ['name', 'code'];

    protected $casts = [
        "active" => 'boolean'
    ];
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
