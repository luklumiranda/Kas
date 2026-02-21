package com.example.aplikasikas

import android.os.Bundle
import android.widget.EditText
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton

class EditExpenseCategoryActivity : AppCompatActivity() {

    private lateinit var etCategoryName: EditText
    private lateinit var etCategoryDescription: EditText
    private lateinit var btnUpdate: MaterialButton
    private lateinit var btnCancel: MaterialButton
    private lateinit var btnBack: ImageView

    private var categoryToEdit: ExpenseCategory? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_expense_category)

        categoryToEdit = intent.getParcelableExtra("EXTRA_CATEGORY")

        if (categoryToEdit == null) {
            Toast.makeText(this, "Data kategori tidak ditemukan", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        initViews()
        populateData()
        setupListeners()
    }

    private fun initViews() {
        etCategoryName = findViewById(R.id.etCategoryName)
        etCategoryDescription = findViewById(R.id.etCategoryDescription)
        btnUpdate = findViewById(R.id.btnUpdate)
        btnCancel = findViewById(R.id.btnCancel)
        btnBack = findViewById(R.id.btnBack)
    }

    private fun populateData() {
        categoryToEdit?.let { category ->
            etCategoryName.setText(category.name)
            etCategoryDescription.setText(category.description)
        }
    }

    private fun setupListeners() {
        btnUpdate.setOnClickListener {
            updateCategory()
        }

        btnCancel.setOnClickListener { finish() }
        btnBack.setOnClickListener { finish() }
    }

    private fun updateCategory() {
        val name = etCategoryName.text.toString().trim()
        val description = etCategoryDescription.text.toString().trim()

        if (name.isEmpty()) {
            Toast.makeText(this, "Nama kategori wajib diisi", Toast.LENGTH_SHORT).show()
            return
        }

        val index = KategoriActivity.categoryList.indexOfFirst { it.id == categoryToEdit?.id }
        if (index != -1) {
            KategoriActivity.categoryList[index].apply {
                this.name = name
                this.description = description
            }
            Toast.makeText(this, "Kategori berhasil diperbarui!", Toast.LENGTH_SHORT).show()
            finish()
        } else {
            Toast.makeText(this, "Gagal memperbarui kategori", Toast.LENGTH_SHORT).show()
        }
    }
}
