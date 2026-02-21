<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('customer')
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('bills.index', compact('bills'))->with('title', 'Manajemen Tagihan');
    }

    public function create()
    {
        $customers = Customer::all();
        $students = User::where('role', 'collector')->get();
        return view('bills.create', compact('customers', 'students'))->with('title', 'Buat Tagihan Baru');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'bill_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Bill::create($validated);

        return redirect()->route('bill.index')->with('success', 'Tagihan berhasil dibuat');
    }

    public function edit(Bill $bill)
    {
        $customers = Customer::all();
        $students = User::where('role', 'collector')->get();
        return view('bills.edit', compact('bill', 'customers', 'students'))->with('title', 'Edit Tagihan');
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'bill_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $bill->update($validated);

        return redirect()->route('bill.index')->with('success', 'Tagihan berhasil diperbarui');
    }

    public function markAsPaid(Bill $bill, Request $request)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . $bill->amount,
        ]);

        $bill->update([
            'paid_amount' => $validated['paid_amount'],
            'paid_date' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil dicatat sebagai terbayar');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('bill.index')->with('success', 'Tagihan berhasil dihapus');
    }

    // Generate tagihan rutin untuk semua siswa aktif
    public function generateRoutineBills(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'bill_type' => 'required|string',
            'due_date' => 'required|date',
        ]);

        $customers = Customer::where('is_active', true)->get();
        $count = 0;

        foreach ($customers as $customer) {
            Bill::create([
                'customer_id' => $customer->id,
                'amount' => $validated['amount'],
                'bill_type' => $validated['bill_type'],
                'due_date' => $validated['due_date'],
            ]);
            $count++;
        }

        return redirect()->route('bill.index')->with('success', "Tagihan berhasil dibuat untuk {$count} siswa");
    }

    // Laporan tagihan
    public function report()
    {
        $pendingBills = Bill::pending()->sum('amount');
        $paidBills = Bill::paid()->sum('paid_amount');
        $overdueBills = Bill::overdue()->sum('amount');
        $bills = Bill::with('customer')
            ->orderBy('due_date', 'desc')
            ->get();

        return view('bills.report', compact('pendingBills', 'paidBills', 'overdueBills', 'bills'))->with('title', 'Laporan Tagihan');
    }

    /**
     * API: Get all bills
     */
    public function apiIndex()
    {
        try {
            $bills = Bill::with('customer:id,name,username')
                ->orderBy('due_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $bills
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
     * API: Get single bill
     */
    public function apiShow($id)
    {
        try {
            $bill = Bill::with('customer')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => $bill
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan',
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
     * API: Create bill
     */
    public function apiStore(Request $request)
    {
        try {
            $data = $request->validate([
                'customer_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'bill_type' => 'required|string',
                'notes' => 'nullable|string',
            ]);

            $bill = Bill::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dibuat',
                'data' => $bill->load('customer')
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
     * API: Update bill
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $bill = Bill::findOrFail($id);

            $data = $request->validate([
                'amount' => 'sometimes|numeric|min:0',
                'due_date' => 'sometimes|date',
                'bill_type' => 'sometimes|string',
                'notes' => 'nullable|string',
            ]);

            $bill->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil diperbarui',
                'data' => $bill
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan',
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
     * API: Delete bill
     */
    public function apiDestroy($id)
    {
        try {
            $bill = Bill::findOrFail($id);
            $bill->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dihapus',
                'data' => null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $th) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan',
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
