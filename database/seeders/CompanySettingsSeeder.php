<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            'company_name' => 'CityTech Computer Shop',
            'company_name_km' => 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច',
            'company_address' => 'Phnom Penh, Cambodia',
            'company_address_km' => 'ភ្នំពេញ ប្រទេសកម្ពុជា',
            'company_phone' => '012-345-678',
            'company_email' => 'info@citytech.com',
            'company_business_license' => '',
            'company_logo' => '',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('Company settings seeded successfully!');
    }
}
