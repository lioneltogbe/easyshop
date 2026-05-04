<?php
 
namespace App\Http\Controllers;
 
use App\Models\Client;
use App\Traits\HasIntelligentPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
 
class ClientController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['name', 'email', 'phone', 'city'];
    protected array $sortable   = ['name', 'email', 'city', 'creditLimit', 'created_at'];

    public function index(Request $request)
    {
        try {
            $query = Client::with('ventes');

            $clients = $this->applyIntelligentPagination(
                $query, $request,
                $this->searchable,
                $this->sortable,
                15
            );

            return view('clients.index', [
                'clients'       => $clients,
                'totalClients'  => $clients->total(),
                'params'        => $this->getPaginationParams($request),
            ]);
        } catch (\Exception $e) {
            return view('clients.index', [
                'clients'      => collect(),
                'totalClients' => 0,
                'params'       => $this->getPaginationParams($request),
            ]);
        }
    }

    public function show($id)
    {
        try {
            $client = Client::with('ventes')->findOrFail($id);
            $client->available_credit      = $client->getAvailableCredit();
            $client->is_over_credit_limit  = $client->isOverCreditLimit();
            $client->command_count         = $client->getCommandCount();
            $client->total_montant         = $client->getTotalMontant();
            return view('clients.show', ['client' => $client]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('clients.index')->with('erreur', 'Client non trouvé');
        } catch (\Exception $e) {
            return to_route('clients.index')->with('erreur', 'Erreur lors de la récupération du client');
        }
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'email'       => 'nullable|email|unique:clients,email',
                'phone'       => 'nullable|string|max:20',
                'address'     => 'nullable|string',
                'city'        => 'nullable|string',
                'country'     => 'nullable|string',
                'creditLimit' => 'nullable|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            Client::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'address'        => $request->address,
                'city'           => $request->city,
                'country'        => $request->country,
                'creditLimit'    => $request->creditLimit ?? 0,
                'current_credit' => 0,
            ]);
            return to_route('clients.index')->with('success', 'Client créé avec succès');
        } catch (\Exception $e) {
            return to_route('clients.index')->with('erreur', 'Erreur lors de la création du client');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name'        => 'sometimes|string|max:255',
                'email'       => 'sometimes|email|unique:clients,email,' . $id,
                'phone'       => 'nullable|string|max:20',
                'address'     => 'nullable|string',
                'city'        => 'nullable|string',
                'country'     => 'nullable|string',
                'creditLimit' => 'nullable|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return to_route('clients.index')->with('erreur', 'Erreur de validation');
            }
            $client->update($request->only(['name','email','phone','address','city','country','creditLimit']));
            return to_route('clients.index')->with('success', 'Client mis à jour avec succès');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('clients.index')->with('erreur', 'Client non trouvé');
        } catch (\Exception $e) {
            return to_route('clients.index')->with('erreur', 'Erreur lors de la mise à jour du client');
        }
    }

    public function destroy($id)
    {
        try {
            Client::findOrFail($id)->delete();
            return to_route('clients.index')->with('success', 'Client supprimé avec succès');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('clients.index')->with('erreur', 'Client non trouvé');
        } catch (\Exception $e) {
            return to_route('clients.index')->with('erreur', 'Erreur lors de la suppression du client');
        }
    }
}
