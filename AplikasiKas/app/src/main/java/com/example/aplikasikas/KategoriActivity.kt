package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView

class KategoriActivity : BaseActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var categoryAdapter: ExpenseCategoryAdapter

    companion object {
        // Simulasi database statis untuk kategori pengeluaran
        val categoryList = mutableListOf<ExpenseCategory>()

        init {
            if (categoryList.isEmpty()) {
                categoryList.add(ExpenseCategory("1", "Konsumsi", "Segala pengeluaran terkait makan dan minum", 12))
                categoryList.add(ExpenseCategory("2", "Logistik", "Pengadaan barang dan perlengkapan operasional", 5))
                categoryList.add(ExpenseCategory("3", "Transport", "BIAYA perjalanan, bensin, dan akomodasi", 8))
                categoryList.add(ExpenseCategory("4", "Lain-lain", "Pengeluaran tak terduga lainnya", 2))
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_kategori)

        // Update toolbar title
        findViewById<TextView>(R.id.tvToolbarTitle).text = "Kategori Pengeluaran"
        findViewById<TextView>(R.id.tvPageTitle).text = "Kategori Pengeluaran"

        recyclerView = findViewById(R.id.recyclerView)
        recyclerView.layoutManager = LinearLayoutManager(this)

        setupAdapter()

        val btnAddNew: Button = findViewById(R.id.btnAddNew)
        btnAddNew.text = "Kategori Baru"
        btnAddNew.setOnClickListener {
            // Memperbaiki: Sekarang benar-benar membuka halaman AddExpenseCategoryActivity
            val intent = Intent(this, AddExpenseCategoryActivity::class.java)
            startActivity(intent)
        }
    }

    override fun onResume() {
        super.onResume()
        categoryAdapter.updateData(ArrayList(categoryList))
    }

    private fun setupAdapter() {
        categoryAdapter = ExpenseCategoryAdapter(ArrayList(categoryList),
            onEditClick = { category ->
                // Memperbaiki: Sekarang benar-benar membuka halaman EditExpenseCategoryActivity
                val intent = Intent(this, EditExpenseCategoryActivity::class.java)
                intent.putExtra("EXTRA_CATEGORY", category)
                startActivity(intent)
            },
            onDeleteClick = { category ->
                showDeleteConfirmation(category)
            }
        )
        recyclerView.adapter = categoryAdapter
    }

    private fun showDeleteConfirmation(category: ExpenseCategory) {
        AlertDialog.Builder(this)
            .setTitle("Hapus Kategori")
            .setMessage("Yakin ingin menghapus kategori '${category.name}'? Ini mungkin berdampak pada data pengeluaran terkait.")
            .setPositiveButton("Hapus") { _, _ ->
                categoryList.removeAll { it.id == category.id }
                categoryAdapter.updateData(ArrayList(categoryList))
                Toast.makeText(this, "Kategori berhasil dihapus", Toast.LENGTH_SHORT).show()
            }
            .setNegativeButton("Batal", null)
            .show()
    }
}
