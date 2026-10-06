<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $userTasks = $request->user()->tasks();

        $tasks = (clone $userTasks)->latest()
            ->when($filter === 'todo', fn ($q) => $q->where('is_done', false))
            ->when($filter === 'done', fn ($q) => $q->where('is_done', true))
            ->get();

        $remaining = (clone $userTasks)->where('is_done', false)->count();

        return view('tasks.index', compact('tasks', 'filter', 'remaining'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'due_date' => 'nullable|date',
        ]);
        $request->user()->tasks()->create($data);
        return back()->with('success', 'Tâche ajoutée.');
    }

    public function update(Request $request, Task $task)
    {
        $this->checkOwner($request, $task);
        $task->update(['is_done' => !$task->is_done]);
        return back();
    }

    public function rename(Request $request, Task $task)
    {
        $this->checkOwner($request, $task);
        $data = $request->validate(['title' => 'required|max:255']);
        $task->update($data);
        return back()->with('success', 'Tâche modifiée.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->checkOwner($request, $task);
        $task->delete();
        return back()->with('success', 'Tâche supprimée.');
    }

    private function checkOwner(Request $request, Task $task): void
    {
        abort_if($task->user_id !== $request->user()->id, 403);
    }
}