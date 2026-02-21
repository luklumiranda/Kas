package com.example.aplikasikas

import android.os.Bundle
import android.util.Log
import android.widget.Toast

class DashboardActivity : BaseActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        try {
            setContentView(R.layout.activity_dashboard)
        } catch (e: Exception) {
            Log.e("DashboardActivity", "Error in onCreate", e)
            Toast.makeText(this, "Critical error: " + e.message, Toast.LENGTH_LONG).show()
            finish()
        }
    }
}
