<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class SaveUserRequest
 *
 * @property \App\Models\User|null $user
 * @method string input()
 * @method array only()
 */
class SaveUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user?->id,
            'password' => ['nullable','string','min:8','confirmed', Rule::requiredIf(!$this->user?->id)],
        ];
    }
}
