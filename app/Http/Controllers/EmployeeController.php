<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Task;
use App\Enums\RoleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = User::whereNot('role', RoleType::SUPER_ADMIN->value)->with('department');

        if (in_array($user->role, [RoleType::DME_HEAD, RoleType::HA_HEAD])) {
            $query->where('department_id', $user->department_id);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }

        $employees = $query->latest()->paginate(10)->withQueryString();
        return view('employees.index', compact('employees'));
    }

    public function create(Request $request)
    {
        $departments = Department::all();
        $roles = [RoleType::EMPLOYEE->value];
        if ($request->user()->role === RoleType::SUPER_ADMIN) {
            $roles = [RoleType::DME_HEAD->value, RoleType::HA_HEAD->value, RoleType::EMPLOYEE->value];
        }
        return view('employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $authUser = $request->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($authUser->role === RoleType::SUPER_ADMIN) {
            $rules['role'] = ['required', 'string', 'in:' . implode(',', [RoleType::DME_HEAD->value, RoleType::HA_HEAD->value, RoleType::EMPLOYEE->value])];
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if ($authUser->role === RoleType::SUPER_ADMIN) {
            $data['role'] = \App\Enums\RoleType::from($validated['role']);
            $data['department_id'] = $validated['department_id'];
        } else {
            $data['role'] = RoleType::EMPLOYEE;
            $data['department_id'] = $authUser->department_id;
        }

        User::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Request $request, User $employee)
    {
        Gate::authorize('view', $employee);

        $period = $request->input('period', 'this_month');
        $query = Task::where('assigned_to', $employee->id);

        if ($period === 'this_month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        } elseif ($period === 'last_month') {
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', Carbon::now()->subMonth()->year);
        }

        $taskCounts = [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];

        $recentTasks = (clone $query)
            ->latest()
            ->take(5)
            ->get();

        return view('employees.show', compact('employee', 'taskCounts', 'recentTasks', 'period'));
    }

    public function edit(User $employee)
    {
        Gate::authorize('update', $employee);
        $departments = Department::all();
        $roles = [RoleType::EMPLOYEE->value];
        if (request()->user()->role === RoleType::SUPER_ADMIN) {
            $roles = [RoleType::DME_HEAD->value, RoleType::HA_HEAD->value, RoleType::EMPLOYEE->value];
        }
        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        Gate::authorize('update', $employee);
        $authUser = $request->user();
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',id,'.$employee->id],
        ];
        
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        if ($authUser->role === RoleType::SUPER_ADMIN) {
            $rules['role'] = ['required', 'string', 'in:' . implode(',', [RoleType::DME_HEAD->value, RoleType::HA_HEAD->value, RoleType::EMPLOYEE->value])];
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'];
        if ($request->filled('password')) {
            $employee->password = Hash::make($validated['password']);
        }
        if ($authUser->role === RoleType::SUPER_ADMIN) {
            $employee->role = \App\Enums\RoleType::from($validated['role']);
            $employee->department_id = $validated['department_id'];
        }
        
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        Gate::authorize('delete', $employee);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
