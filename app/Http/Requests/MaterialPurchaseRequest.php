<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialPurchaseRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $project = $this->route('project');
        $purchase = $this->route('purchase');

        if ($project) {
            $this->merge(['project_id' => is_object($project) ? $project->getKey() : $project]);
        } elseif ($purchase) {
            $this->merge(['project_id' => $purchase->project_id]);
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
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'payment_status' => ['required', 'in:paid,unpaid,partial'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_id' => ['required', 'exists:materials,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
