<?php

namespace Database\Seeders;

use App\Models\WorkPattern;
use App\Models\WorkTiming;
use Illuminate\Database\Seeder;

class WorkPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Work Patterns
        $workPatternNames = [
            'alternative',
            'flexible',
            'full-time',
            'variable',
        ];

        $alternativeWorkTimings = [
            [
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'monday',
                    'tuesday',
                ],
            ],
            [
                'start_time' => '09:30:00',
                'end_time' => '17:30:00',
                'break_time' => '30',
                'repeat_days' => [
                    'wednesday',
                    'thursday',
                ],
            ],
            [
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'friday',
                ],
            ],
        ];

        $flexibleWorkTimings = [
            [
                'start_time' => '13:00:00',
                'end_time' => '18:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'monday',
                    'wednesday',
                    'friday',
                ],
            ],
            [
                'start_time' => '08:00:00',
                'end_time' => '13:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'tuesday',
                    'thursday',
                ],
            ],
        ];

        $variableWorkTimings = [
            [

                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'monday',
                ],
            ],
            [

                'start_time' => '10:00:00',
                'end_time' => '17:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'tuesday',
                ],

            ],
            [

                'start_time' => '13:00:00',
                'end_time' => '18:00:00',
                'break_time' => '15',
                'repeat_days' => [
                    'wednesday',
                ],
            ],
            [

                'start_time' => '15:00:00',
                'end_time' => '20:00:00',
                'break_time' => '15',
                'repeat_days' => [
                    'thursday',
                ],

            ],
            [

                'start_time' => '12:00:00',
                'end_time' => '16:00:00',
                'break_time' => '15',
                'repeat_days' => [
                    'friday',
                ],

            ],
        ];

        $fullTimeWorkTimings = [
            [
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'break_time' => '30',
                'repeat_days' => [
                    'monday',
                    'tuesday',
                    'wednesday',
                    'thursday',
                    'friday',
                ],
            ],
        ];

        foreach ($workPatternNames as $workPatternName):
            if ($workPatternName === 'alternative') {
                // Create work pattern
                $workPattern = new WorkPattern();
                $workPattern->name = $workPatternName;
                $workPattern->save();
                foreach ($alternativeWorkTimings as $alternativeWorkTiming):
                    // Create Work Timing
                    $workTiming = new WorkTiming();
                    $workTiming->work_pattern_id = $workPattern->id;
                    $workTiming->start_time = $alternativeWorkTiming['start_time'];
                    $workTiming->end_time = $alternativeWorkTiming['end_time'];
                    $workTiming->break_time = $alternativeWorkTiming['break_time'];
                    $workTiming->repeat_days = $alternativeWorkTiming['repeat_days'];
                    $workPattern->workTimings()->save($workTiming);
                endforeach;
            }

            if ($workPatternName === 'flexible') {
                // Create work pattern
                $workPattern = new WorkPattern();
                $workPattern->name = $workPatternName;
                $workPattern->save();
                foreach ($flexibleWorkTimings as $flexibleWorkTiming):
                    // Create Work Timing
                    $workTiming = new WorkTiming();
                    $workTiming->work_pattern_id = $workPattern->id;
                    $workTiming->start_time = $flexibleWorkTiming['start_time'];
                    $workTiming->end_time = $flexibleWorkTiming['end_time'];
                    $workTiming->break_time = $flexibleWorkTiming['break_time'];
                    $workTiming->repeat_days = $flexibleWorkTiming['repeat_days'];
                    $workPattern->workTimings()->save($workTiming);
                endforeach;
            }

            if ($workPatternName === 'variable') {
                // Create work pattern
                $workPattern = new WorkPattern();
                $workPattern->name = $workPatternName;
                $workPattern->save();
                foreach ($variableWorkTimings as $variableWorkTiming):
                    // Create Work Timing
                    $workTiming = new WorkTiming();
                    $workTiming->work_pattern_id = $workPattern->id;
                    $workTiming->start_time = $variableWorkTiming['start_time'];
                    $workTiming->end_time = $variableWorkTiming['end_time'];
                    $workTiming->break_time = $variableWorkTiming['break_time'];
                    $workTiming->repeat_days = $variableWorkTiming['repeat_days'];
                    $workPattern->workTimings()->save($workTiming);
                endforeach;
            }

            if ($workPatternName === 'full-time') {
                // Create work pattern
                $workPattern = new WorkPattern();
                $workPattern->name = $workPatternName;
                $workPattern->save();
                foreach ($fullTimeWorkTimings as $fullTimeWorkTiming):
                    // Create Work Timing
                    $workTiming = new WorkTiming();
                    $workTiming->work_pattern_id = $workPattern->id;
                    $workTiming->start_time = $fullTimeWorkTiming['start_time'];
                    $workTiming->end_time = $fullTimeWorkTiming['end_time'];
                    $workTiming->break_time = $fullTimeWorkTiming['break_time'];
                    $workTiming->repeat_days = $fullTimeWorkTiming['repeat_days'];
                    $workPattern->workTimings()->save($workTiming);
                endforeach;
            }
        endforeach;
    }
}