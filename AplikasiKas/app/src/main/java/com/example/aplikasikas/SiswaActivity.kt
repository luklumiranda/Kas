package com.example.aplikasikas

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.print.PrintAttributes
import android.print.PrintManager
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.SimpleDateFormat
import java.util.*

class SiswaActivity : BaseActivity() {

    private lateinit var rvStudent: RecyclerView
    private lateinit var studentAdapter: StudentAdapter

    companion object {
        var studentList = mutableListOf<Student>()
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_siswa)

        findViewById<TextView>(R.id.tvToolbarTitle).text = "Manajemen Siswa"

        rvStudent = findViewById(R.id.rvStudent)
        rvStudent.layoutManager = LinearLayoutManager(this)

        setupAdapter()
        fetchStudents() 

        val btnStudentNew: MaterialButton = findViewById(R.id.btnStudentNew)
        btnStudentNew.setOnClickListener {
            val intent = Intent(this, AddStudentActivity::class.java)
            startActivity(intent)
        }

        val btnPrint: MaterialButton = findViewById(R.id.btnPrint)
        btnPrint.setOnClickListener {
            showPrintDialog()
        }
    }

    private fun fetchStudents() {
        // Menggunakan getClient(this) agar Token Authorization terkirim
        RetrofitClient.getClient(this).getStudents().enqueue(object : Callback<ApiResponse<List<Student>>> {
            override fun onResponse(call: Call<ApiResponse<List<Student>>>, response: Response<ApiResponse<List<Student>>>) {
                val body = response.body()
                if (response.isSuccessful && body?.success == true) {
                    studentList = body.data?.toMutableList() ?: mutableListOf()
                    studentAdapter.updateData(studentList)
                } else {
                    val errorMsg = when {
                        response.code() == 401 -> "Unauthorized: Token tidak valid atau sudah expired"
                        response.code() == 403 -> "Forbidden: Anda tidak memiliki akses"
                        response.code() == 404 -> "Not Found: Endpoint tidak ditemukan"
                        else -> body?.message ?: "Gagal memuat data (Code: ${response.code()})"
                    }
                    Toast.makeText(this@SiswaActivity, errorMsg, Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ApiResponse<List<Student>>>, t: Throwable) {
                Toast.makeText(this@SiswaActivity, "Error Koneksi: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun setupAdapter() {
        studentAdapter = StudentAdapter(studentList,
            onEditClick = { student ->
                val intent = Intent(this, EditStudentActivity::class.java)
                intent.putExtra("EXTRA_STUDENT", student)
                startActivity(intent)
            },
            onDeleteClick = { student ->
                showDeleteConfirmation(student)
            }
        )
        rvStudent.adapter = studentAdapter
    }

    private fun showDeleteConfirmation(student: Student) {
        AlertDialog.Builder(this)
            .setTitle("Hapus Siswa")
            .setMessage("Apakah Anda yakin ingin menghapus siswa ${student.name}?")
            .setPositiveButton("Hapus") { _, _ ->
                RetrofitClient.getClient(this).deleteStudent(student.id ?: "").enqueue(object : Callback<ApiResponse<Void>> {
                    override fun onResponse(call: Call<ApiResponse<Void>>, response: Response<ApiResponse<Void>>) {
                        if (response.isSuccessful) {
                            Toast.makeText(this@SiswaActivity, "Siswa berhasil dihapus", Toast.LENGTH_SHORT).show()
                            fetchStudents()
                        } else {
                            Toast.makeText(this@SiswaActivity, "Gagal menghapus", Toast.LENGTH_SHORT).show()
                        }
                    }
                    override fun onFailure(call: Call<ApiResponse<Void>>, t: Throwable) {
                        Toast.makeText(this@SiswaActivity, "Error: ${t.message}", Toast.LENGTH_SHORT).show()
                    }
                })
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun showPrintDialog() {
        AlertDialog.Builder(this)
            .setTitle("Cetak Laporan")
            .setMessage("Tekan tombol Cetak untuk mengunduh laporan dalam bentuk PDF.")
            .setPositiveButton("Cetak") { _, _ -> doPrint() }
            .setNegativeButton("Tutup", null)
            .show()
    }

    private fun doPrint() {
        val webView = WebView(this)
        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest) = false
            override fun onPageFinished(view: WebView, url: String) {
                createWebPrintJob(view)
            }
        }
        val htmlContent = generateHtmlReport()
        webView.loadDataWithBaseURL(null, htmlContent, "text/HTML", "UTF-8", null)
    }

    private fun generateHtmlReport(): String {
        val date = SimpleDateFormat("dd-MM-yyyy HH:mm", Locale.getDefault()).format(Date())
        val sharedPref = getSharedPreferences("UserPrefs", Context.MODE_PRIVATE)
        val adminName = sharedPref.getString("user_name", "Admin")

        val tableRows = StringBuilder()
        studentList.forEachIndexed { index, s ->
            tableRows.append("""
                <tr>
                    <td>${index + 1}</td>
                    <td>${s.name}</td>
                    <td>${s.gender ?: "-"}</td>
                    <td>${s.birth ?: "-"}</td>
                    <td>${s.lastEducation ?: "-"}</td>
                    <td>${s.address ?: "-"}</td>
                    <td>${s.phone ?: "-"}</td>
                    <td>${s.role?.uppercase() ?: "-"}</td>
                </tr>
            """.trimIndent())
        }

        return """
            <html>
            <head>
                <style>
                    body { font-family: sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                    th { background-color: #f2f2f2; }
                    .header-table { border: none; margin-bottom: 30px; }
                    .header-table td { border: none; padding: 4px; }
                    .font-weight-bold { font-weight: bold; }
                    h2 { text-align: center; }
                </style>
            </head>
            <body>
                <h2>LAPORAN DATA SISWA</h2>
                <table class="header-table">
                    <tr>
                        <td width="15%" class="font-weight-bold">Dicetak:</td>
                        <td width="50%">$adminName</td>
                        <td width="15%" class="font-weight-bold">Tanggal Cetak:</td>
                        <td width="20%" align="right">$date</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold">Filter:</td>
                        <td>Semua Data (Live dari API)</td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>JK</th>
                            <th>Tgl Lahir</th>
                            <th>Pendidikan</th>
                            <th>Alamat</th>
                            <th>No Telepon</th>
                            <th>Divisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        $tableRows
                    </tbody>
                </table>
            </body>
            </html>
        """.trimIndent()
    }

    private fun createWebPrintJob(webView: WebView) {
        val printManager = getSystemService(Context.PRINT_SERVICE) as PrintManager
        val jobName = "${getString(R.string.app_name)} - Laporan Siswa"
        val printAdapter = webView.createPrintDocumentAdapter(jobName)
        printManager.print(jobName, printAdapter, PrintAttributes.Builder().build())
    }

    override fun onResume() {
        super.onResume()
        fetchStudents()
    }
}
