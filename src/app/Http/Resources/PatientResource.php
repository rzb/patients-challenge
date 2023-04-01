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
            'birthdate' => $this->birthdate->format('Y-m-d'),
            'cpf' => $this->cpf(),
            'cns' => $this->cns(),
            'created_at' => $this->created_at,
            'address' => new AddressResource($this->whenLoaded('address')),
        ];
    }

    protected function cpf(): string
    {
        return
            substr($this->cpf, 0, 3) . '.' .
            substr($this->cpf, 3, 3) . '.' .
            substr($this->cpf, 6, 3) . '-' .
            substr($this->cpf, 9, 2);
    }

    protected function cns(): string
    {
        return
            substr($this->cns, 0, 3) . ' ' .
            substr($this->cns, 3, 4) . ' ' .
            substr($this->cns, 7, 4) . ' ' .
            substr($this->cns, 11, 4);
    }
}
