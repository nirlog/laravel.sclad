<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialWriteOffRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $project = $this->route('project');

        if ($project) {
            $this->merge(['project_id' => is_object($project) ? $project->getKey() : $project]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'material_id' => ['required', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
