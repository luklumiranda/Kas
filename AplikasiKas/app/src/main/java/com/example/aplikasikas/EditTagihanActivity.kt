package com.example.aplikasikas

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.ArrayAdapter
import android.widget.EditText
import android.widget.ImageView
import android.widget.Spinner
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.util.*

class EditTagihanActivity : AppCompatActivity() {

    private lateinit var spinnerStudent: Spinner
    private lateinit var etBillType: EditText
    private lateinit var etAmount: EditText
    private lateinit var etDueDate: EditText
    private lateinit var etNotes: EditText
    private lateinit var btnUpdate: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private var billToEdit: Bill? = null

    private val students = listOf(
        Student("1", "Siswa 1", "siswa1"),
        Student("2", "Siswa 2", "siswa2"),
        Student("3", "Siswa 3", "siswa3")
    )

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_tagihan)

        // Gunakan getParcelableExtra yang aman untuk berbagai versi Android
        billToEdit = intent.getParcelableExtra("EXTRA_BILL")

        if (billToEdit == null) {
            Toast.makeText(this, "Data tagihan tidak ditemukan", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        initViews()
        setupStudentSpinner()
        populateData()
        setupDatePicker()
        setupListeners()
    }

    private fun initViews() {
        spinnerStudent = findViewById(R.id.spinnerStudent)
        etBillType = findViewById(R.id.etBillType)
        etAmount = findViewById(R.id.etAmount)
        etDueDate = findViewById(R.id.etDueDate)
        etNotes = findViewById(R.id.etNotes)
        btnUpdate = findViewById(R.id.btnUpdate)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupStudentSpinner() {
        val studentNames = students.map { "${it.name} (${it.username})" }
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, studentNames)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerStudent.adapter = adapter
    }

    private fun populateData() {
        billToEdit?.let { bill ->
            etBillType.setText(bill.billType)
            etAmount.setText(bill.amount.toInt().toString())
            etDueDate.setText(bill.dueDate)
            etNotes.setText(bill.notes ?: "")

            val studentIndex = students.indexOfFirst { it.username == bill.studentId }
            if (studentIndex != -1) {
                spinnerStudent.setSelection(studentIndex)
            }
        }
    }

    private fun setupDatePicker() {
        etDueDate.setOnClickListener {
            val calendar = Calendar.getInstance()
            val dateParts = billToEdit?.dueDate?.split("-")
            var year = calendar.get(Calendar.YEAR)
            var month = calendar.get(Calendar.MONTH)
            var day = calendar.get(Calendar.DAY_OF_MONTH)

            if (dateParts?.size == 3) {
                year = dateParts[0].toInt()
                month = dateParts[1].toInt() - 1
                day = dateParts[2].toInt()
            }

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
        btnUpdate.setOnClickListener {
            updateBill()
        }

        btnCancel.setOnClickListener {
            finish()
        }

        btnBack.setOnClickListener {
            finish()
        }
    }

    private fun updateBill() {
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

        val index = TagihanActivity.billList.indexOfFirst { it.id == billToEdit?.id }
        if (index != -1) {
            val updatedBill = TagihanActivity.billList[index].apply {
                this.studentName = selectedStudent.name
                this.studentId = selectedStudent.username
                this.billType = billType
                this.amount = amount
                this.dueDate = dueDate
                this.notes = if (notes.isEmpty()) null else notes
            }
            TagihanActivity.billList[index] = updatedBill
            Toast.makeText(this, "Tagihan berhasil diperbarui!", Toast.LENGTH_SHORT).show()
            finish()
        } else {
            Toast.makeText(this, "Gagal memperbarui tagihan", Toast.LENGTH_SHORT).show()
        }
    }

    data class Student(val id: String, val name: String, val username: String)
}
