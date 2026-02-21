package com.example.aplikasikas

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val etUsername = findViewById<EditText>(R.id.etUsername)
        val etPassword = findViewById<EditText>(R.id.etPassword)
        val btnLogin = findViewById<Button>(R.id.btnLogin)
        val tvRegister = findViewById<TextView>(R.id.tvRegisterLink)

        val sharedPref = getSharedPreferences("UserPrefs", Context.MODE_PRIVATE)

        btnLogin.setOnClickListener {
            val username = etUsername.text.toString().trim()
            val password = etPassword.text.toString().trim()

            if (username.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Username dan Password tidak boleh kosong", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            // Kirim dengan "username" field (dari users table)
            // atau bisa juga pakai "number" untuk compatibility dengan customers table
            val credentials = mapOf(
                "username" to username, 
                "password" to password
            )

            Log.d("LOGIN_DEBUG", "Mencoba login dengan: $credentials")

            RetrofitClient.instance.login(credentials).enqueue(object : Callback<ApiResponse<UserResponse>> {
                override fun onResponse(call: Call<ApiResponse<UserResponse>>, response: Response<ApiResponse<UserResponse>>) {
                    val body = response.body()
                    if (response.isSuccessful && body?.success == true) {
                        val user = body.data?.user
                        val token = body.data?.token

                        val editor = sharedPref.edit()
                        editor.putString("user_name", user?.name)
                        editor.putString("user_role", user?.role)
                        editor.putString("user_token", token)
                        editor.apply()

                        Toast.makeText(this@MainActivity, "Login Berhasil!", Toast.LENGTH_SHORT).show()
                        startActivity(Intent(this@MainActivity, DashboardActivity::class.java))
                        finish()
                    } else {
                        // Jika gagal, tampilkan pesan dari server (jika ada)
                        val errorMsg = body?.message ?: "Gagal: Kredensial tidak cocok"
                        Toast.makeText(this@MainActivity, errorMsg, Toast.LENGTH_LONG).show()
                        Log.e("LOGIN_ERROR", "Response Code: ${response.code()} Body: ${response.errorBody()?.string()}")
                    }
                }

                override fun onFailure(call: Call<ApiResponse<UserResponse>>, t: Throwable) {
                    Toast.makeText(this@MainActivity, "Masalah Jaringan: ${t.message}", Toast.LENGTH_LONG).show()
                }
            })
        }

        tvRegister.setOnClickListener {
            startActivity(Intent(this, RegisterActivity::class.java))
        }
    }
}
