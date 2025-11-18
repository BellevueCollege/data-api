<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Client;
use App\Models\Permission;



class AdminController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:admin', ['except' => 'admin.login']); //->except(['admin/login','admin/logout']);
    }

    public function index()
    {
        $client_data = Client::all();
        //dd($client_data);
        return view('admin.index', ['data' => $client_data ]);
    }

    public function loginShow()
    {
        if ( Auth::check() ) {
            return redirect('admin');
        }
        return view('admin.login');
    }

    public function loginPost(Request $request)
    {
        $this->validate($request, [
            'samaccountname'    => 'required',
            'password' => 'required',
        ]);

        $req_creds = $request->only('samaccountname', 'password');
        if ( Auth::guard('admin')->attempt($req_creds) ) {
            // Passed so send to admin dashboard!
            return redirect('admin');
        }
        
        // Failed so send back to login
        return redirect('admin')->withErrors('Your username or password is incorrect. Or you may not be in the correct group to have authorization to this dashboard.');

    }

    public function logout(Request $request) {
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }

    public function deleteClient($id){

        //delete client
        try { 
            Client::destroy($id);
            Log::info(sprintf("Client %d deleted", $id));
        } catch ( \Exception $e ) {
            Log::error($e->getMessage());
            return redirect()->back()->withError("There was an error while deleting the client.");
        }

        return redirect()->back()->withSuccess('Client successfully deleted.');
    }

    public function addClientShow() {
        // Get available permissions
        $permissions = Permission::all();
        return view('admin.addclient', compact('permissions')); 
    }

    public function addClientPost(Request $request) {

        $this->validate($request, [
            'clientname'  => 'required', 
            'clienturl'   => 'required'
        ]);

        $form_input = $request->only('clientname', 'clienturl', 'permissions');

        $new_clientkey = Client::generateClientKey();

        $newclient = new Client;
        $newclient->clientname = $form_input['clientname'];
        $newclient->clienturl = $form_input['clienturl'];
        $newclient->clientid = Client::generateClientID();
        $newclient->password = Hash::make($new_clientkey);

        try { 
            $newclient->save();

            // Sync permissions after the client is saved
            if (isset($form_input['permissions'])) {
                $newclient->syncPermissionsByName($form_input['permissions']);
            } else {
                $newclient->permissions()->detach();
            }
        }
        catch ( \Exception $e ) {
            return redirect()->back()->withError("There was an error while adding the client.");
        }

        return view('admin.addclient')->with('success', true)
            ->with('clientname', $newclient->clientname)
            ->with('clientid', $newclient->clientid)
            ->with('clientkey', $new_clientkey)
            ->with('set_permissions', $form_input['permissions'] ?? []);
    }

    /**
     * Edit an existing client
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function updateClient(Request $request, $id)
    {
        // Get available permissions
        $permissions = Permission::all();
        
        // Get the client to edit
        $client = Client::findOrFail($id);
        
        return view('admin.updateclient', compact('client', 'permissions'));
    }

    public function updateClientPut(Request $request, $id)
    {
        $this->validate($request, [
            'clientname' => 'required',
            'clienturl' => 'required|url'
        ]);

        $form_input = $request->only('clientname', 'clienturl', 'permissions');
        
        try {
            // Find the existing client
            $client = Client::findOrFail($id);
            
            // Update client details
            $client->clientname = $form_input['clientname'];
            $client->clienturl = $form_input['clienturl'];
            $client->save();

            // Sync permissions
            if (isset($form_input['permissions'])) {
                $client->syncPermissionsByName($form_input['permissions']);
            } else {
                $client->permissions()->detach();
            }

            return redirect()
                ->route('admin.client.update', ['id' => $id])
                ->with('success', 'Client updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating client: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->withError('There was an error while updating the client.');
        }
    }
}
