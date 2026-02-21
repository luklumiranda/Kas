<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        // Widget Saldo Real-time
        $totalIncome = Bill::where('paid_date', '!=', null)->sum('paid_amount');
        $totalExpense = Expense::sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Bulan ini
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        $monthlyIncome = Bill::where('paid_date', '!=', null)
            ->whereBetween('paid_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('paid_amount');

        $monthlyExpense = Expense::whereBetween('expense_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        // Status Tunggakan
        $totalDebt = Bill::pending()->sum('amount');
        $overdueDebt = Bill::overdue()->sum('amount');
        $overdueCount = Bill::overdue()->count();

        // Grafik arus kas (last 12 months)
        $cashFlowData = $this->getCashFlowData();

        // Top debtors
        $topDebtors = Customer::withSum('bills', 'amount')
            ->having('bills_sum_amount', '>', 0)
            ->orderBy('bills_sum_amount', 'desc')
            ->limit(5)
            ->get();

        // Expense by category
        $expenseByCategory = $this->getExpenseByCategory();

        // Recent transactions
        $recentBills = Bill::with('customer')->latest('created_at')->limit(5)->get();
        $recentExpenses = Expense::with('category')->latest('created_at')->limit(5)->get();

        return view('dashboard.index', compact(
            'balance',
            'totalIncome',
            'totalExpense',
            'monthlyIncome',
            'monthlyExpense',
            'totalDebt',
            'overdueDebt',
            'overdueCount',
            'cashFlowData',
            'topDebtors',
            'expenseByCategory',
            'recentBills',
            'recentExpenses'
        ))->with('title', 'Dashboard - Ringkasan Keuangan');
    }

    /**
     * Get cash flow data untuk grafik (12 bulan terakhir)
     */
    private function getCashFlowData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();

            $income = Bill::where('paid_date', '!=', null)
                ->whereBetween('paid_date', [$monthStart, $monthEnd])
                ->sum('paid_amount');

            $expense = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $data[] = [
                'month' => $date->format('M'),
                'income' => $income,
                'expense' => $expense,
            ];
        }

        return collect($data);
    }

    /**
     * Get expense breakdown by category
     */
    private function getExpenseByCategory()
    {
        return Expense::with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($items) {
                return $items->sum('amount');
            });
    }
}
