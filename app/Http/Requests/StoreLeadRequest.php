<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'ulid', 'exists:companies,id'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'source_id' => ['nullable', 'ulid', 'exists:lead_sources,id'],
            'interested_service' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'integer', 'min:0'],
            'priority' => ['required', 'in:low,medium,high'],
            'assigned_to' => ['nullable', 'ulid', 'exists:employees,id'],
            'status_id' => ['required', 'ulid', 'exists:pipeline_stages,id'],
            'description' => ['nullable', 'string'],
        ];
    }
}
