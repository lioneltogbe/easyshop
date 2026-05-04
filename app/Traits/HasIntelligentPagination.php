<?php

namespace App\Traits;

use Illuminate\Http\Request;

/**
 * Trait HasIntelligentPagination
 * Centralise la logique de pagination intelligente avec :
 * - Pagination dynamique
 * - Filtrage multi-colonnes
 * - Tri configurable
 * - Recherche
 * - Export des paramètres
 */
trait HasIntelligentPagination
{
    /**
     * Applique pagination intelligente à une query
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Request $request
     * @param array $searchable - Colonnes accessibles pour la recherche
     * @param array $sortable - Colonnes accessibles pour le tri
     * @param int $defaultPerPage - Résultats par page par défaut
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function applyIntelligentPagination($query, Request $request, array $searchable = [], array $sortable = [], int $defaultPerPage = 15)
    {
        // 1️⃣ RECHERCHE
        if ($search = $request->query('search')) {
            foreach ($searchable as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }
        }

        // 2️⃣ FILTRES (colonnes individuelles)
        foreach ($request->query() as $key => $value) {
            if ($value && strpos($key, 'filter_') === 0 && !empty($value)) {
                $column = str_replace('filter_', '', $key);
                if (in_array($column, $sortable)) {
                    if (is_array($value)) {
                        $query->whereIn($column, $value);
                    } else {
                        $query->where($column, $value);
                    }
                }
            }
        }

        // 3️⃣ TRI
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = in_array($request->query('sort_dir'), ['asc', 'desc']) ? $request->query('sort_dir') : 'desc';
        
        if (in_array($sortBy, $sortable)) {
            $query->orderBy($sortBy, $sortDir);
        }

        // 4️⃣ PAGINATION
        $perPage = $this->validatePerPage($request->query('per_page', $defaultPerPage));
        
        return $query->paginate($perPage)
            ->appends($request->query());
    }

    /**
     * Valide le nombre de résultats par page
     * @param int $perPage
     * @return int
     */
    private function validatePerPage($perPage): int
    {
        $allowed = [10, 15, 25, 50, 100];
        return in_array($perPage, $allowed) ? $perPage : 15;
    }

    /**
     * Retourne les paramètres de pagination pour la vue
     * @param Request $request
     * @return array
     */
    public function getPaginationParams(Request $request): array
    {
        return [
            'search' => $request->query('search'),
            'sort_by' => $request->query('sort_by'),
            'sort_dir' => $request->query('sort_dir'),
            'per_page' => $request->query('per_page', 15),
            'page' => $request->query('page', 1),
        ];
    }

    /**
     * Construit l'URL avec paramètres de pagination
     * @param string $baseUrl
     * @param Request $request
     * @return string
     */
    public function buildPaginationUrl($baseUrl, Request $request): string
    {
        $params = $this->getPaginationParams($request);
        $query = http_build_query(array_filter($params));
        return $baseUrl . ($query ? "?{$query}" : '');
    }
}
