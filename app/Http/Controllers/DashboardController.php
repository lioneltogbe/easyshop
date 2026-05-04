<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VenteCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * ============================================================
     * INDEX - AFFICHAGE DU DASHBOARD
     * ============================================================
     */

    /**
     * Affiche le dashboard
     * 
     * GET /dashboard
     * 
     * Affiche les statistiques selon le rôle de l'utilisateur :
     * - Admin : Toutes les statistiques
     * - Manager : Statistiques de vente
     * - Vendeur : Ses propres ventes
     * - Comptable : Factures et rapports
     * - Client : Ses commandes
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $stats=[];

        // Récupérer les statistiques selon le rôle
        if ($user->hasRole('admin')) {
           $stats=$this->getAdminStats();
        } elseif ($user->hasRole('manager')) {
            $stats=$this->getManagerStats();        
        } elseif ($user->hasRole('vendeur')) {
           $stats=$this->getVendeurStats();
        } elseif ($user->hasRole('comptable')) {
            $stats=$this->getClientStats($user);
        } elseif ($user->hasRole('client')) {
            $stats=$this->getComptableStats(); 
        }

        return view('dashboard.index', [
            'user' => $user,
            'userRole' => $user->roles->first()?->name ?? 'user',
            'stats'=>$stats,
        ]);
    }

    /**
     * ============================================================
     * STATISTIQUES ADMIN
     * ============================================================
     */

    /**
     * Récupère les statistiques pour l'Admin
     * 
     * @return array
     */
    private function getAdminStats(){
        return [
            // Utilisateurs
            'totalUsers' => User::count(),
            'activeUsers' => User::where('is_active', 1)->count(),
            'inactiveUsers' => User::where('is_active', 0)->count(),
            'newUsersThisMonth' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // Ventes
            'totalVentes' => VenteCommand::count(),
            'ventesThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'ventesThisYear' => VenteCommand::whereYear('created_at', now()->year)->count(),

            // Montants
            'totalMontant' => VenteCommand::sum('montantPayer') ?? 0,
            'montantThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montantPayer') ?? 0,

            // Factures
            'totalFactures' => VenteCommand::whereNotNull('lien_facture')->count(),
            'facturesThisMonth' => VenteCommand::whereNotNull('lien_facture')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // Graphiques
            'ventesParMois' => $this->getVentesParMois(),
            'topVendeurs' => $this->getTopVendeurs(),
            'recentVentes' => VenteCommand::with('client', 'creator')
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * ============================================================
     * STATISTIQUES MANAGER
     * ============================================================
     */

    /**
     * Récupère les statistiques pour le Manager
     * 
     * @return array
     */
    private function getManagerStats()
    {
        return [
            // Ventes
            'totalVentes' => VenteCommand::count(),
            'ventesThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'ventesThisWeek' => VenteCommand::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            // Montants
            'totalMontant' => VenteCommand::sum('montantPayer') ?? 0,
            'montantThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montantPayer') ?? 0,


            // // Factures
            'totalFactures' => VenteCommand::whereNotNull('lien_facture')->count(),
            'facturesEnAttente' => VenteCommand::whereNull('lien_facture')
                ->where('status', 'livrer')
                ->count(),

            // Vendeurs
            'totalVendeurs' => User::whereHas('roles', function ($query) {
                $query->where('name', 'vendeur');
            })->count(),

            // Graphiques
            'ventesParMois' => $this->getVentesParMois(),
            'topVendeurs' => $this->getTopVendeurs(5),
            'recentVentes' => VenteCommand::with('client', 'creator')
                ->latest()
                ->limit(15)
                ->get(),
        ];
    }

    /**
     * ============================================================
     * STATISTIQUES VENDEUR
     * ============================================================
     */

    /**
     * Récupère les statistiques pour le Vendeur
     * 
     * @param User $user
     * @return array
     */
    private function getVendeurStats()
    {
                $user = Auth::user();

        return [
            // Ventes personnelles
            'mesVentes' => VenteCommand::where('created_by', $user->id)->count(),
            'mesVentesThisMonth' => VenteCommand::where('created_by', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'mesVentesThisWeek' => VenteCommand::where('created_by', $user->id)
                ->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])
                ->count(),

            // Montants personnels
            'monMontantTotal' => VenteCommand::where('created_by', $user->id)
                ->sum('montantPayer') ?? 0,
            'monMontantThisMonth' => VenteCommand::where('created_by', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montantPayer') ?? 0,

            // Factures personnelles
            'mesFactures' => VenteCommand::where('created_by', $user->id)
                ->whereNotNull('lien_facture')
                ->count(),

            // Mes ventes récentes
            'mesVentesRecentes' => VenteCommand::where('created_by', $user->id)
                ->with('client')
                ->latest()
                ->limit(10)
                ->get(),

            // Graphiques
            'mesVentesParMois' => $this->getVentesParMoisVendeur($user->id),
        ];
    }

    /**
     * ============================================================
     * STATISTIQUES COMPTABLE
     * ============================================================
     */

    /**
     * Récupère les statistiques pour le Comptable
     * 
     * @return array
     */
    private function getComptableStats()
    {
        return [
            // Factures
            'totalFactures' => VenteCommand::whereNotNull('lien_facture')->count(),
            'facturesThisMonth' => VenteCommand::whereNotNull('lien_facture')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'facturesEnAttente' => VenteCommand::whereNull('lien_facture')
                ->where('status', 'livrer')
                ->count(),

            // Montants
            'montantTotal' => VenteCommand::sum('montantTotal') ?? 0,
            'montantThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montantPayer') ?? 0,
            'montantThisYear' => VenteCommand::whereYear('created_at', now()->year)
                ->sum('montantPayer') ?? 0,

            // TVA
            'tvaThisMonth' => VenteCommand::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('montant_tva') ?? 0,
            'tvaThisYear' => VenteCommand::whereYear('created_at', now()->year)
                ->sum('montant_tva') ?? 0,

            // Graphiques
            'montantsParMois' => $this->getMontantsParMois(),
            'recentFactures' => VenteCommand::whereNotNull('lien_facture')
                ->with('client', 'user')
                ->latest()
                ->limit(15)
                ->get(),
        ];
    }

    /**
     * ============================================================
     * STATISTIQUES CLIENT
     * ============================================================
     */

    /**
     * Récupère les statistiques pour le Client
     * 
     * @param User $user
     * @return array
     */
    private function getClientStats(User $user)
    {
        return [
            // Commandes
            'mesCommandes' => VenteCommand::where('client_id', $user->id)->count(),
            'mesCommandesEnCours' => VenteCommand::where('client_id', $user->id)
                ->where('status', '!=', 'livrer')
                ->count(),
            'mesCommandesLivrees' => VenteCommand::where('client_id', $user->id)
                ->where('status', 'livrer')
                ->count(),

            // Montants
            'monMontantTotal' => VenteCommand::where('client_id', $user->id)
                ->sum('montantTotal') ?? 0,

            // Factures
            'mesFactures' => VenteCommand::where('client_id', $user->id)
                ->whereNotNull('lien_facture')
                ->count(),

            // Mes commandes récentes
            'mesCommandesRecentes' => VenteCommand::where('client_id', $user->id)
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * ============================================================
     * MÉTHODES UTILITAIRES POUR LES GRAPHIQUES
     * ============================================================
     */

    /**
     * Récupère les ventes par mois (12 derniers mois)
     * 
     * @return array
     */
    private function getVentesParMois()
    {
        $ventes = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $count = VenteCommand::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $ventes[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $ventes,
        ];
    }

    /**
     * Récupère les ventes par mois pour un vendeur
     * 
     * @param int $userId
     * @return array
     */
    private function getVentesParMoisVendeur($userId)
    {
        $ventes = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $count = VenteCommand::where('created_by', $userId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $ventes[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $ventes,
        ];
    }

    /**
     * Récupère les montants par mois (12 derniers mois)
     * 
     * @return array
     */
    private function getMontantsParMois()
    {
        $montants = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $total = VenteCommand::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('montantPayer') ?? 0;

            $montants[] = $total;
        }

        return [
            'labels' => $labels,
            'data' => $montants,
        ];
    }

    /**
     * Récupère les top vendeurs
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopVendeurs($limit = 5)
    {
        return VenteCommand::select('created_by')
            ->selectRaw('COUNT(*) as total_ventes')
            ->selectRaw('SUM(montantPayer) as montant_total')
            ->groupBy('created_by')
            ->orderBy('montant_total', 'desc')
            ->limit($limit)
            ->with('creator')
            ->get();
    }
}
