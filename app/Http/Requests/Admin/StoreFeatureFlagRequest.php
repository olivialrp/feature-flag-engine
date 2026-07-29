<?php

namespace App\Http\Requests\Admin;

use App\Models\Environment;
use App\Models\FeatureFlag;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeatureFlagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FeatureFlag::class);
    }

    public function rules(): array
    {
        return [
            'environment_id' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, Closure $fail) {
                    $environment = Environment::with('project')->find($value);
                    if (! $environment || $environment->project->tenant_id !== $this->user()->tenant_id) {
                        $fail('The selected environment is invalid or unauthorized.');
                    }
                },
            ],
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('feature_flags')->where(fn ($query) => $query->where('environment_id', $this->environment_id)),
            ],
            'is_enabled' => ['required', 'boolean'],
        ];
    }
}
