package com.example.aplikasikas

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import java.text.NumberFormat
import java.util.*

class BillAdapter(
    private var listBill: List<Bill>,
    private val onPayClick: (Bill) -> Unit,
    private val onEditClick: (Bill) -> Unit,
    private val onDeleteClick: (Bill) -> Unit
) : RecyclerView.Adapter<BillAdapter.BillViewHolder>() {

    class BillViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val tvStudentName: TextView = itemView.findViewById(R.id.tvStudentName)
        val tvStudentId: TextView = itemView.findViewById(R.id.tvStudentId)
        val tvBillType: TextView = itemView.findViewById(R.id.tvBillType)
        val tvAmount: TextView = itemView.findViewById(R.id.tvAmount)
        val tvDueDate: TextView = itemView.findViewById(R.id.tvDueDate)
        val tvStatus: TextView = itemView.findViewById(R.id.tvStatus)
        val btnPay: MaterialButton = itemView.findViewById(R.id.btnPay)
        val btnEdit: MaterialButton = itemView.findViewById(R.id.btnEdit)
        val btnDelete: MaterialButton = itemView.findViewById(R.id.btnDelete)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): BillViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_bill, parent, false)
        return BillViewHolder(view)
    }

    override fun onBindViewHolder(holder: BillViewHolder, position: Int) {
        val bill = listBill[position]
        holder.tvStudentName.text = bill.studentName
        holder.tvStudentId.text = bill.studentId
        holder.tvBillType.text = bill.billType
        holder.tvAmount.text = formatRupiah(bill.amount)
        holder.tvDueDate.text = "Jatuh Tempo: ${bill.dueDate}"
        holder.tvStatus.text = bill.status

        // Set status color
        when (bill.status.lowercase()) {
            "terbayar" -> {
                holder.tvStatus.setBackgroundColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_green_dark))
                holder.btnPay.visibility = View.GONE
            }
            "overdue" -> {
                holder.tvStatus.setBackgroundColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_red_dark))
                holder.btnPay.visibility = View.VISIBLE
            }
            else -> { // Pending
                holder.tvStatus.setBackgroundColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_orange_dark))
                holder.btnPay.visibility = View.VISIBLE
            }
        }

        holder.btnPay.setOnClickListener { onPayClick(bill) }
        holder.btnEdit.setOnClickListener { onEditClick(bill) }
        holder.btnDelete.setOnClickListener { onDeleteClick(bill) }
    }

    override fun getItemCount(): Int = listBill.size

    fun updateData(newList: List<Bill>) {
        listBill = newList
        notifyDataSetChanged()
    }

    private fun formatRupiah(amount: Double): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace("Rp", "Rp ")
    }
}
