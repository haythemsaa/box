<?php

namespace App\Http\Requests;

use App\Models\InsuranceProduct;
use Illuminate\Foundation\Http\FormRequest;

class AddContractInsuranceRequest extends FormRequest
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
            'insurance_product_id' => [
                'required',
                'exists:insurance_products,id',
                function ($attribute, $value, $fail) {
                    $product = InsuranceProduct::find($value);

                    if (!$product) {
                        $fail('Le produit d\'assurance sélectionné n\'existe pas.');
                        return;
                    }

                    if (!$product->is_active) {
                        $fail('Ce produit d\'assurance n\'est plus disponible.');
                        return;
                    }
                },
            ],
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
            'insurance_product_id' => 'produit d\'assurance',
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
            'insurance_product_id.required' => 'Veuillez sélectionner un produit d\'assurance.',
            'insurance_product_id.exists' => 'Le produit d\'assurance sélectionné est invalide.',
        ];
    }
}
