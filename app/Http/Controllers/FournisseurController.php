<?php
 
namespace App\Http\Controllers;
 
use App\Models\Fournisseur;
use App\Traits\HasIntelligentPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
 
class FournisseurController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['name', 'email', 'phone', 'city'];
    protected array $sortable   = ['name', 'email', 'city', 'created_at'];

    public function index(Request $request)
    {
        try {
            $query = Fournisseur::with('achats');

            $fournisseurs = $this->applyIntelligentPagination(
                $query, $request,
                $this->searchable,
                $this->sortable,
                15
            );

            return view('fournisseurs.index', [
                'fournisseurs'       => $fournisseurs,
                'totalFournisseurs'  => $fournisseurs->total(),
                'params'             => $this->getPaginationParams($request),
            ]);
        } catch (\Exception $e) {
            return view('fournisseurs.index', [
                'fournisseurs'      => collect(),
                'totalFournisseurs' => 0,
                'params'            => $this->getPaginationParams($request),
            ]);
        }
    }

    public function show($id)
    {
        try {
            $fournisseur = Fournisseur::with('achats')->findOrFail($id);
            $fournisseur->command_count  = $fournisseur->getCommandCount();
            $fournisseur->total_montant  = $fournisseur->getCommandTotalMontant();
            return view('fournisseurs.show', ['fournisseur' => $fournisseur]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Fournisseur non trouvé');
        } catch (\Exception $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Erreur lors de la récupération du fournisseur');
        }
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:255',
                'email'   => 'nullable|email|unique:fournisseurs,email',
                'phone'   => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'city'    => 'nullable|string',
                'country' => 'nullable|string',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            Fournisseur::create($request->only(['name','email','phone','address','city','country']));
            return to_route('fournisseurs.index')->with('success', 'Fournisseur créé avec succès');
        } catch (\Exception $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Erreur lors de la création du fournisseur');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $fournisseur = Fournisseur::findOrFail($id);
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'nullable|email|unique:fournisseurs,email,' . $id,
                'phone'       => 'nullable|string|max:20',
                'address'     => 'nullable|string',
                'city'        => 'nullable|string',
                'country'     => 'nullable|string',
                'creditLimit' => 'nullable|numeric|min:0',
            ]);
            $fournisseur->update($validated);
            return to_route('fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès');
        } catch (\Exception $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Erreur lors de la mise à jour du fournisseur');
        }
    }

    public function delete($id)
    {
        try {
            Fournisseur::findOrFail($id)->delete();
            return to_route('fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Fournisseur non trouvé');
        } catch (\Exception $e) {
            return to_route('fournisseurs.index')->with('erreur', 'Erreur lors de la suppression du fournisseur');
        }
    }
}
