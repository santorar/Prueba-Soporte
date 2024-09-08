<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
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

        return redirect()->back()->with('success', 'Task created successfully.');
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
            return redirect()->back()->with('error', 'Task not found.');
        }

        // Corrección: Se actualiza la tarea con datos validados.
        $task->update($validated);
        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    // Eliminar tarea
    public function destroy($id)
    {
        $task = Task::find($id);

        if(!$task) {
            return redirect()->back()->with('error', 'Task not found.');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
