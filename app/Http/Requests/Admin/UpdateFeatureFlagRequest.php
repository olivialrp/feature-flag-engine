<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('feature_flag'));
    }

    public function rules(): array
    {
        return [
            'key' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('feature_flags')->where(fn ($query) => $query->where('environment_id', $this->route('feature_flag')->environment_id))->ignore($this->route('feature_flag')->id),
            ],
            'is_enabled' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
