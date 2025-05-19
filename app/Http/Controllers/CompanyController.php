<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    //
    public function store(Request $request)
    {
         $user = auth()->user();

        if (!$user || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'company_name' => 'required|string|unique:companies,company_name'
        ]);

        $company = Company::create([
            'company_name' => $request->company_name
        ]);

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company
        ], 201);
    }

    public function getAllCompanies()
    {
        $user = auth()->user();

        if (!$user || $user->role->access_type !== 'superadmin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $companies = Company::all();

        return response()->json($companies, 200);
    }

            public function search(Request $request)
        {
            $query = Company::query();

            if ($request->has('id')) {
                $query->where('id', $request->id);
            }

            if ($request->has('company_name')) {
                $query->where('company_name', 'like', '%' . $request->company_name . '%');
            }

            $results = $query->get();

            if ($results->isEmpty()) {
                return response()->json(['message' => 'No matching companies found'], 404);
            }

            return response()->json(['companies' => $results], 200);
        }


public function update(Request $request, $id)
{
    $user = auth()->user();

    if (!$user || $user->role->access_type !== 'admin') {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $company = Company::find($id);

    if (!$company) {
        return response()->json(['error' => 'Company not found'], 404);
    }

    $request->validate([
        'company_name' => 'required|string|unique:companies,company_name,' . $company->id
    ]);

    $company->update(['company_name' => $request->company_name]);

    return response()->json([
        'message' => 'Company name updated successfully',
        'company' => $company
    ]);
}

public function destroy($id)
{
    $user = auth()->user();

    if (!$user || $user->role->access_type !== 'admin') {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    $company = Company::find($id);

    if (!$company) {
        return response()->json(['error' => 'Company not found'], 404);
    }

    $company->delete();

    return response()->json(['message' => 'Company deleted successfully']);
}

}
        


