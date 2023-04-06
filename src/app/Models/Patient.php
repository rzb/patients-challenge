<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;

class Patient extends Model implements Explored
{
    use HasFactory;
    use Searchable;

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

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'cpf' => $this->cpf,
        ];
    }

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
        ];
    }
}
