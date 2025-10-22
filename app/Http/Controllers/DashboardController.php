<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get authenticated admin
        $admin = \App\Models\Admin::find(session('admin_id'));

        // Get search query
        $search = $request->get('search');
        $category = $request->get('category');

        // Build query
        $query = Item::query()->with('category');

        // Apply search filter (search by name, description, and category name)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply category filter
        if ($category && $category !== 'all') {
            $query->where('category_id', $category);
        }

        // Get paginated items (15 per page)
        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all categories for filter
        $categories = Category::all();

        // Get statistics
        $totalProducts = Item::count();

        return view('admins.dash', compact('products', 'categories', 'totalProducts', 'admin'));
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // Delete image if exists
        if ($item->image) {
            $imagePath = storage_path('app/public/' . $item->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $item->delete();

        return redirect()->back()->with('success', 'Product deleted successfully');
    }
}
