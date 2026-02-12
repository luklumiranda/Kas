<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Fotokopi',
                'description' => 'Biaya fotokopi untuk keperluan sekolah'
            ],
            [
                'name' => 'Kebersihan',
                'description' => 'Biaya kebersihan ruang dan area'
            ],
            [
                'name' => 'Dana Sosial',
                'description' => 'Dana untuk kegiatan sosial anggota'
            ],
            [
                'name' => 'Perawatan Peralatan',
                'description' => 'Pemeliharaan dan perbaikan peralatan'
            ],
            [
                'name' => 'Administrasi',
                'description' => 'Biaya administrasi dan operasional'
            ],
            [
                'name' => 'Konsumsi & Catering',
                'description' => 'Biaya makan minum untuk acara'
            ],
            [
                'name' => 'Dekorasi & Perlengkapan Acara',
                'description' => 'Biaya dekorasi untuk kegiatan'
            ],
            [
                'name' => 'Transportasi',
                'description' => 'Biaya transportasi anggota'
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Kategori pengeluaran lainnya'
            ]
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
