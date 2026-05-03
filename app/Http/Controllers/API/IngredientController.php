<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        return Ingredient::all();
    }

    public function store(Request $request)
    {
        $this->authorizeAction($request->user());

        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'stock_quantity' => 'required|numeric',
            'unit' => 'nullable|string|max:50',
            'min_stock' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        if (! array_key_exists('price', $data) || is_null($data['price'])) {
            $data['price'] = 0;
        }

        $ingredient = Ingredient::create($data);
        return response()->json($ingredient, 201);
    }

    public function show(Ingredient $ingredient)
    {
        return $ingredient;
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorizeAction($request->user());

        $data = $request->validate([
            'item_name' => 'sometimes|string|max:255',
            'stock_quantity' => 'sometimes|numeric',
            'unit' => 'nullable|string|max:50',
            'min_stock' => 'nullable|numeric',
            'price' => 'nullable|numeric',
        ]);

        if (array_key_exists('price', $data) && is_null($data['price'])) {
            // if client explicitly set price to null, keep existing price
            unset($data['price']);
        }

        $ingredient->update($data);
        return response()->json($ingredient);
    }

    public function destroy(Request $request, Ingredient $ingredient)
    {
        $this->authorizeAction($request->user());
        $ingredient->delete();
        return response()->json(null, 204);
    }

    protected function authorizeAction($user)
    {
        if (! $user || ! in_array($user->role ?? '', ['admin', 'owner'])) {
            abort(403, 'Unauthorized');
        }
    }
}
