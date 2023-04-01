<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'picture' => $this->picture,
            'name' => $this->name,
            'mothers_name' => $this->mothers_name,
            'birthdate' => $this->birthdate,
            'cpf' => $this->cpf,
            'cns' => $this->cns,
            'created_at' => $this->created_at,
        ];
    }
}
