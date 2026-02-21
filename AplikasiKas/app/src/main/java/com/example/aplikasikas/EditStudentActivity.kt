package com.example.aplikasikas

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.widget.ArrayAdapter
import android.widget.EditText
import android.widget.ImageView
import android.widget.Spinner
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.util.*

class EditStudentActivity : AppCompatActivity() {

    private lateinit var etStudentName: EditText
    private lateinit var etUsername: EditText
    private lateinit var etRole: EditText
    private lateinit var etJoinedAt: EditText
    private lateinit var spinnerGender: Spinner
    private lateinit var etBirth: EditText
    private lateinit var etPhone: EditText
    private lateinit var etAddress: EditText
    private lateinit var etLastEducation: EditText
    private lateinit var ivPreview: ImageView
    private lateinit var btnChoosePhoto: MaterialButton
    private lateinit var btnUpdate: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private var studentToEdit: Student? = null
    private val genders = listOf("Laki-laki", "Perempuan")

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_student)

        studentToEdit = intent.getParcelableExtra("EXTRA_STUDENT")

        if (studentToEdit == null) {
            Toast.makeText(this, "Data siswa tidak ditemukan", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        initViews()
        setupSpinner()
        populateData()
        setupListeners()
    }

    private fun initViews() {
        etStudentName = findViewById(R.id.etStudentName)
        etUsername = findViewById(R.id.etUsername)
        etRole = findViewById(R.id.etRole)
        etJoinedAt = findViewById(R.id.etJoinedAt)
        spinnerGender = findViewById(R.id.spinnerGender)
        etBirth = findViewById(R.id.etBirth)
        etPhone = findViewById(R.id.etPhone)
        etAddress = findViewById(R.id.etAddress)
        etLastEducation = findViewById(R.id.etLastEducation)
        ivPreview = findViewById(R.id.ivPreview)
        btnChoosePhoto = findViewById(R.id.btnChoosePhoto)
        btnUpdate = findViewById(R.id.btnUpdate)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupSpinner() {
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, genders)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerGender.adapter = adapter
    }

    private fun populateData() {
        studentToEdit?.let { student ->
            etStudentName.setText(student.name)
            etUsername.setText(student.username)
            etRole.setText(student.role?.uppercase() ?: "")
            etBirth.setText(student.birth ?: "")
            etPhone.setText(student.phone ?: "")
            etAddress.setText(student.address ?: "")
            etLastEducation.setText(student.lastEducation ?: "")

            val genderIndex = if (student.gender == "P") 1 else 0
            spinnerGender.setSelection(genderIndex)
        }
    }

    private fun setupListeners() {
        etBirth.setOnClickListener {
            val calendar = Calendar.getInstance()
            val datePickerDialog = DatePickerDialog(
                this,
                { _, year, month, day ->
                    val date = String.format("%04d-%02d-%02d", year, month + 1, day)
                    etBirth.setText(date)
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
            )
            datePickerDialog.show()
        }

        btnChoosePhoto.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK)
            intent.type = "image/*"
            startActivityForResult(intent, 100)
        }

        btnUpdate.setOnClickListener { updateStudent() }
        btnCancel.setOnClickListener { finish() }
        btnBack.setOnClickListener { finish() }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
            val imageUri: Uri? = data?.data
            ivPreview.setImageURI(imageUri)
        }
    }

    private fun updateStudent() {
        val name = etStudentName.text.toString().trim()
        val gender = if (spinnerGender.selectedItemPosition == 1) "P" else "L"
        val birth = etBirth.text.toString().trim()
        val phone = etPhone.text.toString().trim()
        val address = etAddress.text.toString().trim()
        val lastEducation = etLastEducation.text.toString().trim()

        if (name.isEmpty()) {
            Toast.makeText(this, "Nama wajib diisi", Toast.LENGTH_SHORT).show()
            return
        }

        val updatedStudent = studentToEdit?.copy(
            name = name,
            gender = gender,
            birth = birth,
            phone = phone,
            address = address,
            lastEducation = lastEducation
        )

        if (updatedStudent != null && updatedStudent.id != null) {
            RetrofitClient.instance.updateStudent(updatedStudent.id, updatedStudent)
                .enqueue(object : Callback<ApiResponse<Student>> {
                    override fun onResponse(call: Call<ApiResponse<Student>>, response: Response<ApiResponse<Student>>) {
                        if (response.isSuccessful) {
                            Toast.makeText(this@EditStudentActivity, "Data berhasil diperbarui!", Toast.LENGTH_SHORT).show()
                            finish()
                        } else {
                            Toast.makeText(this@EditStudentActivity, "Gagal memperbarui data", Toast.LENGTH_SHORT).show()
                        }
                    }

                    override fun onFailure(call: Call<ApiResponse<Student>>, t: Throwable) {
                        Toast.makeText(this@EditStudentActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                    }
                })
        } else {
            Toast.makeText(this, "ID Siswa tidak valid", Toast.LENGTH_SHORT).show()
        }
    }
}
