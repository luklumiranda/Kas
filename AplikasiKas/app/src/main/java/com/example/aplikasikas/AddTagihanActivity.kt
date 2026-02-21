package com.example.aplikasikas

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.View
import android.widget.ArrayAdapter
import android.widget.EditText
import android.widget.ImageView
import android.widget.Spinner
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.util.*

class AddTagihanActivity : AppCompatActivity() {

    private lateinit var spinnerStudent: Spinner
    private lateinit var etBillType: EditText
    private lateinit var etAmount: EditText
    private lateinit var etDueDate: EditText
    private lateinit var etNotes: EditText
    private lateinit var btnSave: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private val students = listOf(
        Student("1", "Siswa 1", "siswa1"),
        Student("2", "Siswa 2", "siswa2"),
        Student("3", "Siswa 3", "siswa3")
    )

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_tagihan)

        initViews()
        setupStudentSpinner()
        setupDatePicker()
        setupListeners()
    }

    private fun initViews() {
        spinnerStudent = findViewById(R.id.spinnerStudent)
        etBillType = findViewById(R.id.etBillType)
        etAmount = findViewById(R.id.etAmount)
        etDueDate = findViewById(R.id.etDueDate)
        etNotes = findViewById(R.id.etNotes)
        btnSave = findViewById(R.id.btnSave)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupStudentSpinner() {
        val studentNames = students.map { "${it.name} (${it.username})" }
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, studentNames)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerStudent.adapter = adapter
    }

    private fun setupDatePicker() {
        etDueDate.setOnClickListener {
            val calendar = Calendar.getInstance()
            val year = calendar.get(Calendar.YEAR)
            val month = calendar.get(Calendar.MONTH)
            val day = calendar.get(Calendar.DAY_OF_MONTH)

            val datePickerDialog = DatePickerDialog(
                this,
                { _, selectedYear, selectedMonth, selectedDay ->
                    val date = String.format("%04d-%02d-%02d", selectedYear, selectedMonth + 1, selectedDay)
                    etDueDate.setText(date)
                },
                year,
                month,
                day
            )
            datePickerDialog.show()
        }
    }

    private fun setupListeners() {
        btnSave.setOnClickListener {
            saveBill()
        }

        btnCancel.setOnClickListener {
            finish()
        }

        btnBack.setOnClickListener {
            finish()
        }
    }

    private fun saveBill() {
        val selectedStudent = students[spinnerStudent.selectedItemPosition]
        val billType = etBillType.text.toString().trim()
        val amountStr = etAmount.text.toString().trim()
        val dueDate = etDueDate.text.toString().trim()
        val notes = etNotes.text.toString().trim()

        if (billType.isEmpty() || amountStr.isEmpty() || dueDate.isEmpty()) {
            Toast.makeText(this, "Harap isi semua bidang yang bertanda *", Toast.LENGTH_SHORT).show()
            return
        }

        val amount = amountStr.toDoubleOrNull() ?: 0.0

        val newBill = Bill(
            id = UUID.randomUUID().toString(),
            studentName = selectedStudent.name,
            studentId = selectedStudent.username,
            billType = billType,
            amount = amount,
            dueDate = dueDate,
            status = "Pending",
            notes = if (notes.isEmpty()) null else notes
        )

        TagihanActivity.billList.add(newBill)

        Toast.makeText(this, "Tagihan berhasil disimpan!", Toast.LENGTH_SHORT).show()
        finish()
    }

    data class Student(val id: String, val name: String, val username: String)
}
