package com.example.aplikasikas

import android.content.Intent
import android.os.Bundle
import android.view.MenuItem
import android.widget.ImageView
import androidx.appcompat.app.ActionBarDrawerToggle
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.GravityCompat
import androidx.drawerlayout.widget.DrawerLayout
import com.google.android.material.navigation.NavigationView

open class BaseActivity : AppCompatActivity(), NavigationView.OnNavigationItemSelectedListener {

    private lateinit var drawerLayout: DrawerLayout
    private lateinit var navigationView: NavigationView
    private lateinit var btnMenu: ImageView

    override fun setContentView(layoutResID: Int) {
        super.setContentView(layoutResID)

        drawerLayout = findViewById(R.id.drawerLayout)
        navigationView = findViewById(R.id.navView)
        btnMenu = findViewById(R.id.btnMenu)

        setupDrawer()
    }

    private fun setupDrawer() {
        val toggle = ActionBarDrawerToggle(
            this, drawerLayout, R.string.navigation_drawer_open, R.string.navigation_drawer_close
        )
        drawerLayout.addDrawerListener(toggle)
        toggle.syncState()

        navigationView.setNavigationItemSelectedListener(this)

        btnMenu.setOnClickListener {
            drawerLayout.openDrawer(GravityCompat.START)
        }
    }

    override fun onNavigationItemSelected(item: MenuItem): Boolean {
        when (item.itemId) {
            R.id.nav_dashboard -> {
                // Perbaikan: Navigasi ke DashboardActivity, bukan MainActivity (Login)
                if (this !is DashboardActivity) {
                    startActivity(Intent(this, DashboardActivity::class.java))
                }
            }
            R.id.nav_tagihan -> {
                if (this !is TagihanActivity) {
                    startActivity(Intent(this, TagihanActivity::class.java))
                }
            }
            R.id.nav_catat -> {
                if (this !is PencatatanActivity) {
                    startActivity(Intent(this, PencatatanActivity::class.java))
                }
            }
            R.id.nav_kategori -> {
                if (this !is KategoriActivity) {
                    startActivity(Intent(this, KategoriActivity::class.java))
                }
            }
            R.id.nav_laporan -> {
                if (this !is LaporanActivity) {
                    startActivity(Intent(this, LaporanActivity::class.java))
                }
            }
            R.id.nav_telegram -> {
                if (this !is TelegramActivity) {
                    startActivity(Intent(this, TelegramActivity::class.java))
                }
            }
            R.id.nav_siswa -> {
                if (this !is SiswaActivity) {
                    startActivity(Intent(this, SiswaActivity::class.java))
                }
            }
        }
        drawerLayout.closeDrawer(GravityCompat.START)
        return true
    }

    override fun onBackPressed() {
        if (drawerLayout.isDrawerOpen(GravityCompat.START)) {
            drawerLayout.closeDrawer(GravityCompat.START)
        } else {
            super.onBackPressed()
        }
    }
}
