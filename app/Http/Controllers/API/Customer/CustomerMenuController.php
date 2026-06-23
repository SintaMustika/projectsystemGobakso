<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Menu;

class CustomerMenuController extends Controller
{
    public function index()
    {
        $menus = Menu::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'image' => $this->imageUrl($menu->image),
                    'image_url' => $this->imageUrl($menu->image),
                    'is_available' => $menu->is_available,
                    'stock' => $menu->stock,
                ];
            });

        return response()->json($menus);
    }

    private function imageUrl(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        $path = str_starts_with($image, 'menus/')
            ? $image
            : 'menus/' . ltrim($image, '/');

        return asset('storage/' . $path);
    }
}
