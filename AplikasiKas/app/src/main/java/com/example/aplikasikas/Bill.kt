package com.example.aplikasikas

import android.os.Parcelable
import kotlinx.parcelize.Parcelize

@Parcelize
data class Bill(
    val id: String,
    var studentName: String,
    var studentId: String,
    var billType: String,
    var amount: Double,
    var dueDate: String,
    var status: String,
    var notes: String? = null,
    var paidAmount: Double? = null,
    var paidDate: String? = null
) : Parcelable
