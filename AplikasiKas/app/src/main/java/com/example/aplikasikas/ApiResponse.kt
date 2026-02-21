package com.example.aplikasikas

data class ApiResponse<T>(
    val success: Boolean? = false,
    val message: String? = null,
    val data: T? = null
)
