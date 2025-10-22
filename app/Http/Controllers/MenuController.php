<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Item;

class MenuController extends Controller
{
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/category'), $filename);
            $data['image'] = $filename;
        }

        Category::create($data);

        return redirect()->route('admin.menu')->with('success', 'Category created');
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/items'), $filename);
            $data['image'] = $filename;
        }

        Item::create($data);

        return redirect()->route('admin.menu')->with('success', 'Item created');
    }

    public function destroyCategory(Request $request, $id)
    {
        $category = Category::with('items')->find($id);
        if (!$category) {
            return redirect()->route('admin.menu')->with('error', 'Category not found');
        }

        if ($category->items()->count() > 0) {
            return redirect()->route('admin.menu')->with('error', 'Cannot delete category that has items');
        }

        // delete category image if exists
        if ($category->image && file_exists(public_path('uploads/category/' . $category->image))) {
            unlink(public_path('uploads/category/' . $category->image));
        }

        $category->delete();
        return redirect()->route('admin.menu')->with('success', 'Category deleted');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('admin.menu')->with('error', 'Category not found');
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            // remove old image
            if ($category->image && file_exists(public_path('uploads/category/' . $category->image))) {
                unlink(public_path('uploads/category/' . $category->image));
            }
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/category'), $filename);
            $data['image'] = $filename;
        }

        $category->update($data);

        return redirect()->route('admin.menu')->with('success', 'Category updated');
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $item = Item::find($id);
        if (!$item) {
            return redirect()->route('admin.menu')->with('error', 'Item not found');
        }

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($item->image && file_exists(public_path('uploads/items/' . $item->image))) {
                unlink(public_path('uploads/items/' . $item->image));
            }
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/items'), $filename);
            $data['image'] = $filename;
        }

        $item->update($data);

        return redirect()->route('admin.menu')->with('success', 'Item updated');
    }

    public function editCategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('admin.menu')->with('error', 'Category not found');
        }
        return view('category_edit', compact('category'));
    }

    public function editItem($id)
    {
        $item = Item::with('category')->find($id);
        $categories = Category::orderBy('name')->get();
        if (!$item) {
            return redirect()->route('admin.menu')->with('error', 'Item not found');
        }
        return view('item_edit', compact('item', 'categories'));
    }

    public function destroyItem(Request $request, $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return redirect()->route('admin.menu')->with('error', 'Item not found');
        }
        if ($item->image && file_exists(public_path('uploads/items/' . $item->image))) {
            unlink(public_path('uploads/items/' . $item->image));
        }
        $item->delete();
        return redirect()->route('admin.menu')->with('success', 'Item deleted');
    }
}
