package com.example.aplikasikas

import android.os.Bundle
import android.widget.EditText
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.util.*

class AddExpenseCategoryActivity : AppCompatActivity() {

    private lateinit var etCategoryName: EditText
    private lateinit var etCategoryDescription: EditText
    private lateinit var btnSave: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_expense_category)

        initViews()
        setupListeners()
    }

    private fun initViews() {
        etCategoryName = findViewById(R.id.etCategoryName)
        etCategoryDescription = findViewById(R.id.etCategoryDescription)
        btnSave = findViewById(R.id.btnSave)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun setupListeners() {
        btnSave.setOnClickListener {
            saveCategory()
        }

        btnCancel.setOnClickListener { finish() }
        btnBack.setOnClickListener { finish() }
    }

    private fun saveCategory() {
        val name = etCategoryName.text.toString().trim()
        val description = etCategoryDescription.text.toString().trim()

        if (name.isEmpty()) {
            Toast.makeText(this, "Nama kategori wajib diisi", Toast.LENGTH_SHORT).show()
            return
        }

        val newCategory = ExpenseCategory(
            id = UUID.randomUUID().toString(),
            name = name,
            description = description,
            expensesCount = 0
        )

        KategoriActivity.categoryList.add(newCategory)

        Toast.makeText(this, "Kategori berhasil disimpan!", Toast.LENGTH_SHORT).show()
        finish()
    }
}
