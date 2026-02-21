<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\TransparencyReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransparencyReportController extends Controller
{
    public function index()
    {
        //$this->authorize('isAdmin');
        $reports = TransparencyReport::paginate(10);
        return view('transparency.index', compact('reports'))->with('title', 'Laporan Transparansi');
    }

    public function create()
    {
        //$this->authorize('isAdmin');
        return view('transparency.create')->with('title', 'Buat Laporan Transparansi');
    }

    public function store(Request $request)
    {
        //$this->authorize('isAdmin');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['access_token'] = TransparencyReport::generateToken();

        TransparencyReport::create($validated);

        return redirect()->route('transparency.index')->with('success', 'Laporan transparansi berhasil dibuat');
    }

    public function edit(TransparencyReport $transparencyReport)
    {
        //$this->authorize('isAdmin');
        return view('transparency.edit', compact('transparencyReport'))->with('title', 'Edit Laporan Transparansi');
    }

    public function update(Request $request, TransparencyReport $transparencyReport)
    {
        //$this->authorize('isAdmin');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $transparencyReport->update($validated);

        return redirect()->route('transparency.index')->with('success', 'Laporan transparansi berhasil diperbarui');
    }

    public function destroy(TransparencyReport $transparencyReport)
    {
        //$this->authorize('isAdmin');
        $transparencyReport->delete();

        return redirect()->route('transparency.index')->with('success', 'Laporan transparansi berhasil dihapus');
    }

    /**
     * Tampilkan laporan transparansi publik
     * Accessible via token tanpa login
     */
    public function publicView($token)
    {
        $report = TransparencyReport::where('access_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $bills = Bill::whereBetween('created_at', [$report->start_date, $report->end_date])->get();
        $expenses = Expense::whereBetween('expense_date', [$report->start_date, $report->end_date])->get();

        $totalIncome = $bills->where('paid_date', '!=', null)->sum('paid_amount');
        $totalExpense = $expenses->sum('amount');

        return view('transparency.public', compact('report', 'bills', 'expenses', 'totalIncome', 'totalExpense'));
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf(Request $request)
    {
        //$this->authorize('isAdmin');

        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()));

        $bills = Bill::whereBetween('created_at', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();

        $totalIncome = $bills->where('paid_date', '!=', null)->sum('paid_amount');
        $totalExpense = $expenses->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $pdf = Pdf::loadView('reports.pdf', compact('bills', 'expenses', 'totalIncome', 'totalExpense', 'balance', 'startDate', 'endDate'));

        return $pdf->download('laporan-transparansi-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        //$this->authorize('isAdmin');

        $startDate = Carbon::parse($request->input('start_date', now()->startOfMonth()));
        $endDate = Carbon::parse($request->input('end_date', now()->endOfMonth()));

        return Excel::download(
            new \App\Exports\TransparencyReportExport($startDate, $endDate),
            'laporan-transparansi-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * API: Get all transparency reports
     */
    public function apiIndex()
    {
        try {
            $reports = TransparencyReport::orderBy('created_at', 'desc')->get();
            return response()->json(['success' => true, 'message' => 'Data berhasil diambil', 'data' => $reports]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage(), 'data' => null], 500);
        }
    }

    /**
     * API: Get single transparency report by ID
     */
    public function apiShow($id)
    {
        try {
            $report = TransparencyReport::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan', 'data' => null], 404);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil diambil', 'data' => $report]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage(), 'data' => null], 500);
        }
    }

    /**
     * API: Create new transparency report
     */
    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'is_active' => 'boolean',
            ]);

            $validated['access_token'] = TransparencyReport::generateToken();
            $report = TransparencyReport::create($validated);

            return response()->json(['success' => true, 'message' => 'Laporan berhasil dibuat', 'data' => $report], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'data' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage(), 'data' => null], 500);
        }
    }

    /**
     * API: Update transparency report
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            $report = TransparencyReport::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan', 'data' => null], 404);
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after:start_date',
                'is_active' => 'boolean',
            ]);

            $report->update($validated);

            return response()->json(['success' => true, 'message' => 'Laporan berhasil diperbarui', 'data' => $report]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'data' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage(), 'data' => null], 500);
        }
    }

    /**
     * API: Delete transparency report
     */
    public function apiDestroy($id)
    {
        try {
            $report = TransparencyReport::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan', 'data' => null], 404);
            }

            $report->delete();

            return response()->json(['success' => true, 'message' => 'Laporan berhasil dihapus', 'data' => null]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $th->getMessage(), 'data' => null], 500);
        }
    }
}
