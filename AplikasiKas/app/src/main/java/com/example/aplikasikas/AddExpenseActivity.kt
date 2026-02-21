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
import java.text.SimpleDateFormat
import java.util.*

class AddExpenseActivity : AppCompatActivity() {

    private lateinit var spinnerCategory: Spinner
    private lateinit var etDescription: EditText
    private lateinit var etAmount: EditText
    private lateinit var etDate: EditText
    private lateinit var etNotes: EditText
    private lateinit var tvFileName: TextView
    private lateinit var btnUpload: Button
    private lateinit var btnSave: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private var selectedFileUri: Uri? = null

    private val categories = listOf("Konsumsi", "Logistik", "Transport", "Lain-lain")

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_expense)

        initViews()
        setupCategorySpinner()
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
        btnSave = findViewById(R.id.btnSave)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)

        // Set default date to today
        val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        etDate.setText(sdf.format(Date()))
    }

    private fun setupCategorySpinner() {
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, categories)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerCategory.adapter = adapter
    }

    private fun setupDatePicker() {
        etDate.setOnClickListener {
            val calendar = Calendar.getInstance()
            val year = calendar.get(Calendar.YEAR)
            val month = calendar.get(Calendar.MONTH)
            val day = calendar.get(Calendar.DAY_OF_MONTH)

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

        btnSave.setOnClickListener {
            saveExpense()
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

    private fun saveExpense() {
        val category = categories[spinnerCategory.selectedItemPosition]
        val description = etDescription.text.toString().trim()
        val amountStr = etAmount.text.toString().trim()
        val date = etDate.text.toString().trim()

        if (description.isEmpty() || amountStr.isEmpty() || date.isEmpty() || selectedFileUri == null) {
            Toast.makeText(this, "Harap isi semua bidang dan upload bukti nota", Toast.LENGTH_SHORT).show()
            return
        }

        val amount = amountStr.toDoubleOrNull() ?: 0.0

        val newExpense = Expense(
            id = UUID.randomUUID().toString(),
            date = date.split("-").reversed().joinToString("/"), // Format dd/MM/yyyy
            category = category,
            description = description,
            amount = amount,
            receiptFilesCount = 1
        )

        PencatatanActivity.expenseList.add(0, newExpense)

        Toast.makeText(this, "Pengeluaran berhasil disimpan!", Toast.LENGTH_SHORT).show()
        finish()
    }
}
