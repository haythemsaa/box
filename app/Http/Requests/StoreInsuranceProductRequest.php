<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInsuranceProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_coverage_amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'coverage_details' => ['nullable', 'string', 'max:2000'],
            'exclusions' => ['nullable', 'string', 'max:2000'],
            'monthly_price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'yearly_price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'is_mandatory' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tenant_id' => 'tenant',
            'name' => 'nom du produit',
            'description' => 'description',
            'max_coverage_amount' => 'montant maximum de couverture',
            'coverage_details' => 'détails de couverture',
            'exclusions' => 'exclusions',
            'monthly_price' => 'prix mensuel',
            'yearly_price' => 'prix annuel',
            'commission_rate' => 'taux de commission',
            'is_active' => 'statut actif',
            'is_mandatory' => 'statut obligatoire',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'monthly_price.required' => 'Le prix mensuel est obligatoire.',
            'monthly_price.numeric' => 'Le prix mensuel doit être un nombre.',
            'monthly_price.min' => 'Le prix mensuel doit être supérieur ou égal à 0.',
            'monthly_price.max' => 'Le prix mensuel ne peut pas dépasser 9999.99.',

            'yearly_price.numeric' => 'Le prix annuel doit être un nombre.',
            'yearly_price.min' => 'Le prix annuel doit être supérieur ou égal à 0.',

            'commission_rate.required' => 'Le taux de commission est obligatoire.',
            'commission_rate.numeric' => 'Le taux de commission doit être un nombre.',
            'commission_rate.min' => 'Le taux de commission doit être supérieur ou égal à 0.',
            'commission_rate.max' => 'Le taux de commission ne peut pas dépasser 100%.',

            'max_coverage_amount.required' => 'Le montant maximum de couverture est obligatoire.',
            'max_coverage_amount.numeric' => 'Le montant maximum de couverture doit être un nombre.',
            'max_coverage_amount.min' => 'Le montant maximum de couverture doit être supérieur ou égal à 0.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that yearly price is less than 12 times monthly price
            if ($this->filled('yearly_price') && $this->filled('monthly_price')) {
                $monthlyTotal = $this->monthly_price * 12;
                if ($this->yearly_price >= $monthlyTotal) {
                    $validator->errors()->add(
                        'yearly_price',
                        'Le prix annuel doit être inférieur au total mensuel (économies obligatoires).'
                    );
                }
            }
        });
    }
}
