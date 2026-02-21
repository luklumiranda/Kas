package com.example.aplikasikas

import android.os.Parcelable
import kotlinx.parcelize.Parcelize

@Parcelize
data class ExpenseCategory(
    val id: String,
    var name: String,
    var description: String,
    var expensesCount: Int = 0
) : Parcelable
