<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Task;
use App\Models\User;
use App\Models\Setting;



class cronRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:dbRefresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (User::all() as $user) {
            if ($user->id !== 1) {
                $user->delete();
            }
        }

        foreach (Setting::all() as $setting) {
            if ($setting->user_id !== 1) {
                $setting->delete();
            }
        }
        DB::table('tasks')->truncate();
        DB::table('oauth_access_tokens')->truncate();
        DB::table('oauth_refresh_tokens')->truncate();
        DB::table('password_resets')->truncate();

        $tasks_data = array(
            array(
                'user_id' => 1,
                'name' => "Test task №1 Active",
                'description' => "test description of task №1",
                'priority' => 1,
                'status' => 0,
                'deadline' => Date('Y-m-d H:i:s', strtotime("+3 days")),
            ),
            array(
                'user_id' => 1,
                'name' => "Test task №2 Active",
                'description' => "test description of task №2",
                'priority' => 2,
                'status' => 0,
                'deadline' => Date('Y-m-d H:i:s', strtotime("+2 days")),

            ),
            array(
                'user_id' => 1,
                'name' => "Test task №3 Active",
                'description' => "test description of task №3",
                'priority' => 3,
                'status' => 0,
                'deadline' => Date('Y-m-d H:i:s', strtotime("+1 days")),

            ),
            array(
                'user_id' => 1,
                'name' => "Test task №1 Done",
                'description' => "test description of task №1",
                'priority' => 1,
                'status' => 1,
                'deadline' => Date('Y-m-d H:i:s', strtotime("-1 days")),

            ),
            array(
                'user_id' => 1,
                'name' => "Test task №2 Done",
                'description' => "test description of task №2",
                'priority' => 2,
                'status' => 1,
                'deadline' => Date('Y-m-d H:i:s', strtotime("-2 days")),

            ),
            array(
                'user_id' => 1,
                'name' => "Test task №3 Done",
                'description' => "test description of task №3",
                'priority' => 3,
                'status' => 1,
                'deadline' => Date('Y-m-d H:i:s', strtotime("-7 days")),
            ),
        );

        foreach($tasks_data as $task_data){
            $task = new Task();
            $task->user_id = $task_data['user_id'];
            $task->name = $task_data['name'];
            $task->description = $task_data['description'];
            $task->priority = $task_data['priority'];
            $task->status = $task_data['status'];
            $task->deadline = $task_data['deadline'];
            $task->save();
        }
















    }
}
