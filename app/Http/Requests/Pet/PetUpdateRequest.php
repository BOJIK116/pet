<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PetUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        if ($this->has('name') && is_string($this->input('name'))) {
            $normalized['name'] = trim($this->input('name'));
        }

        if ($this->has('species') && is_string($this->input('species'))) {
            $normalized['species'] = mb_strtolower(
                trim($this->input('species'))
            );
        }

        if ($this->has('breed') && is_string($this->input('breed'))) {
            $normalized['breed'] = trim($this->input('breed'));
        }

        $this->merge($normalized);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'species' => [
                'sometimes',
                'required',
                'string',
                Rule::in([
                    'dog',
                    'cat',
                    'bird',
                    'rabbit',
                    'rodent',
                    'reptile',
                    'other',
                ]),
            ],

            'breed' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'sex' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in([
                    'male',
                    'female',
                    'unknown',
                ]),
            ],

            'birth_date' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],

            'weight' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0.01',
                'max:999.99',
            ],

            'chronic_conditions' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],

            'is_neutered' => [
                'sometimes',
                'nullable',
                'boolean',
            ],

            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя питомца не может быть пустым.',
            'name.min' => 'Имя должно содержать минимум 2 символа.',
            'name.max' => 'Имя не должно превышать 100 символов.',

            'species.required' => 'Вид питомца не может быть пустым.',
            'species.in' => 'Выбран недопустимый вид питомца.',

            'sex.in' => 'Выбрано недопустимое значение пола.',

            'birth_date.date_format' => 'Дата рождения должна быть в формате YYYY-MM-DD.',
            'birth_date.before_or_equal' => 'Дата рождения не может быть в будущем.',

            'weight.numeric' => 'Вес должен быть числом.',
            'weight.min' => 'Вес должен быть больше нуля.',
            'weight.max' => 'Вес не должен превышать 999.99 кг.',
        ];
    }
}