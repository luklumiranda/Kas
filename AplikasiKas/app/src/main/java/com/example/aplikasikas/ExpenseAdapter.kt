package com.example.aplikasikas

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import java.text.NumberFormat
import java.util.*

class ExpenseAdapter(
    private var listExpense: List<Expense>,
    private val onViewClick: (Expense) -> Unit,
    private val onEditClick: (Expense) -> Unit,
    private val onDeleteClick: (Expense) -> Unit
) : RecyclerView.Adapter<ExpenseAdapter.ExpenseViewHolder>() {

    class ExpenseViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val tvDate: TextView = itemView.findViewById(R.id.tvDate)
        val tvCategory: TextView = itemView.findViewById(R.id.tvCategory)
        val tvDescription: TextView = itemView.findViewById(R.id.tvDescription)
        val tvAmount: TextView = itemView.findViewById(R.id.tvAmount)
        val tvReceipts: TextView = itemView.findViewById(R.id.tvReceipts)
        val btnView: View = itemView.findViewById(R.id.btnView)
        val btnEdit: View = itemView.findViewById(R.id.btnEdit)
        val btnDelete: View = itemView.findViewById(R.id.btnDelete)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ExpenseViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_expense, parent, false)
        return ExpenseViewHolder(view)
    }

    override fun onBindViewHolder(holder: ExpenseViewHolder, position: Int) {
        val expense = listExpense[position]
        holder.tvDate.text = expense.date
        holder.tvCategory.text = expense.category
        holder.tvDescription.text = if (expense.description.length > 50) expense.description.take(50) + "..." else expense.description
        holder.tvAmount.text = formatRupiah(expense.amount)
        holder.tvReceipts.text = "(${expense.receiptFilesCount} file)"

        holder.btnView.setOnClickListener { onViewClick(expense) }
        holder.btnEdit.setOnClickListener { onEditClick(expense) }
        holder.btnDelete.setOnClickListener { onDeleteClick(expense) }
    }

    override fun getItemCount(): Int = listExpense.size

    fun updateData(newList: List<Expense>) {
        listExpense = newList
        notifyDataSetChanged()
    }

    private fun formatRupiah(amount: Double): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace("Rp", "Rp ")
    }
}
