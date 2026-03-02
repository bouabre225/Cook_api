<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyResponseRequest;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class SurveyResponseController extends Controller
{
    /**
     * GET /api/responses
     * List all responses with optional pagination & filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = SurveyResponse::query()->latest();

        // Optional filters
        if ($request->filled('dejeuner_interet')) {
            $query->where('dejeuner_interet', $request->dejeuner_interet);
        }

        if ($request->filled('contact_ok')) {
            $query->where('contact_ok', 'like', '%Oui%');
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'ilike', $search)
                  ->orWhere('quartier', 'ilike', $search)
                  ->orWhere('whatsapp', 'ilike', $search);
            });
        }

        $perPage = min((int) $request->get('per_page', 50), 1000);

        if ($perPage >= 1000) {
            // Return all for CSV/dashboard
            $data = $query->get();
            return response()->json(['data' => $data, 'total' => $data->count()]);
        }

        $paginated = $query->paginate($perPage);
        return response()->json($paginated);
    }

    /**
     * POST /api/responses
     * Store a new survey response.
     */
    public function store(StoreSurveyResponseRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Attach metadata
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr($request->userAgent() ?? '', 0, 255);

        $response = SurveyResponse::create($data);

        return response()->json([
            'message' => 'Réponse enregistrée avec succès.',
            'data'    => $response,
        ], 201);
    }

    /**
     * GET /api/responses/{id}
     * Get a single response.
     */
    public function show(SurveyResponse $response): JsonResponse
    {
        return response()->json($response);
    }

    /**
     * DELETE /api/responses/{id}
     * Delete a single response (admin use).
     */
    public function destroy(SurveyResponse $response): JsonResponse
    {
        $response->delete();
        return response()->json(['message' => 'Réponse supprimée.']);
    }

    /**
     * GET /api/responses/export/csv
     * Export all responses as a CSV file.
     */
    public function exportCsv(Request $request): Response
    {
        $responses = SurveyResponse::query()->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="cocooking_reponses_' . date('Y-m-d') . '.csv"',
        ];

        $columns = [
            'id', 'created_at',
            'nom', 'whatsapp', 'contact_ok',
            'situation', 'quartier',
            'petit_dej_habitude', 'petit_dej_interet',
            'petit_dej_menu', 'petit_dej_heure', 'petit_dej_budget',
            'dejeuner_lieu', 'dejeuner_interet',
            'dejeuner_menu', 'contraintes',
            'dejeuner_heure', 'dejeuner_budget',
            'mode_service', 'abonnement',
        ];

        $callback = function () use ($responses, $columns) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, $columns, ';');

            foreach ($responses as $r) {
                $row = [];
                foreach ($columns as $col) {
                    $val = $r->$col;
                    // Flatten arrays to string
                    if (is_array($val)) {
                        $val = implode(' | ', $val);
                    }
                    $row[] = $val;
                }
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GET /api/stats
     * Aggregated statistics for the dashboard.
     */
    public function stats(): JsonResponse
    {
        $total = SurveyResponse::count();

        $interestedPetitDej = SurveyResponse::where('petit_dej_interet', 'like', '%Oui%')->count();
        $interestedDejeuner = SurveyResponse::where('dejeuner_interet', 'like', '%Oui%')->count();
        $wantContact        = SurveyResponse::where('contact_ok', 'like', '%Oui%')->count();

        // Count by field
        $bySituation       = $this->countByField('situation');
        $byPetitDejInteret = $this->countByField('petit_dej_interet');
        $byDejeunerInteret = $this->countByField('dejeuner_interet');
        $byPetitDejBudget  = $this->countByField('petit_dej_budget');
        $byDejeunerBudget  = $this->countByField('dejeuner_budget');
        $byAbonnement      = $this->countByField('abonnement');

        return response()->json([
            'total'               => $total,
            'interested_petit_dej' => $interestedPetitDej,
            'interested_dejeuner' => $interestedDejeuner,
            'want_contact'        => $wantContact,
            'by_situation'        => $bySituation,
            'by_petit_dej_interet' => $byPetitDejInteret,
            'by_dejeuner_interet' => $byDejeunerInteret,
            'by_petit_dej_budget' => $byPetitDejBudget,
            'by_dejeuner_budget'  => $byDejeunerBudget,
            'by_abonnement'       => $byAbonnement,
        ]);
    }

    private function countByField(string $field): array
    {
        return SurveyResponse::select($field, DB::raw('count(*) as total'))
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->groupBy($field)
            ->orderByDesc('total')
            ->pluck('total', $field)
            ->toArray();
    }
}
