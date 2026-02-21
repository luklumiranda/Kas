package com.example.aplikasikas

import android.os.Parcelable
import kotlinx.parcelize.Parcelize

@Parcelize
data class Expense(
    val id: String,
    var date: String,
    var category: String,
    var description: String,
    var amount: Double,
    var notes: String? = null,
    var receiptFilesCount: Int = 0,
    var recordedBy: String = "Admin",
    var inputDate: String = "20/05/2024 10:00"
) : Parcelable
