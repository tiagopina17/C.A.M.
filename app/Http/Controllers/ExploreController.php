<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    /**
     * Display a listing of stores and categories for exploration.
     */
    public function index(Request $request): View
    {
        $categories = Category::withCount('services')->orderBy('name')->get();

        $selectedCategory = $request->query('categoria');
        $searchQuery = $request->query('q');

        $storesQuery = Store::with(['services.category'])->latest();

        if (! empty($searchQuery)) {
            $storesQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        if (! empty($selectedCategory)) {
            $storesQuery->whereHas('services.category', function ($query) use ($selectedCategory) {
                $query->where('slug', $selectedCategory)
                    ->orWhere('name', $selectedCategory);
            });
        }

        $stores = $storesQuery->paginate(18)->withQueryString();

        return view('explore', [
            'categories' => $categories,
            'stores' => $stores,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
        ]);
    }
}
