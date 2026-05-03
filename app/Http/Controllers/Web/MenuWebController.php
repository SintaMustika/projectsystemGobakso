<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuWebController extends Controller
{
    public function index()
    {
        $menus = Menu::with('recipes.ingredient')->latest()->paginate(15);
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);
        // Normalize checkbox (present when checked)
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        // Handle uploaded image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image'] = basename($path);
        }

        Menu::create($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu created');
    }

    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);
        $data['is_available'] = $request->has('is_available') ? 1 : 0;

        // If new image uploaded, store and remove old
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            // delete old image if exists
            if ($menu->image) {
                Storage::disk('public')->delete('menus/' . $menu->image);
            }
            $data['image'] = basename($path);
        }

        $menu->update($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu updated');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted');
    }

    /**
     * Show recipe management for a menu
     */
    public function recipe($id)
    {
        $menu = Menu::with('recipes.ingredient')->findOrFail($id);
        $ingredients = Ingredient::orderBy('item_name')->get();
        return view('menus.recipe', compact('menu', 'ingredients'));
    }

    /**
     * Store a recipe (ingredient) for a menu
     */
    public function storeRecipe(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'qty_usage' => 'required|numeric|min:0.0001',
        ]);

        // prevent duplicate ingredient for the same menu
        $exists = Recipe::where('menu_id', $menu->id)->where('ingredient_id', $data['ingredient_id'])->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Ingredient already added to this menu');
        }

        Recipe::create([
            'menu_id' => $menu->id,
            'ingredient_id' => $data['ingredient_id'],
            'qty_usage' => $data['qty_usage'],
        ]);

        return redirect()->route('admin.menus.recipe', $menu->id)->with('success', 'Ingredient added to recipe');
    }

    /**
     * Update recipe quantity
     */
    public function updateRecipe(Request $request, $menuId, $recipeId)
    {
        $menu = Menu::findOrFail($menuId);
        $recipe = Recipe::where('menu_id', $menu->id)->where('id', $recipeId)->firstOrFail();

        $data = $request->validate([
            'qty_usage' => 'required|numeric|min:0.0001',
        ]);

        $recipe->qty_usage = $data['qty_usage'];
        $recipe->save();

        return redirect()->route('admin.menus.recipe', $menu->id)->with('success', 'Recipe updated');
    }

    /**
     * Delete recipe item
     */
    public function deleteRecipe($menuId, $recipeId)
    {
        $menu = Menu::findOrFail($menuId);
        $recipe = Recipe::where('menu_id', $menu->id)->where('id', $recipeId)->firstOrFail();
        $recipe->delete();
        return redirect()->route('admin.menus.recipe', $menu->id)->with('success', 'Ingredient removed from recipe');
    }
}
