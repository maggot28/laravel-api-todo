<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function sync(Request $request)
    {
        $response = array('status' => true);
        $user = Auth::guard('api')->user();
        $tasks = Task::where('user_id', $user->id)->get();
        if(sizeof($tasks) > 0){
            foreach($tasks as $task) {
                $task->forceDelete();
            }
            
        }
        $count = -1;
        foreach($request->columns as $column){
            if(sizeof($column['tasks']) > 0){
                foreach($column['tasks'] as $task_data){
                    $task = new Task();
                    $task->name = $task_data['name']?? "";
                    $task->status = $column['name'];
                    $task->description = $task_data['description']?? "";
                    $task->priority = $count--;
                    $task->user_id = $user->id;
                    $task->save();
                    $response['data'][] = $task;
                }
            } else {
                $task = new Task();
                $task->name = "";
                $task->description = "";
                $task->user_id = $user->id;
                $task->status = $column['name'];
                $task->priority = $count--;
                $task->save();
                $response['data'][] = $task;
            }
        }
        $response['message'] = "Tasks sync";
        return response()->json($response);
    }

    public function get($task = false)
    {
        $user = Auth::guard('api')->user();
        $response = array('status' => true);
        if ($task) {
            if ($task == "active") {
                $data = $user->activeTasks;

                $new_data = array();
                $deadline_tasks = array();
                foreach ($data as $i => $task) {
                    if (date("Y-m-d", strtotime($task->deadline)) < date('Y-m-d')) {
                        $deadline_tasks[] = $task;
                    } else {
                        $new_data[] = $task;
                    }
                }
                $data = array_merge($new_data, $deadline_tasks);

            } else if($task == "done"){
                $data = $user->doneTasks;
            } else if($task == "archive"){
                $data = $user->archiveTasks;
            } else {
                $data = Task::find($task);
            }
            $response['message'] = "Task ";
        } else {
            $data = $user->tasks;
            $response['message'] = "Tasks ";
        }

        if (sizeof($data) == 0) {
            $response['message'] .= "not found";
            $response['data'] = [];
        } else {
            $response['message'] .= "found";
            $response['data'] = $data;
        }
        return response()->json($response);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'priority' => 'required|digits:1|max:3|min:1',
            'deadline' => 'required|date'
        ]);

        $user = Auth::guard('api')->user();
        $task = new Task();
        $task->name = $request->name;
        $task->user_id = $user->id;
        $task->description = ($request->description)?$request->description:'';
        $task->priority = $request->priority;
        $task->deadline = date("Y-m-d", strtotime($request->deadline));
        $response = [];
        if ($task->save()) {
            $response["status"] = true;
            $response["message"] = "Task Created";
            $response["data"] = $task;
        } else {
            $response["status"] = false;
            $response["message"] = "Task Creating Error!";
        }
        return response()->json($response);
    }

    public function delete($id)
    {
        $task = Task::find($id);
        $response = [];
        if ($task->delete()) {
            $response["status"] = true;
            $response["message"] = "Task Deleted";
        } else {
            $response["status"] = false;
            $response["message"] = "Task Deleting Error!";
        }
        return response()->json($response);
    }

    public function destroy($id)
    {
        $task = Task::onlyTrashed()->find($id);
        $response = [];
        if ($task->forceDelete()) {
            $response["status"] = true;
            $response["message"] = "Task Destroy";
        } else {
            $response["status"] = false;
            $response["message"] = "Task Destroing Error!";
        }
        return response()->json($response);
    }

    public function patch(Request $request, $id)
    {
        $response = [];
        if ($request->field == "restore") {
            $task = Task::withTrashed()->find($id);
            $result = $task->restore();
        } else{
            $task = Task::find($id);
            $field = $request->field;
            $task->$field = $request->value;
            $result = $task->save();
        }
        if ($result) {
            $response["status"] = true;
            $response["message"] = "Task Patched";
            $response["data"] = $task;
        } else {
            $response["status"] = false;
            $response["message"] = "Task Patching Error!";
        }
        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'priority' => 'required|digits:1|max:3|min:1',
            'deadline' => 'required|date'
        ]);
        $response = [];

        $user = Auth::guard('api')->user();
        $task = Task::withTrashed()->find($id);
        if ($task) {
            $task->name = $request->name;
            $task->description = ($request->description)?$request->description:'';
            $task->priority = $request->priority;
            $task->deadline = date("Y-m-d", strtotime($request->deadline));
            if ($task->save()) {
                $response["status"] = true;
                $response["message"] = "Task Updated";
                $response["data"] = $task;
            } else {
                $response["status"] = false;
                $response["message"] = "Task Updating Error!";
            }
        } else{
            $response["status"] = true;
            $response["message"] = "Task Not Found";
        }
        
        return response()->json($response);
    }
}