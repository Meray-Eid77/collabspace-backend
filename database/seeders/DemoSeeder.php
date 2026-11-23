<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $meray = User::create([
            'name' => 'Meray Eid', 'email' => 'meray@test.com', 'password' => 'password',
            'job_title' => 'Backend Developer', 'phone' => '+20 123 456 9878',
            'availability' => 'Available', 'years_of_experience' => 2,
            'current_team' => 'CollabSpace - Web Platform',
            'bio' => 'Backend developer focused on Laravel and clean APIs.',
        ]);

        $users = collect([
            ['Mohanad Wahib', 'mohanad@test.com', 'UI/UX Designer'],
            ['Sara Hassan', 'sara@test.com', 'Frontend Developer'],
            ['Ali Mohamed', 'ali@test.com', 'Backend Developer'],
            ['Khalid Mousa', 'khalid@test.com', 'Marketing Specialist'],
        ])->map(fn ($u) => User::create([
            'name' => $u[0], 'email' => $u[1], 'password' => 'password',
            'job_title' => $u[2], 'availability' => 'Available',
        ]));

        // المشاريع
        $alpha = Project::create([
            'name' => 'Alpha', 'type' => 'SaaS', 'priority' => 'high', 'status' => 'active',
            'description' => 'This project focuses on organizing tasks, tracking progress, and coordinating team efforts.',
            'start_date' => '2026-10-12', 'deadline' => '2026-12-25', 'owner_id' => $meray->id,
        ]);
        $sepetgo = Project::create([
            'name' => 'SepetGo', 'type' => 'Food Delivery', 'priority' => 'medium', 'status' => 'done',
            'description' => 'Food delivery platform.',
            'start_date' => '2026-09-01', 'deadline' => '2026-11-30', 'owner_id' => $meray->id,
        ]);

        // الفرق والأعضاء
        $backend = Team::create(['project_id' => $alpha->id, 'name' => 'Backend']);
        $backend->members()->attach($meray->id, ['role' => 'Back Leader']);
        $backend->members()->attach($users[2]->id, ['role' => 'Backend Dev']);

        $uiux = Team::create(['project_id' => $alpha->id, 'name' => 'UI/UX']);
        $uiux->members()->attach($users[0]->id, ['role' => 'Leader']);

        $frontend = Team::create(['project_id' => $alpha->id, 'name' => 'Frontend']);
        $frontend->members()->attach($users[1]->id, ['role' => 'Front Leader']);

        // المهام
        $tasks = [
            [$alpha, 'Create Wireframes', 'todo', 'high', 0, '2026-12-12'],
            [$alpha, 'User Research Analysis', 'todo', 'medium', 0, '2026-12-14'],
            [$alpha, 'Design System Updates', 'todo', 'low', 0, '2026-12-20'],
            [$alpha, 'Chat Screen Prototyping', 'in_progress', 'high', 62, '2026-12-12'],
            [$alpha, 'Create Miro Interaction', 'in_progress', 'low', 32, '2026-12-15'],
            [$alpha, 'Mobile Responsiveness', 'in_review', 'high', 90, '2026-12-10'],
            [$alpha, 'Accessibility Audit', 'in_review', 'low', 85, '2026-12-11'],
            [$alpha, 'Login Screen', 'completed', 'high', 100, '2026-12-05'],
            [$sepetgo, 'Survey', 'completed', 'low', 100, '2026-11-20'],
        ];

        $all = collect([$meray])->merge($users);

        foreach ($tasks as $i => [$project, $title, $status, $priority, $progress, $due]) {
            $task = Task::create([
                'project_id' => $project->id,
                'title' => $title,
                'description' => 'Initial layout and structure for main page',
                'status' => $status,
                'priority' => $priority,
                'progress' => $progress,
                'due_date' => $due,
                'completed_at' => $status === 'completed' ? now()->subDays($i) : null,
                'reviewer_id' => $users[0]->id,
                'created_by' => $meray->id,
            ]);
            $task->assignees()->attach($all->random(2)->pluck('id'));
        }
    }
}
