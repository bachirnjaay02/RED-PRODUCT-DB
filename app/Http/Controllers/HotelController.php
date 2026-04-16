<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    // Récupérer tous les hôtels pour ton dashboard
    public function index()
    {
        $hotels = Hotel::all()->map(function ($hotel) {
            // Pour les hôtels uploadés via formulaire, l'image contient déjà "hotels/"
            $imagePath = $hotel->image;
            if (!str_contains($imagePath, 'hotels/')) {
                $imagePath = 'hotels/' . $imagePath;       // Adapter pour les hôtels importés depuis le fichier JSON
            }
            
            return [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'location' => $hotel->address, // Adapter pour le frontend
                'price' => $hotel->price . ' ' . $hotel->currency,
                'image' => asset('storage/' . $imagePath), // Génère une URL complète pour l'image
                'email' => $hotel->email,
                'phone' => $hotel->phone,
            ];
        });

        return response()->json($hotels);
    }

    // Enregistrer un nouvel hôtel via le formulaire
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'image' => 'required|string', // image sous forme de base64 ou URL, à adapter selon ton frontend
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public');
            $validated['image'] = $path;
        }

        $hotel = Hotel::create($validated);

        return response()->json([
            'message' => 'Hôtel ajouté avec succès !',
            'hotel' => $hotel
        ], 201);
    }
}