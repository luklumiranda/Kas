package com.example.aplikasikas

data class Laporan(
    val id: String,
    val judul: String,
    val tanggalMulai: String,
    val tanggalSelesai: String,
    val status: String,
    val deskripsi: String = ""
)
