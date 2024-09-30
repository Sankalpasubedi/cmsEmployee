<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,ico,pdf,doc,docx,ppt,pptx,gif,bmp,webp,tiff,svg,txt,html,css,xml,json,yaml', 'max:2048'],
            'status' => ['required', 'integer', 'in:0,1,2,3'],
        ];
    }
}
