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

class AddStudentActivity : AppCompatActivity() {

    private lateinit var etStudentName: EditText
    private lateinit var etUsername: EditText
    private lateinit var etPassword: EditText
    private lateinit var spinnerRole: Spinner
    private lateinit var etJoinedAt: EditText
    private lateinit var spinnerGender: Spinner
    private lateinit var etBirth: EditText
    private lateinit var etPhone: EditText
    private lateinit var etAddress: EditText
    private lateinit var etLastEducation: EditText
    private lateinit var ivPreview: ImageView
    private lateinit var btnChoosePhoto: MaterialButton
    private lateinit var btnSave: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private val roles = listOf("Siswa", "Bendahara")
    private val genders = listOf("Laki-laki", "Perempuan")

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_student)

        initViews()
        setupSpinners()
        setupDatePickers()
        setupListeners()
    }

    private fun initViews() {
        etStudentName = findViewById(R.id.etStudentName)
        etUsername = findViewById(R.id.etUsername)
        etPassword = findViewById(R.id.etPassword)
        spinnerRole = findViewById(R.id.spinnerRole)
        etJoinedAt = findViewById(R.id.etJoinedAt)
        spinnerGender = findViewById(R.id.spinnerGender)
        etBirth = findViewById(R.id.etBirth)
        etPhone = findViewById(R.id.etPhone)
        etAddress = findViewById(R.id.etAddress)
        etLastEducation = findViewById(R.id.etLastEducation)
        ivPreview = findViewById(R.id.ivPreview)
        btnChoosePhoto = findViewById(R.id.btnChoosePhoto)
        btnSave = findViewById(R.id.btnSave)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupSpinners() {
        val roleAdapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, roles)
        roleAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerRole.adapter = roleAdapter

        val genderAdapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, genders)
        genderAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        spinnerGender.adapter = genderAdapter
    }

    private fun setupDatePickers() {
        etJoinedAt.setOnClickListener { showDatePicker(etJoinedAt) }
        etBirth.setOnClickListener { showDatePicker(etBirth) }
    }

    private fun showDatePicker(editText: EditText) {
        val calendar = Calendar.getInstance()
        val datePickerDialog = DatePickerDialog(
            this,
            { _, year, month, day ->
                val date = String.format("%04d-%02d-%02d", year, month + 1, day)
                editText.setText(date)
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )
        datePickerDialog.show()
    }

    private fun setupListeners() {
        btnChoosePhoto.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK)
            intent.type = "image/*"
            startActivityForResult(intent, 100)
        }

        btnSave.setOnClickListener { saveStudent() }
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

    private fun saveStudent() {
        val name = etStudentName.text.toString().trim()
        val username = etUsername.text.toString().trim()
        val password = etPassword.text.toString().trim()
        val role = roles[spinnerRole.selectedItemPosition]
        val gender = genders[spinnerGender.selectedItemPosition]
        val birth = etBirth.text.toString().trim()
        val address = etAddress.text.toString().trim()
        val phone = etPhone.text.toString().trim()
        val lastEducation = etLastEducation.text.toString().trim()
        val joinedAt = etJoinedAt.text.toString().trim()

        if (name.isEmpty() || username.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Harap isi semua bidang yang bertanda *", Toast.LENGTH_SHORT).show()
            return
        }

        val newStudent = Student(
            name = name,
            username = username,
            password = password,
            role = role,
            gender = gender,
            birth = birth,
            address = address,
            phone = phone,
            lastEducation = lastEducation
        )

        // Simpan ke API
        RetrofitClient.instance.addStudent(newStudent).enqueue(object : Callback<ApiResponse<Student>> {
            override fun onResponse(call: Call<ApiResponse<Student>>, response: Response<ApiResponse<Student>>) {
                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(this@AddStudentActivity, "Siswa berhasil disimpan ke API!", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    val errorMsg = response.body()?.message ?: "Gagal menyimpan ke server"
                    Toast.makeText(this@AddStudentActivity, errorMsg, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ApiResponse<Student>>, t: Throwable) {
                Toast.makeText(this@AddStudentActivity, "Error Koneksi: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
