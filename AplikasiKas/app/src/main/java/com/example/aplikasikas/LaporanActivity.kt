package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.widget.Toast
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton

class LaporanActivity : BaseActivity() {

    private lateinit var rvLaporan: RecyclerView
    private lateinit var adapter: LaporanAdapter
    private val listLaporan = mutableListOf<Laporan>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_laporan)

        rvLaporan = findViewById(R.id.rvLaporan)
        rvLaporan.layoutManager = LinearLayoutManager(this)

        // Data dummy awal
        listLaporan.add(Laporan("1", "Laporan Januari 2024", "2024-01-01", "2024-01-31", "Aktif"))
        
        adapter = LaporanAdapter(listLaporan, 
            onEditClick = { laporan ->
                val intent = Intent(this, EditLaporanActivity::class.java)
                startActivity(intent)
            },
            onDeleteClick = { laporan ->
                listLaporan.remove(laporan)
                adapter.updateData(listLaporan.toList())
                Toast.makeText(this, "Laporan dihapus", Toast.LENGTH_SHORT).show()
            },
            onLihatClick = { laporan ->
                val intent = Intent(this, PublicLaporanActivity::class.java)
                startActivity(intent)
            }
        )
        rvLaporan.adapter = adapter

        // Hubungkan tombol Laporan Baru
        val btnLaporanBaru = findViewById<MaterialButton>(R.id.btnLaporanBaru)
        btnLaporanBaru.setOnClickListener {
            val intent = Intent(this, AddLaporanActivity::class.java)
            startActivityForResult(intent, REQUEST_CODE_ADD_LAPORAN)
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == REQUEST_CODE_ADD_LAPORAN && resultCode == RESULT_OK) {
            val judul = data?.getStringExtra("judul") ?: ""
            val tglMulai = data?.getStringExtra("tglMulai") ?: ""
            val tglSelesai = data?.getStringExtra("tglSelesai") ?: ""
            
            val newLaporan = Laporan(
                id = (listLaporan.size + 1).toString(),
                judul = judul,
                tanggalMulai = tglMulai,
                tanggalSelesai = tglSelesai,
                status = "Aktif"
            )
            
            listLaporan.add(0, newLaporan) // Tambah di paling atas
            adapter.updateData(listLaporan.toList())
        }
    }

    companion object {
        const val REQUEST_CODE_ADD_LAPORAN = 100
    }
}
