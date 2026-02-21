package com.example.aplikasikas

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class ExpenseCategoryAdapter(
    private var listCategory: List<ExpenseCategory>,
    private val onEditClick: (ExpenseCategory) -> Unit,
    private val onDeleteClick: (ExpenseCategory) -> Unit
) : RecyclerView.Adapter<ExpenseCategoryAdapter.CategoryViewHolder>() {

    class CategoryViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val tvName: TextView = itemView.findViewById(R.id.tvCategoryName)
        val tvDescription: TextView = itemView.findViewById(R.id.tvCategoryDescription)
        val tvCount: TextView = itemView.findViewById(R.id.tvExpensesCount)
        val btnEdit: View = itemView.findViewById(R.id.btnEditCategory)
        val btnDelete: View = itemView.findViewById(R.id.btnDeleteCategory)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CategoryViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_expense_category, parent, false)
        return CategoryViewHolder(view)
    }

    override fun onBindViewHolder(holder: CategoryViewHolder, position: Int) {
        val category = listCategory[position]
        holder.tvName.text = category.name
        holder.tvDescription.text = if (category.description.length > 80) category.description.take(80) + "..." else category.description
        holder.tvCount.text = category.expensesCount.toString()

        holder.btnEdit.setOnClickListener { onEditClick(category) }
        holder.btnDelete.setOnClickListener { onDeleteClick(category) }
    }

    override fun getItemCount(): Int = listCategory.size

    fun updateData(newList: List<ExpenseCategory>) {
        listCategory = newList
        notifyDataSetChanged()
    }
}
