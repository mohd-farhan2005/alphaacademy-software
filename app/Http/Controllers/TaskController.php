<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Task::with(['assigner', 'assignee', 'responsiblePerson']);

        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            $deptUserIds = User::where('department_id', $user->department_id)->pluck('id');
            $query->whereIn('assigned_to', $deptUserIds);
        } elseif ($user->role === RoleType::EMPLOYEE) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();
        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $employeesQuery = User::whereNot('role', RoleType::SUPER_ADMIN->value);
        
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            $employeesQuery->where('department_id', $user->department_id);
        } elseif ($user->role === RoleType::EMPLOYEE) {
            $employeesQuery->where('id', $user->id);
        }

        $employees = $employeesQuery->get();
        $allUsers = User::whereNot('role', RoleType::SUPER_ADMIN->value)->get();
        return view('tasks.create', compact('employees', 'allUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'responsible_person' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $validated['assigned_by'] = $request->user()->id;
        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        $user = request()->user();
        
        $employeesQuery = User::whereNot('role', RoleType::SUPER_ADMIN->value);
        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            $employeesQuery->where('department_id', $user->department_id);
        } elseif ($user->role === RoleType::EMPLOYEE) {
            $employeesQuery->where('id', $user->id);
        }
        $employees = $employeesQuery->get();
        $allUsers = User::whereNot('role', RoleType::SUPER_ADMIN->value)->get();

        return view('tasks.edit', compact('task', 'employees', 'allUsers'));
    }

    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'responsible_person' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
