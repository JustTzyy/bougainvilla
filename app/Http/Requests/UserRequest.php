<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
                $userId = $this->route(param: 'id'); // Get the user ID from the route

        return [
            'firstName' => 'required',
            'lastName' => 'required',
            'middleName' => 'nullable',
            'email' =>  "required|email|unique:users,email,$userId,id",
            'birthday' => 'required|date',
            'age' => 'required|integer',
            'sex' => 'required',
            'contactNumber' => 'required',
        ];
    }
}
