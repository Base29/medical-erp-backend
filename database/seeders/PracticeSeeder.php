<?php

namespace Database\Seeders;

use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Practices
        $practices = [
            'County Surgery',
            'Fieldhead',
            'Lockwood Surgery',
            'Rushden Medical Center',
            'Thorton Lodge Surgery',
            'Northampton',
            'Kirklees',
        ];

        // Get Practice manager
        $practiceManager = User::where('email', 'manager@eharleystreetadmin.com')->firstOrFail();

        // Create practices and seed database
        foreach ($practices as $practice):
            Practice::create([
                'practice_manager' => $practiceManager->id,
                'practice_name' => $practice,
            ]);
        endforeach;
    }
}