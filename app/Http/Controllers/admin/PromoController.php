<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:promos,code|max:255',
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_scope' => ['nullable', 'string', Rule::in(['global', 'personal'])],
            'expires_at' => 'nullable|date|after:today',
            'constraints' => 'nullable|array',
            'constraints.*.type' => 'required|string|in:fabric_type,payment_method',
            'constraints.*.value' => 'required|string',
        ]);
        
        $validatedData['current_uses'] = 0;

        
        try {
            Promo::create($validatedData);
            return response()->json(['message' => 'Promo created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create promo.'], 500);
        }
    }
    public function update(Request $request, Promo $promo)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|unique:promos,code,' . $promo->id . '|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_scope' => 'nullable|string|in:global,personal',
            'expires_at' => 'nullable|date|after:today',
            'constraints' => 'nullable|array',
            'constraints.*.type' => 'required|string|in:fabric_type,payment_method',
            'constraints.*.value' => 'required|string',
        ]);

        $promo->update($validatedData);

        return response()->json(['message' => 'Promo updated successfully!']);
    }

    public function destroy(Promo $promo)
    {
        try {
            $promo->delete();
            return response()->json(['message' => 'Promo deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete promo.'], 500);
        }
    }
}