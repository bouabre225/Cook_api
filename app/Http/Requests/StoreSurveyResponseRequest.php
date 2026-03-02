<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyResponseRequest extends FormRequest
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
            // Required fields
            'situation'          => ['required', 'string', 'max:100'],
            'quartier'           => ['required', 'string', 'max:150'],
            'dejeuner_interet'   => ['required', 'string', 'max:100'],

            // Optional singles
            'petit_dej_habitude' => ['nullable', 'string', 'max:100'],
            'petit_dej_interet'  => ['nullable', 'string', 'max:100'],
            'petit_dej_heure'    => ['nullable', 'string', 'max:50'],
            'petit_dej_budget'   => ['nullable', 'string', 'max:50'],
            'dejeuner_heure'     => ['nullable', 'string', 'max:50'],
            'dejeuner_budget'    => ['nullable', 'string', 'max:50'],
            'abonnement'         => ['nullable', 'string', 'max:100'],
            'nom'                => ['nullable', 'string', 'max:150'],
            'whatsapp'           => ['nullable', 'string', 'max:30'],
            'contact_ok'         => ['nullable', 'string', 'max:100'],

            // Array fields
            'petit_dej_menu'     => ['nullable', 'array'],
            'petit_dej_menu.*'   => ['string', 'max:100'],
            'dejeuner_lieu'      => ['nullable', 'array'],
            'dejeuner_lieu.*'    => ['string', 'max:100'],
            'dejeuner_menu'      => ['nullable', 'array'],
            'dejeuner_menu.*'    => ['string', 'max:100'],
            'contraintes'        => ['nullable', 'array'],
            'contraintes.*'      => ['string', 'max:100'],
            'mode_service'       => ['nullable', 'array'],
            'mode_service.*'     => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'situation.required'        => 'Veuillez sélectionner votre situation professionnelle.',
            'quartier.required'         => 'Veuillez indiquer votre quartier de travail.',
            'dejeuner_interet.required' => 'Veuillez indiquer votre intérêt pour le service de déjeuner.',
        ];
    }
}
