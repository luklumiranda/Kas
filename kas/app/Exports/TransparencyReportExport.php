<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransparencyReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Deskripsi',
            'Pemasukan',
            'Pengeluaran',
            'Status',
        ];
    }

    public function collection()
    {
        $bills = Bill::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($bill) {
                return [
                    'Tanggal' => $bill->due_date->format('d/m/Y'),
                    'Tipe' => 'Tagihan',
                    'Deskripsi' => $bill->bill_type . ' - ' . $bill->customer->name,
                    'Pemasukan' => $bill->paid_date ? $bill->paid_amount : 0,
                    'Pengeluaran' => 0,
                    'Status' => $bill->isPaid() ? 'Terbayar' : 'Belum Bayar',
                ];
            });

        $expenses = Expense::whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($expense) {
                return [
                    'Tanggal' => $expense->expense_date->format('d/m/Y'),
                    'Tipe' => 'Pengeluaran',
                    'Deskripsi' => $expense->category->name . ' - ' . $expense->description,
                    'Pemasukan' => 0,
                    'Pengeluaran' => $expense->amount,
                    'Status' => 'Tercatat',
                ];
            });

        return $bills->merge($expenses)->sortBy('Tanggal');
    }
}
