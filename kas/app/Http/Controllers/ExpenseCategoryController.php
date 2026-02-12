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
}
