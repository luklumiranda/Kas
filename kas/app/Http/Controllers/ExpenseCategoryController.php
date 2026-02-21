<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->paginate(15);
        return view('expense-categories.index', compact('categories'))->with('title', 'Kategori Pengeluaran');
    }

    public function create()
    {
        return view('expense-categories.create')->with('title', 'Tambah Kategori Pengeluaran')->with('title', 'Tambah Kategori Pengeluaran');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil ditambahkan');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('expense-categories.edit', compact('expenseCategory'))->with('title', 'Edit Kategori Pengeluaran')->with('title', 'Edit Kategori Pengeluaran');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil diperbarui');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki pengeluaran');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-category.index')->with('success', 'Kategori pengeluaran berhasil dihapus');
    }

    /**
     * API: Get all expense categories
     */
    public function apiIndex()
    {
        try {
            $categories = ExpenseCategory::withCount('expenses')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $categories
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Get single category
     */
    public function apiShow($id)
    {
        try {
            $category = ExpenseCategory::withCount('expenses')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $category
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Create category
     */
    public function apiStore(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:expense_categories',
                'description' => 'nullable|string',
            ]);

            $category = ExpenseCategory::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dibuat',
                'data' => $category
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Update category
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);

            $data = $request->validate([
                'name' => 'sometimes|string|max:255|unique:expense_categories,name,' . $id,
                'description' => 'nullable|string',
            ]);

            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * API: Delete category
     */
    public function apiDestroy($id)
    {
        try {
            $category = ExpenseCategory::findOrFail($id);

            if ($category->expenses()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menghapus kategori yang masih memiliki pengeluaran',
                    'data' => null
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
                'data' => null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data' => null
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $th->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
