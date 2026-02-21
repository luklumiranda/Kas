package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton

class TagihanActivity : BaseActivity() {

    private lateinit var rvTagihan: RecyclerView
    private lateinit var billAdapter: BillAdapter

    companion object {
        // Menggunakan static list agar data tersimpan selama aplikasi berjalan (simulasi database)
        val billList = mutableListOf<Bill>()
        
        init {
            if (billList.isEmpty()) {
                billList.add(Bill("1", "Siswa 1", "siswa1", "Kas Bulanan", 50000.0, "2024-05-30", "Pending"))
                billList.add(Bill("2", "Siswa 2", "siswa2", "Uang Buku", 150000.0, "2024-06-10", "Terbayar", paidAmount = 150000.0, paidDate = "2024-05-20"))
                billList.add(Bill("3", "Siswa 3", "siswa3", "Sumbangan", 25000.0, "2024-04-20", "Overdue"))
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_tagihan)

        rvTagihan = findViewById(R.id.rvTagihan)
        rvTagihan.layoutManager = LinearLayoutManager(this)

        setupAdapter()

        val btnTagihanBaru: MaterialButton = findViewById(R.id.btnTagihanBaru)
        btnTagihanBaru.setOnClickListener {
            // Tombol langsung menuju halaman form tagihan baru
            val intent = Intent(this, AddTagihanActivity::class.java)
            startActivity(intent)
        }
    }

    override fun onResume() {
        super.onResume()
        // Refresh data saat kembali dari halaman tambah/edit
        billAdapter.updateData(ArrayList(billList))
    }

    private fun setupAdapter() {
        billAdapter = BillAdapter(ArrayList(billList),
            onPayClick = { bill ->
                Toast.makeText(this, "Fitur Pembayaran untuk ${bill.billType}", Toast.LENGTH_SHORT).show()
            },
            onEditClick = { bill ->
                // Tombol Edit berfungsi menuju halaman edit dengan data yang sesuai
                val intent = Intent(this, EditTagihanActivity::class.java)
                intent.putExtra("EXTRA_BILL", bill)
                startActivity(intent)
            },
            onDeleteClick = { bill ->
                // Tombol Delete berfungsi dengan konfirmasi
                showDeleteConfirmation(bill)
            }
        )
        rvTagihan.adapter = billAdapter
    }

    private fun showDeleteConfirmation(bill: Bill) {
        AlertDialog.Builder(this)
            .setTitle("Hapus Tagihan")
            .setMessage("Apakah Anda yakin ingin menghapus tagihan ${bill.billType} untuk ${bill.studentName}?")
            .setPositiveButton("Hapus") { _, _ ->
                billList.removeAll { it.id == bill.id }
                billAdapter.updateData(ArrayList(billList))
                Toast.makeText(this, "Tagihan berhasil dihapus", Toast.LENGTH_SHORT).show()
            }
            .setNegativeButton("Batal", null)
            .show()
    }
}
