<?php

namespace Database\Seeders;

use App\Models\SystemConfiguration;
use Illuminate\Database\Seeder;

class SystemConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemConfiguration::create([
            'configuration_name' => 'Discount Percentages',
            'configuration_data' => [
                'Maximum Discount Percentage' => '70'
            ]
        ]);
    }
}
