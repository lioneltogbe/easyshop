<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    /**
     * Page alertes accesssible depuis le menu Stocks
     */
    public function stockAlertes()
    {
        $alerts = Alert::with(['product', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('stocks.alertes', compact('alerts'));
    }

      /**
     * Afficher la liste de toutes les alertes
     * Récupère toutes les alertes avec produit et créateur
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Récupérer toutes les alertes
            $alerts = Alert::with(['product', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
 
            // Retourner les alertes
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Liste des alertes récupérée',
            //     'data' => $alerts
            // ], 200);

            return view('alertes.index', compact('alerts'));
        } catch (\Exception $e) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Erreur lors de la récupération des alertes',
            //     'error' => $e->getMessage()
            // ], 500);

            return view('alerts.index')->withErrors('Erreur lors de la récupération des alertes: ' . $e->getMessage());
        }
    }
 
    /**
     * Récupérer les alertes non lues
     * Utilise la méthode recupererNotRead() du model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnread()
    {
        try {
            // Récupérer les alertes non lues
            $alerts = Alert::with(['product', 'creator'])
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();
 
            // Retourner les alertes
            return response()->json([
                'success' => true,
                'message' => 'Alertes non lues récupérées',
                'data' => $alerts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des alertes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * Récupérer les alertes critiques
     * Utilise la méthode recupererCritique() du model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCritical()
    {
        try {
            // Récupérer les alertes critiques
            $alerts = Alert::with(['product', 'creator'])
                ->where('severity', 'critique')
                ->orderBy('created_at', 'desc')
                ->get();
 
            // Retourner les alertes
            return response()->json([
                'success' => true,
                'message' => 'Alertes critiques récupérées',
                'data' => $alerts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des alertes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
    /**
     * Marquer une alerte comme lue
     * Utilise la méthode markAsRead() du model
     * 
     * @param int $id - ID de l'alerte
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        try {
            // Rechercher l'alerte
            $alert = Alert::findOrFail($id);
 
            // Marquer comme lue
            $alert->update(['read_at' => now()]);
 
            // Retourner l'alerte mise à jour
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Alerte marquée comme lue',
            //     'data' => $alert
            // ], 200);

            return to_route('alertes.index')->with('success', 'Alerte marquée comme lue');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        
            return to_route('alertes.index')->withErrors('Alerte non trouvée');
        } catch (\Exception $e) {
            return to_route('alertes.index')->withErrors('Erreur lors de la mise à jour de l\'alerte: ' . $e->getMessage());
        }
    }
 
    /**
     * Marquer toutes les alertes comme lues
     * Met à jour tous les read_at à la date actuelle
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        try {
            $count = Alert::whereNull('read_at')->count();
            Alert::whereNull('read_at')->update(['read_at' => now()]);
            return to_route('alertes.index')->with('success', $count . ' alerte(s) marquée(s) comme lue(s)');
        } catch (\Exception $e) {
            return to_route('alertes.index')->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
 
    /**
     * Supprimer une alerte
     * Supprime une alerte de la base de données
     * 
     * @param int $id - ID de l'alerte
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->delete();
            return to_route('stocks.alertes')->with('success', 'Alerte supprimée.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('alertes.index')->withErrors('Alerte introuvable.');
        } catch (\Exception $e) {
            return to_route('alertes.index')->withErrors('Erreur : ' . $e->getMessage());
        }
    }
}
