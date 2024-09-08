<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public static function fetchTasks(Request $request)
    {
        $filter = $request->query('filter');
        if($filter == 'completed'){
            $tasks = Task::where('completed', true)->with('user')->get();
        } else if($filter == 'pending'){
            $tasks = Task::where('completed', false)->with('user')->get();
        } else {
            $tasks = Task::with('user')->get();
        }
        $tasks->transform(function ($tasks){
            return [
                'id' => $tasks->id,
                'title' => $tasks->title,
                'description' => $tasks->description,
                'completed' => $tasks->completed,
                'user' => $tasks->user->name,
                'created_at' => $tasks->created_at,
                'updated_at' => $tasks->updated_at,
            ];
        });
        return $tasks;
    }
    // Crear tarea
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:500',
            'user' => 'required|max:500',
        ]);

        $task = new Task($validated);
        $user = User::where('email',$validated['user'])->first();
        $task->user_id = $user->id;
        $task->save();
        $task->user = $user['name'];
        return $task;
    }
    //  Completar tarea
    public function complete($id){
        $task = Task::find($id);
        if(!$task){
            return response()->json(['error' => 'Task not found'], 404);
        }
        if($task->completed){
            return response()->json(['error' => 'Task already completed'], 400);
        }
        $task->completed = true;
        $task->save();
        $user = User::find($task->user_id);
        $task->user = $user['name'];
        return $task;
    }
    // Actualizar tarea
    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:500',
        ]);

        $task = Task::find($id);

        if(!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Corrección: Se actualiza la tarea con datos validados.
        $task->update($validated);
        $user = User::find($task->user_id);
        $task->user = $user['name'];
        return $task;
    }

    // Eliminar tarea
    public function destroy($id)
    {
        $task = Task::find($id);

        if(!$task) {
            return response()->json(['error'=> 'Task not found.']);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted.']);
    }
}
