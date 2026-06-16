<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractTerm;

class ContractTermSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing terms to avoid duplicates
        ContractTerm::truncate();

        $terms = [
            [
                'title_km' => 'មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ',
                'title_en' => 'Article 1 - BUYER\'S OBLIGATIONS',
                'content_km' => "អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ខាងលើ\nរក្សាផលិតផលឲ្យបានល្អ និងប្រើប្រាស់ត្រឹមត្រូវតាមការណែនាំ\nជូនដំណឹងភ្លាមៗប្រសិនបើមានបញ្ហាជាមួយផលិតផល",
                'content_en' => "The buyer agrees to pay monthly according to the schedule above.\nKeep the product in good condition and use it properly.\nNotify immediately if there is any problem with the product.",
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title_km' => 'មាត្រា២ - ការយឺតយ៉ាវ',
                'title_en' => 'Article 2 - LATE PAYMENT',
                'content_km' => "ការបង់ប្រាក់យឺត 1-5 ថ្ងៃ: គ្មានការពិន័យ\nការបង់ប្រាក់យឺត 6-15 ថ្ងៃ: ការពិន័យ $5/ថ្ងៃ\nការបង់ប្រាក់យឺតលើស 15 ថ្ងៃ: ការពិន័យ $10/ថ្ងៃ\nការបង់ប្រាក់យឺតលើស 30 ថ្ងៃ: អាចដកផលិតផលវិញបាន",
                'content_en' => "Late payment 1-5 days: no penalty.\nLate payment 6-15 days: penalty $5/day.\nLate payment over 15 days: penalty $10/day.\nLate payment over 30 days: the product may be repossessed.",
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title_km' => 'មាត្រា៣ - កម្មសិទ្ធិ',
                'title_en' => 'Article 3 - OWNERSHIP',
                'content_km' => "ផលិតផលនៅជាកម្មសិទ្ធិរបស់អ្នកលក់រហូតដល់បង់ប្រាក់រួចរាល់\nអ្នកទិញមិនអាចលក់ត្រង់ផលិតផលមុនពេលបង់រួច\nបន្ទាប់ពីបង់រួច កម្មសិទ្ធិផ្ទេរទៅអ្នកទិញភ្លាមៗ",
                'content_en' => "The product remains the seller's property until fully paid.\nThe buyer cannot resell the product before full payment.\nAfter full payment, ownership transfers to the buyer immediately.",
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'title_km' => 'មាត្រា៤ - ការធានា',
                'title_en' => 'Article 4 - WARRANTY',
                'content_km' => "ការធានា 1 ឆ្នាំសម្រាប់ផលិតផលថ្មីទាំងអស់\nការធានាមិនរាប់បញ្ចូលការខូចខាតដោយសារការប្រើប្រាស់មិនត្រឹមត្រូវ\nសេវាថែទាំ និងជួសជុលឥតគិតថ្លៃក្នុងអំឡុងពេលធានា",
                'content_en' => "1-year warranty for all new products.\nWarranty excludes damage caused by improper use.\nFree maintenance and repair during the warranty period.",
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($terms as $term) {
            ContractTerm::create($term);
        }
    }
}
