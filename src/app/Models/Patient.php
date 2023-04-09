<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
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

    // @todo consider moving to separate Event classes if it gets busier
    protected static function booted(): void
    {
        static::updated(function (Patient $patient) {
            if ($patient->wasChanged('picture')) {
                Storage::delete($patient->getRawOriginal('picture'));
            }
        });

        static::deleted(function (Patient $patient) {
            Storage::delete($patient->picture);
        });
    }

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

    /**
     * @codeCoverageIgnore  does not run when using the database driver for scout
     */
    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
        ];
    }
}
