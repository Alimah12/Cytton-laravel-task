<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now()->toDateString();
        $tomorrow = Carbon::now()->addDay()->toDateString();

        Task::create([
            'title' => 'High priority pending',
            'due_date' => $today,
            'priority' => 'high',
            'status' => 'pending',
        ]);

        Task::create([
            'title' => 'Medium priority done',
            'due_date' => $today,
            'priority' => 'medium',
            'status' => 'done',
        ]);

        Task::create([
            'title' => 'Low priority done',
            'due_date' => $tomorrow,
            'priority' => 'low',
            'status' => 'done',
        ]);
    }
}
