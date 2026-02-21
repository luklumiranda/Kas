package com.example.aplikasikas

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView

class StudentAdapter(
    private var listStudent: List<Student>,
    private val onEditClick: (Student) -> Unit,
    private val onDeleteClick: (Student) -> Unit
) : RecyclerView.Adapter<StudentAdapter.StudentViewHolder>() {

    class StudentViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val tvNo: TextView = itemView.findViewById(R.id.tvNo)
        val tvName: TextView = itemView.findViewById(R.id.tvStudentName)
        val tvUsername: TextView = itemView.findViewById(R.id.tvUsername)
        val tvRole: TextView = itemView.findViewById(R.id.tvRole)
        val btnEdit: View = itemView.findViewById(R.id.btnEdit)
        val btnDelete: View = itemView.findViewById(R.id.btnDelete)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StudentViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_student, parent, false)
        return StudentViewHolder(view)
    }

    override fun onBindViewHolder(holder: StudentViewHolder, position: Int) {
        val student = listStudent[position]
        holder.tvNo.text = (position + 1).toString()
        holder.tvName.text = student.name
        holder.tvUsername.text = student.username
        holder.tvRole.text = student.role

        holder.btnEdit.setOnClickListener { onEditClick(student) }
        holder.btnDelete.setOnClickListener { onDeleteClick(student) }
    }

    override fun getItemCount(): Int = listStudent.size

    fun updateData(newList: List<Student>) {
        listStudent = newList
        notifyDataSetChanged()
    }
}
