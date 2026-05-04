<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\HasIntelligentPagination;

class FinancialTransactionController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['description', 'type', 'categorie', 'paiement_method'];
    protected array $sortable   = ['type', 'montant', 'paiement_method', 'categorie', 'created_at'];

       /**
     * Afficher la liste de toutes les transactions
     * Récupère toutes les transactions avec le créateur
     * 
     * @return \Illuminate\Http\JsonResponse
     */
 public function index(){

    try {
        // Total des revenus
        $totalRevenue = FinancialTransaction::where('type', 'REVENUE')->sum('montant');
        
        // Total des dépenses
        $totalExpense = FinancialTransaction::where('type', 'DEPENSE')->sum('montant');
        
        // Bénéfice net
        $profit = $totalRevenue - $totalExpense;
        
        // Marge bénéficiaire (en pourcentage)
        $marginPercentage = $totalRevenue > 0 
            ? round(($profit / $totalRevenue) * 100, 2) 
            : 0;
        
        // Revenus par mois (12 derniers mois)
        $revenueByMonth = FinancialTransaction::where('type', 'REVENUE')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(montant) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month');
        
        // Dépenses par mois (12 derniers mois)
        $expenseByMonth = FinancialTransaction::where('type', 'DEPENSE')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(montant) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month');
        
        // 10 dernières transactions
        // $transactions = FinancialTransaction::with('creator')
        // ->orderBy('created_at', 'desc')
        // ->get()
        // ->reverse()
        // ->values();

        $transactions = FinancialTransaction::with('creator')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();


        // Préparer les métriques pour la vue
        $metrics = [
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'profit' => $profit,
            'margin_percentage' => $marginPercentage,
        ];

        // Retourner la vue avec toutes les données
        return view('finances.index', [
            'metrics' => $metrics,
            'revenueByMonth' => $revenueByMonth,
            'expenseByMonth' => $expenseByMonth,
            'transactions' => $transactions,
        ]);

    } catch (\Exception $e) {
        // En cas d'erreur, rediriger avec un message d'erreur
        return redirect()->back()->with('error', 'Erreur lors de la récupération des données financières : ' . $e->getMessage());
    }

}
 
    /**
     * Afficher une transaction spécifique
     * Récupère une transaction avec son créateur
     * 
     * @param int $id - ID de la transaction
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Récupérer la transaction
            $transaction = FinancialTransaction::with('creator')->findOrFail($id);
 
            // Retourner la transaction
            return response()->json([
                'success' => true,
                'message' => 'Transaction récupérée',
                'data' => $transaction
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * Créer une nouvelle transaction
     * Crée une transaction financière (REVENUE ou DEPENSE)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Valider les données
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:REVENUE,DEPENSE',
                'description' => 'required|string|max:255',
                'montant' => 'required|numeric|min:0',
                'paiement_method' => 'required|in:caisse,cheque,transfert,credit',
                'categorie' => 'nullable|string|max:255',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()->withErrors($validator)->withInput();
            }
 
            // Calculer le solde actuel avant l'enregistrement
            $soldeActuel = FinancialTransaction::calculerNouveauSolde($request->type, $request->montant);
 
            // Créer la transaction
            $transaction = FinancialTransaction::create([
                'type' => $request->type,
                'description' => $request->description,
                'montant' => $request->montant,
                'paiement_method' => $request->paiement_method,
                'categorie' => $request->categorie,
                'created_by' => auth()->id() ?? 1,
                'solde_actuel' => $soldeActuel,
            ]);
 
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction créée avec succès',
                    'data' => $transaction
                ], 201);
            }
 
            return redirect()->route('finances.transactions')->with('success', 'Transaction créée avec succès.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création de la transaction',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de la création de la transaction : ' . $e->getMessage());
        }
    }
 
    /**
     * Récupérer les métriques financières
     * Calcule le CA, dépenses, bénéfice et marge
     * Règle 13 : Calcul des métriques
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics()
    {
        try {
            // Calculer le total des revenus
            $totalRevenue = FinancialTransaction::where('type', 'REVENUE')->sum('montant');
 
            // Calculer le total des dépenses
            $totalExpense = FinancialTransaction::where('type', 'DEPENSE')->sum('montant');
 
            // Calculer le bénéfice
            $profit = $totalRevenue - $totalExpense;
 
            // Calculer la marge
            $margin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
 
            // Retourner les métriques
            return response()->json([
                'success' => true,
                'message' => 'Métriques financières récupérées',
                'data' => [
                    'total_revenue' => $totalRevenue,
                    'total_expense' => $totalExpense,
                    'profit' => $profit,
                    'margin_percentage' => $margin
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des métriques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * Récupérer les transactions d'une période
     * Filtre les transactions par date de début et fin
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPeriod(Request $request)
    {
        try {
            // Valider les données
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
 
            // Récupérer les transactions de la période
            $transactions = FinancialTransaction::whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ])->with('creator')->orderBy('created_at', 'desc')->get();
 
            // Calculer les totaux
            $totalRevenue = $transactions->where('type', 'REVENUE')->sum('montant');
            $totalExpense = $transactions->where('type', 'DEPENSE')->sum('montant');
            $profit = $totalRevenue - $totalExpense;
 
            // Retourner les transactions
            return response()->json([
                'success' => true,
                'message' => 'Transactions de la période récupérées',
                'data' => [
                    'transactions' => $transactions,
                    'summary' => [
                        'total_revenue' => $totalRevenue,
                        'total_expense' => $totalExpense,
                        'profit' => $profit
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 


    // Récupérer les transactions avec pagination intelligente
    public function getTransactions(\Illuminate\Http\Request $request)
    {
        $query = FinancialTransaction::with('creator');

        $transactions = $this->applyIntelligentPagination(
            $query, $request,
            $this->searchable,
            $this->sortable,
            20
        );

        return view('finances.transactions', [
            'transactions' => $transactions,
            'params'       => $this->getPaginationParams($request),
        ]);
    }
    /**
     * Récupérer les revenus
     * Utilise la méthode recupererRevenue() du model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRevenues(\Illuminate\Http\Request $request)
    {
        $query = FinancialTransaction::where('type', 'REVENUE')->with('creator');

        $transactions = $this->applyIntelligentPagination(
            $query, $request,
            ['description', 'paiement_method', 'categorie'],
            ['montant', 'paiement_method', 'categorie', 'created_at'],
            20
        );

        return view('finances.revenus', [
            'transactions' => $transactions,
            'params'       => $this->getPaginationParams($request),
        ]);
    }
 
    /**
     * Récupérer les dépenses
     * Utilise la méthode recupererDepense() du model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getExpenses(\Illuminate\Http\Request $request)
    {
        $query = FinancialTransaction::where('type', 'DEPENSE')->with('creator');

        $transactions = $this->applyIntelligentPagination(
            $query, $request,
            ['description', 'paiement_method', 'categorie'],
            ['montant', 'paiement_method', 'categorie', 'created_at'],
            20
        );

        return view('finances.depenses', [
            'transactions' => $transactions,
            'params'       => $this->getPaginationParams($request),
        ]);
    }
}
