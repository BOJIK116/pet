<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'sex' => $this->sex,

            'birth_date' => $this->birth_date?->toDateString(),

            'weight' => $this->weight !== null
                ? (float) $this->weight
                : null,

            'chronic_conditions' => $this->chronic_conditions,
            'is_neutered' => $this->is_neutered,
            'notes' => $this->notes,

            'photo_url' => $this->photo_path
                ? Storage::disk('public')->url($this->photo_path)
                : null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}