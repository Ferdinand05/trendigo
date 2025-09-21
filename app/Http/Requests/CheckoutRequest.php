<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'province' => 'required|string',
            'province_id' => 'required|numeric',
            'city' => 'required|string',
            'city_id' => 'required|numeric',
            'district' => 'required|string',
            'district_id' => 'required|numeric',
            'sub_district' => 'required|string',
            'sub_district_id' => 'required|numeric',
            'postal_code' => 'required|string',
            'shipping_cost' => 'required|numeric',
            'shipping_service_name' => 'required|string',
            'shipping_etd' => 'nullable|string',
            'total' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.notes' => 'nullable|string',
        ];
    }
}
