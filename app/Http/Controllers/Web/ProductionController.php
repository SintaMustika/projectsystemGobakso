<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Production;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $productions = Production::with('menu')->latest()->paginate(20);
        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        $menus = Menu::orderBy('name')->get();
        return view('productions.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'qty' => 'required|integer|min:1',
            'production_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data) {
            $menu = Menu::findOrFail($data['menu_id']);

            // fetch recipes
            $recipes = $menu->recipes()->get();
            if ($recipes->isEmpty()) {
                throw new \Exception('Menu belum memiliki resep bahan');
            }

            // calculate total needs per ingredient
            $needs = [];
            foreach ($recipes as $r) {
                $ingId = $r->ingredient_id;
                $need = bcmul((string)$r->qty_usage, (string)$data['qty'], 3);
                if (isset($needs[$ingId])) {
                    $needs[$ingId] = bcadd($needs[$ingId], $need, 3);
                } else {
                    $needs[$ingId] = $need;
                }
            }

            // lock ingredients
            $ingredients = Ingredient::whereIn('id', array_keys($needs))->lockForUpdate()->get()->keyBy('id');

            // validate stock
            foreach ($needs as $ingId => $need) {
                $ingredient = $ingredients->get($ingId);
                if (! $ingredient) throw new \Exception('Bahan tidak ditemukan');
                if (bccomp((string)$ingredient->stock_quantity, (string)$need, 3) < 0) {
                    throw new \Exception("Stok bahan tidak mencukupi: {$ingredient->item_name}");
                }
            }

            // deduct ingredient stock
            foreach ($needs as $ingId => $need) {
                $ingredient = $ingredients->get($ingId);
                $ingredient->stock_quantity = bcsub((string)$ingredient->stock_quantity, (string)$need, 3);
                $ingredient->save();
            }

            // increase menu stock
            $menu->stock = (int)$menu->stock + (int)$data['qty'];
            $menu->save();

            Production::create([
                'menu_id' => $menu->id,
                'qty' => $data['qty'],
                'production_date' => $data['production_date'] ?? now()->toDateString(),
            ]);
        });

        return redirect()->route('admin.productions.index')->with('success', 'Produksi berhasil dan stok diperbarui');
    }
}
