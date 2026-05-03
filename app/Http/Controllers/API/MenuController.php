<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return MenuResource::collection(Menu::where('is_available', true)->get());
    }

    public function store(Request $request)
    {
        $this->authorizeAction($request->user());

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
        ]);

        $menu = Menu::create($data);
        return new MenuResource($menu);
    }

    public function show(Menu $menu)
    {
        return new MenuResource($menu);
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorizeAction($request->user());

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
        ]);

        $menu->update($data);
        return new MenuResource($menu);
    }

    public function destroy(Request $request, Menu $menu)
    {
        $this->authorizeAction($request->user());
        $menu->delete();
        return response()->json(null, 204);
    }

    protected function authorizeAction($user)
    {
        if (! $user || ! in_array($user->role ?? '', ['admin', 'owner'])) {
            abort(403, 'Unauthorized');
        }
    }
}
