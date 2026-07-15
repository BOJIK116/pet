<?php

namespace App\Http\Requests\Pet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name'))
                ? trim($this->input('name'))
                : $this->input('name'),

            'species' => is_string($this->input('species'))
                ? mb_strtolower(trim($this->input('species')))
                : $this->input('species'),

            'breed' => is_string($this->input('breed'))
                ? trim($this->input('breed'))
                : $this->input('breed'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'species' => [
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
                'nullable',
                'string',
                'max:100',
            ],

            'sex' => [
                'nullable',
                'string',
                Rule::in([
                    'male',
                    'female',
                    'unknown',
                ]),
            ],

            'birth_date' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],

            'weight' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:999.99',
            ],

            'chronic_conditions' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'is_neutered' => [
                'nullable',
                'boolean',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Укажите имя питомца.',
            'name.min' => 'Имя должно содержать минимум 2 символа.',
            'name.max' => 'Имя не должно превышать 100 символов.',

            'species.required' => 'Укажите вид питомца.',
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