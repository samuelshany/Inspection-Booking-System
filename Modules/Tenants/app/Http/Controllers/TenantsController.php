<?php

namespace Modules\Tenants\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Stancl\Tenancy\Tenant;
use Illuminate\Support\Facades\Artisan;
use Modules\Tenants\App\Models\Tenant;

// use Stancl\Tenancy\Database\Models\Tenant ;

class TenantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tenants::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tenants::create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {


        $tenant = Tenant::create([
            'id' => $request->tenant_id,

        ]);

        $tenant->domains()->create([
        'domain' => $request->tenant_id.'localhost',
        ]);
        Artisan::call('tenancy:migrate-tenant', [
            'tenant' => $tenant->id,
        ]);
        return redirect()->route('tenants.create')->with('success', 'Tenant created!');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('tenants::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('tenants::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
