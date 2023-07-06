<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'article.title' => 'required|string',
            'article.description' => 'required|string',
            'article.body' => 'required|string',
            'article.tagList' => 'array',
            'article.tagList.*' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'article.title.required' => 'Title is required!',
            'article.title.string' => 'Title must be a string!',
            'article.description.required' => 'Description is required!',
            'article.description.string' => 'Description must be a string!',
            'article.body.required' => 'Body is required!',
            'article.body.string' => 'Body must be a string!',
            'article.tagList.array' => 'TagList must be an array!',
            'article.tagList.*.string' => 'TagList must be an array of strings!',
        ];
    }
}
