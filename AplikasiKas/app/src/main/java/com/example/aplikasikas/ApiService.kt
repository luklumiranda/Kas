package com.example.aplikasikas

import retrofit2.Call
import retrofit2.http.*

interface ApiService {
    // ==================== AUTH ====================
    @POST("api/login")
    fun login(@Body credentials: Map<String, String>): Call<ApiResponse<UserResponse>>

    @POST("api/logout")
    fun logout(): Call<ApiResponse<Void>>

    // ==================== NASABAH / SISWA ====================
    @GET("api/nasabah")
    fun getStudents(): Call<ApiResponse<List<Student>>>

    @GET("api/nasabah/{id}")
    fun getStudentDetail(@Path("id") id: String): Call<ApiResponse<Student>>

    @POST("api/nasabah")
    fun addStudent(@Body student: Student): Call<ApiResponse<Student>>

    @PUT("api/nasabah/{id}")
    fun updateStudent(@Path("id") id: String, @Body student: Student): Call<ApiResponse<Student>>

    @DELETE("api/nasabah/{id}")
    fun deleteStudent(@Path("id") id: String): Call<ApiResponse<Void>>

    // ==================== TAGIHAN / BILL ====================
    @GET("api/bill")
    fun getBills(): Call<ApiResponse<List<Bill>>>

    @GET("api/bill/{id}")
    fun getBillDetail(@Path("id") id: String): Call<ApiResponse<Bill>>

    @POST("api/bill")
    fun addBill(@Body bill: Bill): Call<ApiResponse<Bill>>

    @PUT("api/bill/{id}")
    fun updateBill(@Path("id") id: String, @Body bill: Bill): Call<ApiResponse<Bill>>

    @DELETE("api/bill/{id}")
    fun deleteBill(@Path("id") id: String): Call<ApiResponse<Void>>

    // ==================== PENGELUARAN / EXPENSE ====================
    @GET("api/expense")
    fun getExpenses(): Call<ApiResponse<List<Expense>>>

    @GET("api/expense/{id}")
    fun getExpenseDetail(@Path("id") id: String): Call<ApiResponse<Expense>>

    @POST("api/expense")
    fun addExpense(@Body expense: Expense): Call<ApiResponse<Expense>>

    @PUT("api/expense/{id}")
    fun updateExpense(@Path("id") id: String, @Body expense: Expense): Call<ApiResponse<Expense>>

    @DELETE("api/expense/{id}")
    fun deleteExpense(@Path("id") id: String): Call<ApiResponse<Void>>

    // ==================== KATEGORI PENGELUARAN ====================
    @GET("api/expense-category")
    fun getExpenseCategories(): Call<ApiResponse<List<ExpenseCategory>>>

    @GET("api/expense-category/{id}")
    fun getExpenseCategoryDetail(@Path("id") id: String): Call<ApiResponse<ExpenseCategory>>

    @POST("api/expense-category")
    fun addExpenseCategory(@Body category: ExpenseCategory): Call<ApiResponse<ExpenseCategory>>

    @PUT("api/expense-category/{id}")
    fun updateExpenseCategory(@Path("id") id: String, @Body category: ExpenseCategory): Call<ApiResponse<ExpenseCategory>>

    @DELETE("api/expense-category/{id}")
    fun deleteExpenseCategory(@Path("id") id: String): Call<ApiResponse<Void>>

    // ==================== LAPORAN ====================
    @GET("api/laporan")
    fun getLaporans(): Call<ApiResponse<List<Laporan>>>

    @GET("api/laporan/{id}")
    fun getLaporanDetail(@Path("id") id: String): Call<ApiResponse<Laporan>>

    @POST("api/laporan")
    fun addLaporan(@Body laporan: Laporan): Call<ApiResponse<Laporan>>

    @PUT("api/laporan/{id}")
    fun updateLaporan(@Path("id") id: String, @Body laporan: Laporan): Call<ApiResponse<Laporan>>

    @DELETE("api/laporan/{id}")
    fun deleteLaporan(@Path("id") id: String): Call<ApiResponse<Void>>
}

data class UserResponse(
    val user: Student,
    val token: String? = null
)
