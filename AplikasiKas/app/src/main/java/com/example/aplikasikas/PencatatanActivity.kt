package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

class PencatatanActivity : BaseActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var expenseAdapter: ExpenseAdapter

    companion object {
        val expenseList = mutableListOf<Expense>()

        init {
            if (expenseList.isEmpty()) {
                expenseList.add(Expense("1", "20/05/2024", "Konsumsi", "Makan siang rapat panitia", 150000.0, notes = "Rapat di kantin", receiptFilesCount = 1))
                expenseList.add(Expense("2", "21/05/2024", "Logistik", "Pembelian kertas HVS A4", 55000.0, receiptFilesCount = 0))
                expenseList.add(Expense("3", "22/05/2024", "Transport", "Bensin antar undangan", 25000.0, receiptFilesCount = 1))
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_pencatatan)

        recyclerView = findViewById(R.id.recyclerView)
        recyclerView.layoutManager = LinearLayoutManager(this)

        setupAdapter()

        val btnAddNew: Button = findViewById(R.id.btnAddNew)
        btnAddNew.text = "Pengeluaran Baru"
        btnAddNew.setOnClickListener {
            val intent = Intent(this, AddExpenseActivity::class.java)
            startActivity(intent)
        }
    }

    override fun onResume() {
        super.onResume()
        expenseAdapter.updateData(ArrayList(expenseList))
    }

    private fun setupAdapter() {
        expenseAdapter = ExpenseAdapter(ArrayList(expenseList),
            onViewClick = { expense ->
                // Membuka halaman Detail Pengeluaran
                val intent = Intent(this, ExpenseDetailActivity::class.java)
                intent.putExtra("EXTRA_EXPENSE", expense)
                startActivity(intent)
            },
            onEditClick = { expense ->
                val intent = Intent(this, EditExpenseActivity::class.java)
                intent.putExtra("EXTRA_EXPENSE", expense)
                startActivity(intent)
            },
            onDeleteClick = { expense ->
                showDeleteConfirmation(expense)
            }
        )
        recyclerView.adapter = expenseAdapter
    }

    private fun showDeleteConfirmation(expense: Expense) {
        AlertDialog.Builder(this)
            .setTitle("Hapus Pengeluaran")
            .setMessage("Yakin ingin menghapus pengeluaran senilai Rp ${expense.amount}?")
            .setPositiveButton("Hapus") { _, _ ->
                expenseList.removeAll { it.id == expense.id }
                expenseAdapter.updateData(ArrayList(expenseList))
                Toast.makeText(this, "Pengeluaran berhasil dihapus", Toast.LENGTH_SHORT).show()
            }
            .setNegativeButton("Batal", null)
            .show()
    }
}
