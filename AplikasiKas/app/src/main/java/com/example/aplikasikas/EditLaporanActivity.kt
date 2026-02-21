package com.example.aplikasikas

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.CheckBox
import android.widget.EditText
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.util.*

class EditLaporanActivity : AppCompatActivity() {

    private lateinit var etTitle: EditText
    private lateinit var etDescription: EditText
    private lateinit var etStartDate: EditText
    private lateinit var etEndDate: EditText
    private lateinit var cbIsActive: CheckBox
    private lateinit var tvPublicLink: TextView
    private lateinit var btnUpdate: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_laporan)

        etTitle = findViewById(R.id.etTitle)
        etDescription = findViewById(R.id.etDescription)
        etStartDate = findViewById(R.id.etStartDate)
        etEndDate = findViewById(R.id.etEndDate)
        cbIsActive = findViewById(R.id.cbIsActive)
        tvPublicLink = findViewById(R.id.tvPublicLink)
        btnUpdate = findViewById(R.id.btnUpdate)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)

        // Simulasi pengisian data awal (seperti $transparencyReport di PHP)
        // Jika ini dari list, data bisa dikirim lewat Intent
        etTitle.setText("Laporan Transparansi Kas Januari 2024")
        etDescription.setText("Deskripsi laporan transparansi kas...")
        etStartDate.setText("2024-01-01")
        etEndDate.setText("2024-01-31")
        cbIsActive.isChecked = true
        tvPublicLink.text = "http://aplikasikas.com/laporan-publik/token-abc-123"

        btnBack.setOnClickListener { finish() }
        btnCancel.setOnClickListener { finish() }

        etStartDate.setOnClickListener { showDatePicker(etStartDate) }
        etEndDate.setOnClickListener { showDatePicker(etEndDate) }

        btnUpdate.setOnClickListener {
            val title = etTitle.text.toString().trim()
            val startDate = etStartDate.text.toString().trim()
            val endDate = etEndDate.text.toString().trim()

            if (title.isEmpty() || startDate.isEmpty() || endDate.isEmpty()) {
                Toast.makeText(this, "Harap isi semua kolom yang wajib (*)", Toast.LENGTH_SHORT).show()
            } else {
                // Logika update data ke API atau database
                Toast.makeText(this, "Laporan berhasil diperbarui!", Toast.LENGTH_SHORT).show()
                finish()
            }
        }
    }

    private fun showDatePicker(editText: EditText) {
        val calendar = Calendar.getInstance()
        val year = calendar.get(Calendar.YEAR)
        val month = calendar.get(Calendar.MONTH)
        val day = calendar.get(Calendar.DAY_OF_MONTH)

        val datePickerDialog = DatePickerDialog(
            this,
            { _, selectedYear, selectedMonth, selectedDay ->
                val date = String.format("%d-%02d-%02d", selectedYear, selectedMonth + 1, selectedDay)
                editText.setText(date)
            },
            year,
            month,
            day
        )
        datePickerDialog.show()
    }
}
