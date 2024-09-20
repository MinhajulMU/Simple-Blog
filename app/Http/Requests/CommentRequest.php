<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CommentRequest extends FormRequest
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
        $rules = [];
        if ($this->routeIs('comments.store')) {
            $rules += $this->storeRule();
        } elseif ($this->routeIs('comments.update')) {
            $rules += $this->updateRule();
        }
        return $rules;
    }

    /**
     * store rule function
     *
     * @return array
     */
    private function storeRule(): array
    {
        return [
            'post_id' => 'required|integer|exists:posts,id',
            'content' => 'required|string',
        ];
        return $rules;
    }

    /**
     * update rule function
     *
     * @return array
     */
    private function updateRule(): array
    {
        return [
            'content' => 'required|string',
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
