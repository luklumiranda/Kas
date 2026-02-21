package com.example.aplikasikas

import android.os.Parcelable
import com.google.gson.annotations.SerializedName
import kotlinx.parcelize.Parcelize

@Parcelize
data class Student(
    val id: String? = null,
    @SerializedName("username")
    var username: String = "",
    @SerializedName("name")
    var name: String = "",
    @SerializedName("password")
    var password: String? = null,
    @SerializedName("gender")
    var gender: String? = null,
    @SerializedName("birth")
    var birth: String? = null,
    @SerializedName("address")
    var address: String? = null,
    @SerializedName("phone")
    var phone: String? = null,
    @SerializedName("last_education")
    var lastEducation: String? = null,
    @SerializedName("profession")
    var profession: String? = null,
    @SerializedName("status")
    var status: String? = null,
    @SerializedName("photo")
    var photo: String? = null,
    @SerializedName("role")
    var role: String = "Siswa",
    @SerializedName("created_at")
    var createdAt: String? = null,
    @SerializedName("updated_at")
    var updatedAt: String? = null
) : Parcelable
