package com.example.aplikasikas

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity

class RegisterActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register)

        // Temukan komponen UI
        val etFullname = findViewById<EditText>(R.id.etFullname)
        val etEmail = findViewById<EditText>(R.id.etEmail)
        val etPassReg = findViewById<EditText>(R.id.etPassReg)
        val btnRegister = findViewById<Button>(R.id.btnRegister)
        val tvLoginLink = findViewById<TextView>(R.id.tvLoginLink)

        // Aksi klik tombol Register
        btnRegister.setOnClickListener {
            val username = etFullname.text.toString().trim() // Menggunakan etFullname sebagai username
            val email = etEmail.text.toString().trim()
            val password = etPassReg.text.toString().trim()

            if (username.isEmpty() || email.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Harap isi semua bidang", Toast.LENGTH_SHORT).show()
            } else {
                // Simpan data ke SharedPreferences agar bisa digunakan di Login
                val sharedPref = getSharedPreferences("UserPrefs", Context.MODE_PRIVATE)
                val editor = sharedPref.edit()
                editor.putString("registeredUsername", username)
                editor.putString("registeredPassword", password)
                editor.apply()

                // Simulasi registrasi sukses
                Toast.makeText(this, "Registrasi Berhasil!", Toast.LENGTH_SHORT).show()
                
                // Pindah ke MainActivity (Login) setelah daftar
                val intent = Intent(this, MainActivity::class.java)
                startActivity(intent)
                finish()
            }
        }

        // Aksi klik untuk kembali ke Login
        tvLoginLink.setOnClickListener {
            val intent = Intent(this, MainActivity::class.java)
            startActivity(intent)
            finish()
        }
    }
}