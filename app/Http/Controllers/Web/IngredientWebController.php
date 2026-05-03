<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientWebController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::latest()->paginate(20);
        return view('ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('ingredients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'stock_quantity' => 'required|numeric',
            'unit' => 'nullable|in:gram,pcs,ml,liter',
            'min_stock' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        // Ensure price is not null when saving to DB (DB column is NOT NULL)
        if (! array_key_exists('price', $data) || is_null($data['price'])) {
            $data['price'] = 0;
        }

        Ingredient::create($data);
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient created');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'stock_quantity' => 'required|numeric',
            'unit' => 'nullable|in:gram,pcs,ml,liter',
            'min_stock' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        // Prevent setting price to NULL (database column not nullable)
        if (! array_key_exists('price', $data) || is_null($data['price'])) {
            $data['price'] = $ingredient->price ?? 0;
        }

        $ingredient->update($data);
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient updated');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('admin.ingredients.index')->with('success', 'Ingredient deleted');
    }
}
