<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class AuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    // protected $stopOnFirstFailure = false;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [];
        if ($this->routeIs('auth.register')) {
            $rules += $this->registerRule();
        } elseif ($this->routeIs('auth.login')) {
            $rules += $this->loginRule();
        }
        return $rules;
    }
    /**
     * Register Rule function
     *
     * @return array
     */
    private function registerRule(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|string|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'max:255',
                Password::min(6)->letters()->mixedCase()->numbers(),
            ],
            'password_confirmation' =>
            [
                'required',
                'string',
                'same:password',
                'max:255',
                Password::min(6)->letters()->mixedCase()->numbers()
            ]
        ];
        return $rules;
    }
    /**
     * Login function
     *
     * @return array
     */
    private function loginRule(): array
    {
        return [
            'email' => 'required|email|string|max:255',
            'password' => 'required|string|max:255'
        ];
        return $rules;
    }
    /**
     * If you need to prepare or sanitize any data from the request before you apply your validation rules
    */
    protected function prepareForValidation(): void
    {
    }

    /**
     * Handle a passed validation attempt.
     * Only Work on request->all() not in request->validated()
     */
    protected function passedValidation(): void
    {
        //$this->replace(['vendor_code' => 'Taylor']);
    }

    /**
     * Get the Failed Validation Rules
    */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $this->merge(['errors' => $validator->errors()]);
    }

    /**
     * Get the "after" validation callables for the request.
     * Sometimes you need to perform additional validation
     * after your initial validation is complete. You can accomplish this using the form request's after method.
    */
    protected function after(): array
    {
        return [
            function (Validator $validator) {
                /*
                $validator->errors()->add(
                    "id",
                    "Invalid ID"
                );
                return false;
                */
            }
        ];
    }
}
