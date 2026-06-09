<?php

namespace App\Http\Controllers;

use App\Models\ContractTerm;
use Illuminate\Http\Request;

class ContractTermController extends Controller
{
    public function index()
    {
        $terms = ContractTerm::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.contract_terms.index', compact('terms'));
    }

    public function create()
    {
        return view('admin.contract_terms.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        ContractTerm::create($data);

        return redirect()->route('admin.contract-terms.index')
            ->with('success', __('app.saved_successfully'));
    }

    public function edit(ContractTerm $contractTerm)
    {
        return view('admin.contract_terms.edit', compact('contractTerm'));
    }

    public function update(Request $request, ContractTerm $contractTerm)
    {
        $data = $this->validateData($request);
        $data['is_active'] = $request->boolean('is_active');
        $contractTerm->update($data);

        return redirect()->route('admin.contract-terms.index')
            ->with('success', __('app.updated_successfully'));
    }

    public function destroy(ContractTerm $contractTerm)
    {
        $contractTerm->delete();
        return redirect()->route('admin.contract-terms.index')
            ->with('success', __('app.deleted_successfully'));
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title_km'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'content_km'  => 'required|string',
            'content_en'  => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);
    }
}
