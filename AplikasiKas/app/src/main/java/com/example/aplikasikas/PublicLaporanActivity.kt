package com.example.aplikasikas

import android.graphics.Color
import android.os.Bundle
import android.view.Gravity
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import java.text.NumberFormat
import java.util.*

class PublicLaporanActivity : AppCompatActivity() {

    private lateinit var containerBills: LinearLayout
    private lateinit var containerExpenses: LinearLayout
    private lateinit var tvTotalIncome: TextView
    private lateinit var tvTotalExpense: TextView
    private lateinit var tvBalance: TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_public_laporan)

        containerBills = findViewById(R.id.containerBills)
        containerExpenses = findViewById(R.id.containerExpenses)
        tvTotalIncome = findViewById(R.id.tvTotalIncome)
        tvTotalExpense = findViewById(R.id.tvTotalExpense)
        tvBalance = findViewById(R.id.tvBalance)

        // Simulasi Data (Biasanya data ini diambil dari API berdasarkan Token)
        val income = 5000000.0
        val expense = 1250000.0
        val balance = income - expense

        tvTotalIncome.text = formatRupiah(income)
        tvTotalExpense.text = formatRupiah(expense)
        tvBalance.text = formatRupiah(balance)

        setupBillsTable()
        setupExpensesTable()
    }

    private fun setupBillsTable() {
        // Simulasi baris data tagihan
        addDataRow(containerBills, arrayOf("Budi Santoso", "Iuran Kas", "Rp50.000", "Lunas"), true)
        addDataRow(containerBills, arrayOf("Siti Aminah", "Iuran Kas", "Rp50.000", "Belum"), false)
        addDataRow(containerBills, arrayOf("Agus Setiawan", "Iuran Kas", "Rp50.000", "Lunas"), true)
    }

    private fun setupExpensesTable() {
        // Simulasi baris data pengeluaran
        addDataRow(containerExpenses, arrayOf("01/02/24", "Konsumsi", "Beli Snack Rapat", "Rp150.000"))
        addDataRow(containerExpenses, arrayOf("05/02/24", "ATK", "Beli Kertas A4", "Rp65.000"))
    }

    private fun addDataRow(container: LinearLayout, data: Array<String>, isPaid: Boolean? = null) {
        val row = LinearLayout(this)
        row.orientation = LinearLayout.HORIZONTAL
        row.setPadding(20, 25, 20, 25)
        row.background = getDrawable(android.R.drawable.dialog_holo_light_frame) // Optional border

        val weights = if (data.size == 4) floatArrayOf(1f, 1.2f, 1.5f, 1.2f) else floatArrayOf(1.2f, 1f, 1.2f, 1f)

        for (i in data.indices) {
            val tv = TextView(this)
            tv.layoutParams = LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.WRAP_CONTENT, weights[i])
            tv.text = data[i]
            tv.textSize = 12f
            tv.setTextColor(Color.parseColor("#333333"))

            if (i == data.size - 1 && isPaid != null) {
                // Style for Status Column
                tv.text = if (isPaid) "Terbayar" else "Belum Bayar"
                tv.setTextColor(Color.WHITE)
                tv.setPadding(8, 4, 8, 4)
                tv.gravity = Gravity.CENTER
                tv.setBackgroundColor(if (isPaid) Color.parseColor("#28a745") else Color.parseColor("#ffc107"))
            }
            row.addView(tv)
        }
        container.addView(row)

        // Divider
        val divider = View(this)
        divider.layoutParams = LinearLayout.LayoutParams(LinearLayout.LayoutParams.MATCH_PARENT, 1)
        divider.setBackgroundColor(Color.parseColor("#dee2e6"))
        container.addView(divider)
    }

    private fun formatRupiah(amount: Double): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace("Rp", "Rp ")
    }

    // Helper to convert sp to px
    private val Int.sp: Float get() = this * resources.displayMetrics.scaledDensity
}
