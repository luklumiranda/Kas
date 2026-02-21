package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.google.android.material.button.MaterialButton
import java.text.NumberFormat
import java.util.*

class ExpenseDetailActivity : AppCompatActivity() {

    private lateinit var tvCategory: TextView
    private lateinit var tvDescription: TextView
    private lateinit var tvDate: TextView
    private lateinit var tvAmount: TextView
    private lateinit var tvRecordedBy: TextView
    private lateinit var tvInputDate: TextView
    private lateinit var tvNotes: TextView
    private lateinit var layoutNotes: LinearLayout
    private lateinit var tvReceiptTitle: TextView
    private lateinit var tvNoReceipts: TextView
    private lateinit var layoutReceiptsGrid: LinearLayout
    private lateinit var btnEdit: MaterialButton
    private lateinit var btnBack: MaterialButton
    private lateinit var btnBackHeader: ImageView

    private var expense: Expense? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_expense_detail)

        expense = intent.getParcelableExtra("EXTRA_EXPENSE")

        if (expense == null) {
            Toast.makeText(this, "Data pengeluaran tidak ditemukan", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        initViews()
        displayData()
        setupListeners()
    }

    private fun initViews() {
        tvCategory = findViewById(R.id.tvDetailCategory)
        tvDescription = findViewById(R.id.tvDetailDescription)
        tvDate = findViewById(R.id.tvDetailDate)
        tvAmount = findViewById(R.id.tvDetailAmount)
        tvRecordedBy = findViewById(R.id.tvDetailRecordedBy)
        tvInputDate = findViewById(R.id.tvDetailInputDate)
        tvNotes = findViewById(R.id.tvDetailNotes)
        layoutNotes = findViewById(R.id.layoutNotes)
        tvReceiptTitle = findViewById(R.id.tvReceiptTitle)
        tvNoReceipts = findViewById(R.id.tvNoReceipts)
        layoutReceiptsGrid = findViewById(R.id.layoutReceiptsGrid)
        btnEdit = findViewById(R.id.btnEditExpense)
        btnBack = findViewById(R.id.btnBackToList)
        btnBackHeader = findViewById(R.id.btnBack)
    }

    private fun displayData() {
        expense?.let { exp ->
            tvCategory.text = exp.category
            tvDescription.text = exp.description
            tvDate.text = exp.date
            tvAmount.text = formatRupiah(exp.amount)
            tvRecordedBy.text = exp.recordedBy
            tvInputDate.text = exp.inputDate

            if (exp.notes.isNullOrEmpty()) {
                layoutNotes.visibility = View.GONE
            } else {
                layoutNotes.visibility = View.VISIBLE
                tvNotes.text = exp.notes
            }

            tvReceiptTitle.text = "Bukti Nota (${exp.receiptFilesCount} file)"
            if (exp.receiptFilesCount > 0) {
                tvNoReceipts.visibility = View.GONE
                layoutReceiptsGrid.visibility = View.VISIBLE
                // Di sini bisa ditambahkan logika untuk menampilkan list gambar asli
            } else {
                tvNoReceipts.visibility = View.VISIBLE
                layoutReceiptsGrid.visibility = View.GONE
            }
        }
    }

    private fun setupListeners() {
        btnEdit.setOnClickListener {
            val intent = Intent(this, EditExpenseActivity::class.java)
            intent.putExtra("EXTRA_EXPENSE", expense)
            startActivity(intent)
            finish() // Tutup detail saat ke halaman edit
        }

        btnBack.setOnClickListener { finish() }
        btnBackHeader.setOnClickListener { finish() }
    }

    private fun formatRupiah(amount: Double): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace("Rp", "Rp ")
    }
}
