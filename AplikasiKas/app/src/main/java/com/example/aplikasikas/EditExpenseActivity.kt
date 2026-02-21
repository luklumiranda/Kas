package com.example.aplikasikas

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.provider.OpenableColumns
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.Spinner
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.util.*

class EditExpenseActivity : AppCompatActivity() {

    private lateinit var spinnerCategory: Spinner
    private lateinit var etDescription: EditText
    private lateinit var etAmount: EditText
    private lateinit var etDate: EditText
    private lateinit var etNotes: EditText
    private lateinit var tvFileName: TextView
    private lateinit var btnUpload: Button
    private lateinit var btnUpdate: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private var expenseToEdit: Expense? = null
    private var selectedFileUri: Uri? = null

    private val categories = listOf("Konsumsi", "Logistik", "Transport", "Lain-lain")

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_expense)

        expenseToEdit = intent.getParcelableExtra("EXTRA_EXPENSE")

        if (expenseToEdit == null) {
            Toast.makeText(this, "Data pengeluaran tidak ditemukan", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        initViews()
        setupCategorySpinner()
        populateData()
        setupDatePicker()
        setupListeners()
    }

    private fun initViews() {
        spinnerCategory = findViewById(R.id.spinnerCategory)
        etDescription = findViewById(R.id.etDescription)
        etAmount = findViewById(R.id.etAmount)
        etDate = findViewById(R.id.etDate)
        etNotes = findViewById(R.id.etNotes)
        tvFileName = findViewById(R.id.tvFileName)
        btnUpload = findViewById(R.id.btnUpload)
        btnUpdate = findViewById(R.id.btnUpdate)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupCategorySpinner() {
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, categories)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerCategory.adapter = adapter
    }

    private fun populateData() {
        expenseToEdit?.let { expense ->
            etDescription.setText(expense.description)
            etAmount.setText(expense.amount.toInt().toString())
            
            // Konversi dd/MM/yyyy ke yyyy-MM-dd untuk input date
            val dateParts = expense.date.split("/")
            if (dateParts.size == 3) {
                etDate.setText("${dateParts[2]}-${dateParts[1]}-${dateParts[0]}")
            }
            
            etNotes.setText(expense.notes ?: "")

            val categoryIndex = categories.indexOf(expense.category)
            if (categoryIndex != -1) {
                spinnerCategory.setSelection(categoryIndex)
            }
            
            tvFileName.text = "Bukti ada (${expense.receiptFilesCount} file)"
        }
    }

    private fun setupDatePicker() {
        etDate.setOnClickListener {
            val calendar = Calendar.getInstance()
            val dateParts = etDate.text.toString().split("-")
            var year = calendar.get(Calendar.YEAR)
            var month = calendar.get(Calendar.MONTH)
            var day = calendar.get(Calendar.DAY_OF_MONTH)

            if (dateParts.size == 3) {
                year = dateParts[0].toInt()
                month = dateParts[1].toInt() - 1
                day = dateParts[2].toInt()
            }

            val datePickerDialog = DatePickerDialog(
                this,
                { _, selectedYear, selectedMonth, selectedDay ->
                    val date = String.format("%04d-%02d-%02d", selectedYear, selectedMonth + 1, selectedDay)
                    etDate.setText(date)
                },
                year,
                month,
                day
            )
            datePickerDialog.show()
        }
    }

    private fun setupListeners() {
        btnUpload.setOnClickListener {
            val intent = Intent(Intent.ACTION_GET_CONTENT)
            intent.type = "*/*"
            intent.putExtra(Intent.EXTRA_MIME_TYPES, arrayOf("image/*", "application/pdf"))
            startActivityForResult(intent, 100)
        }

        btnUpdate.setOnClickListener {
            updateExpense()
        }

        btnCancel.setOnClickListener { finish() }
        btnBack.setOnClickListener { finish() }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
            selectedFileUri = data?.data
            selectedFileUri?.let { uri ->
                tvFileName.text = getFileName(uri)
            }
        }
    }

    private fun getFileName(uri: Uri): String {
        var result: String? = null
        if (uri.scheme == "content") {
            val cursor = contentResolver.query(uri, null, null, null, null)
            cursor.use {
                if (it != null && it.moveToFirst()) {
                    val index = it.getColumnIndex(OpenableColumns.DISPLAY_NAME)
                    if (index != -1) result = it.getString(index)
                }
            }
        }
        if (result == null) {
            result = uri.path
            val cut = result?.lastIndexOf('/')
            if (cut != -1) {
                if (result != null) {
                    result = result?.substring(cut!! + 1)
                }
            }
        }
        return result ?: "File terpilih"
    }

    private fun updateExpense() {
        val category = categories[spinnerCategory.selectedItemPosition]
        val description = etDescription.text.toString().trim()
        val amountStr = etAmount.text.toString().trim()
        val date = etDate.text.toString().trim()
        val notes = etNotes.text.toString().trim()

        if (description.isEmpty() || amountStr.isEmpty() || date.isEmpty()) {
            Toast.makeText(this, "Harap isi semua bidang yang bertanda *", Toast.LENGTH_SHORT).show()
            return
        }

        val amount = amountStr.toDoubleOrNull() ?: 0.0

        val index = PencatatanActivity.expenseList.indexOfFirst { it.id == expenseToEdit?.id }
        if (index != -1) {
            PencatatanActivity.expenseList[index].apply {
                this.date = date.split("-").reversed().joinToString("/")
                this.category = category
                this.description = description
                this.amount = amount
                this.notes = if (notes.isEmpty()) null else notes
                if (selectedFileUri != null) {
                    this.receiptFilesCount = 1 // Simulasi ganti file
                }
            }
            Toast.makeText(this, "Pengeluaran berhasil diperbarui!", Toast.LENGTH_SHORT).show()
            finish()
        } else {
            Toast.makeText(this, "Gagal memperbarui pengeluaran", Toast.LENGTH_SHORT).show()
        }
    }
}
