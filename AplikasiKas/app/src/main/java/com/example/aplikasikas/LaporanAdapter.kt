package com.example.aplikasikas

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton

class LaporanAdapter(
    private var listLaporan: List<Laporan>,
    private val onEditClick: (Laporan) -> Unit,
    private val onDeleteClick: (Laporan) -> Unit,
    private val onLihatClick: (Laporan) -> Unit
) : RecyclerView.Adapter<LaporanAdapter.LaporanViewHolder>() {

    class LaporanViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        val tvJudulLaporan: TextView = itemView.findViewById(R.id.tvJudulLaporan)
        val tvPeriode: TextView = itemView.findViewById(R.id.tvPeriode)
        val tvStatus: TextView = itemView.findViewById(R.id.tvStatus)
        val btnEdit: MaterialButton = itemView.findViewById(R.id.btnEdit)
        val btnDelete: MaterialButton = itemView.findViewById(R.id.btnDelete)
        val btnLihat: MaterialButton = itemView.findViewById(R.id.btnLihat)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): LaporanViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_laporan, parent, false)
        return LaporanViewHolder(view)
    }

    override fun onBindViewHolder(holder: LaporanViewHolder, position: Int) {
        val laporan = listLaporan[position]
        holder.tvJudulLaporan.text = laporan.judul
        holder.tvPeriode.text = "${laporan.tanggalMulai} - ${laporan.tanggalSelesai}"
        holder.tvStatus.text = laporan.status

        holder.btnEdit.setOnClickListener { onEditClick(laporan) }
        holder.btnDelete.setOnClickListener { onDeleteClick(laporan) }
        holder.btnLihat.setOnClickListener { onLihatClick(laporan) }
    }

    override fun getItemCount(): Int = listLaporan.size

    fun updateData(newList: List<Laporan>) {
        listLaporan = newList
        notifyDataSetChanged()
    }
}
