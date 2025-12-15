<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        return view('pages.properties.create');
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_deal' => 'required|string',
            'view_deal' => 'nullable|string',
            'type_building' => 'nullable|string',
            'complex' => 'nullable|string',
            'property_type' => 'nullable|string',
            'number_rooms' => 'nullable|string',
            'condition' => 'nullable|string',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'location' => 'nullable|string',
            'address' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'title_advertising' => 'nullable|string',
            'contact_id' => 'nullable|integer',
            'agent_id' => 'nullable|integer',
        ]);

        // TODO: Implement property creation logic
        // Property::create($validated);

        return redirect()->route('properties.index')->with('success', 'Property created successfully');
    }
}
