<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['category', 'user', 'receipts'])
            ->orderBy('expense_date', 'desc')
            ->paginate(15);

        return view('expenses.index', compact('expenses'))->with('title', 'Pencatatan Pengeluaran');
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        return view('expenses.create', compact('categories'))->with('title', 'Input Pengeluaran Baru');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
            'receipts' => 'required|array|min:1',
            'receipts.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $validated['created_by'] = auth()->id();
        
        // Remove receipts from validated data before creating expense
        $receipts = $request->file('receipts');
        $validated = collect($validated)->except(['receipts'])->toArray();

        $expense = Expense::create($validated);

        // Handle multiple receipt uploads
        if ($receipts) {
            foreach ($receipts as $file) {
                $path = $file->store('expenses', 'public');
                ExpenseReceipt::create([
                    'expense_id' => $expense->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'categories'))->with('title', 'Edit Pengeluaran');
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
            'receipts' => 'nullable|array',
            'receipts.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Remove receipts from validated data before updating expense
        $receipts = $request->file('receipts');
        $validated = collect($validated)->except(['receipts'])->toArray();

        $expense->update($validated);

        // Handle new receipt uploads
        if ($receipts) {
            foreach ($receipts as $file) {
                $path = $file->store('expenses', 'public');
                ExpenseReceipt::create([
                    'expense_id' => $expense->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil diperbarui');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'receipts']);
        return view('expenses.show', compact('expense'))->with('title', 'Detail Pengeluaran');
    }

    public function destroy(Expense $expense)
    {
        // Delete receipts
        foreach ($expense->receipts as $receipt) {
            Storage::disk('public')->delete($receipt->file_path);
            $receipt->delete();
        }
        
        $expense->delete();
        return redirect()->route('expense.index')->with('success', 'Pengeluaran berhasil dihapus');
    }

    public function deleteReceipt(ExpenseReceipt $receipt)
    {
        Storage::disk('public')->delete($receipt->file_path);
        $receipt->delete();

        return redirect()->back()->with('success', 'Bukti berhasil dihapus');
    }

    // Laporan pengeluaran
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $expenses = Expense::with(['category', 'user'])
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalByCategory = Expense::with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->groupBy('category.name')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        $totalExpense = $expenses->sum('amount');

        return view('expenses.report', compact('expenses', 'totalByCategory', 'totalExpense', 'startDate', 'endDate'))->with('title', 'Laporan Pengeluaran');
    }

    /**
     * API: Get all expenses
     */
    public function apiIndex()
    {
        try {
            $expenses = Expense::with(['category', 'user:id,name,username'])
                ->orderBy('expense_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $expenses
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
     * API: Get single expense
     */
    public function apiShow($id)
    {
        try {
            $expense = Expense::with(['category', 'user', 'receipts'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $expense
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan',
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
     * API: Create expense
     */
    public function apiStore(Request $request)
    {
        try {
            $data = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'description' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            $data['created_by'] = auth()->id();
            $expense = Expense::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil dibuat',
                'data' => $expense->load('category')
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
     * API: Update expense
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $expense = Expense::findOrFail($id);

            $data = $request->validate([
                'expense_category_id' => 'sometimes|exists:expense_categories,id',
                'description' => 'sometimes|string|max:255',
                'amount' => 'sometimes|numeric|min:0',
                'expense_date' => 'sometimes|date',
                'notes' => 'nullable|string',
            ]);

            $expense->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil diperbarui',
                'data' => $expense
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan',
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
     * API: Delete expense
     */
    public function apiDestroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            
            // Delete receipts
            foreach ($expense->receipts as $receipt) {
                Storage::disk('public')->delete($receipt->file_path);
                $receipt->delete();
            }
            
            $expense->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil dihapus',
                'data' => null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Pengeluaran tidak ditemukan',
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
