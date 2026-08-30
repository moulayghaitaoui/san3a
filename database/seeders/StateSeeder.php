<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            'أم البواقي', 'باتنة', 'بجاية', 'بشار', 'البليدة', 'تبسة', 'تلمسان', 'تيارت', 'تيزي وزو', 'الجزائر', 'الجلفة', 'جيجل', 'سطيف', 'سكيكدة', 'سيدي بلعباس', 'عنابة', 'قسنطينة', 'المدية', 'مستغانم', 'مسيلة', 'معسكر', 'ورقلة', 'وهران', 'البيض', 'برج بوعريريج', 'بومرداس', 'الطارف', 'الوادي', 'خنشلة', 'تيبازة', 'عين الدفلى', 'النعامة', 'عين تيموشنت', 'غرداية', 'غليزان', 'تيميمون', 'بني عباس', 'المنيعة'
        ];

        foreach ($states as $state) {
            State::firstOrCreate(['name' => $state]);
        }
    }
}
