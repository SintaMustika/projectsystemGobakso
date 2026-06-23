<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('details.ingredient')->latest()->paginate(20);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('item_name')->get();
        return view('purchases.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($data) {
            // generate invoice
            $invoice = 'PB-'.date('Ymd').Str::upper(Str::random(5));

            $purchase = Purchase::create([
                'invoice' => $invoice,
                'supplier' => $data['supplier'],
                'total_price' => 0,
                'purchase_date' => $data['purchase_date'],
            ]);

            $total = 0;
            foreach ($data['items'] as $it) {
                $qty = (float) $it['qty'];
                $unitPrice = (float) $it['unit_price'];
                $subtotal = $qty * $unitPrice;

                $detail = PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'ingredient_id' => $it['ingredient_id'],
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // update ingredient stock
                $ingredient = Ingredient::find($it['ingredient_id']);
                if ($ingredient) {
                    $ingredient->stock_quantity = $ingredient->stock_quantity + $qty;
                    $ingredient->save();
                }

                $total += $subtotal;
            }

            $purchase->total_price = $total;
            $purchase->save();
        });

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase saved and stock updated');
    }
}
