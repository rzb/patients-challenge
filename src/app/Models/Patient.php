<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'picture',
        'name',
        'mothers_name',
        'birthdate',
        'cpf',
        'cns',
    ];

    protected $casts = [
        'birthdate' => 'date:Y-m-d',
    ];

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
