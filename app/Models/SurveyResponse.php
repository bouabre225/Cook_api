<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'survey_responses';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'situation',
        'quartier',
        'petit_dej_habitude',
        'petit_dej_interet',
        'petit_dej_menu',
        'petit_dej_heure',
        'petit_dej_budget',
        'dejeuner_lieu',
        'dejeuner_interet',
        'dejeuner_menu',
        'contraintes',
        'dejeuner_heure',
        'dejeuner_budget',
        'mode_service',
        'abonnement',
        'nom',
        'whatsapp',
        'contact_ok',
        'ip_address',
        'user_agent',
    ];

    /**
     * JSON fields - automatically cast to/from PHP arrays.
     */
    protected $casts = [
        'petit_dej_menu' => 'array',
        'dejeuner_lieu'  => 'array',
        'dejeuner_menu'  => 'array',
        'contraintes'    => 'array',
        'mode_service'   => 'array',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * Default values for JSON fields.
     */
    protected $attributes = [
        'petit_dej_menu' => '[]',
        'dejeuner_lieu'  => '[]',
        'dejeuner_menu'  => '[]',
        'contraintes'    => '[]',
        'mode_service'   => '[]',
    ];
}
