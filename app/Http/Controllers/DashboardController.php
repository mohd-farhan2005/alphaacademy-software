<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use App\Enums\RoleType;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role;
        $data = [];

        if ($role === RoleType::SUPER_ADMIN) {
            $data['departments_count'] = Department::count();
            $data['employees_count'] = User::whereNot('role', RoleType::SUPER_ADMIN->value)->count();
            $data['total_tasks'] = Task::count();
            $data['pending_tasks'] = Task::where('status', 'pending')->count();
            $data['completed_tasks'] = Task::where('status', 'completed')->count();

            // Build department-wise task chart data
            $departments = Department::with('users')->get();
            $deptLabels = [];
            $deptTaskCounts = [];
            foreach ($departments as $dept) {
                $userIds = $dept->users->pluck('id');
                $deptLabels[] = $dept->name;
                $deptTaskCounts[] = Task::whereIn('assigned_to', $userIds)->count();
            }
            $data['dept_labels'] = $deptLabels;
            $data['dept_task_counts'] = $deptTaskCounts;

            // Build month-wise task chart data (current year, per department)
            $currentYear = now()->year;
            $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $monthlyDatasets = [];
            $lineColors = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#84cc16'];
            foreach ($departments as $idx => $dept) {
                $userIds = $dept->users->pluck('id');
                $monthlyCounts = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyCounts[] = Task::whereIn('assigned_to', $userIds)
                        ->whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $m)
                        ->count();
                }
                // Assign yellow to DME department
                $color = stripos($dept->name, 'dme') !== false
                    ? '#eab308'
                    : $lineColors[$idx % count($lineColors)];
                $monthlyDatasets[] = [
                    'label'           => $dept->name,
                    'data'            => $monthlyCounts,
                    'borderColor'     => $color,
                    'backgroundColor' => $color . '22',
                    'tension'         => 0.4,
                    'fill'            => false,
                    'pointRadius'     => 4,
                    'pointHoverRadius'=> 6,
                    'borderWidth'     => 2,
                ];
            }
            $data['monthly_month_names'] = $monthNames;
            $data['monthly_datasets']    = $monthlyDatasets;
            $data['chart_year']          = $currentYear;
        } elseif (in_array($role, [RoleType::DME_HEAD, RoleType::HA_HEAD, RoleType::CREATIVES_HEAD])) {
            $deptId = $user->department_id;
            $data['employees_count'] = User::where('department_id', $deptId)->count();
            $deptUserIds = User::where('department_id', $deptId)->pluck('id');

            $data['total_tasks']     = Task::whereIn('assigned_to', $deptUserIds)->count();
            $data['pending_tasks']   = Task::whereIn('assigned_to', $deptUserIds)->where('status', 'pending')->count();
            $data['completed_tasks'] = Task::whereIn('assigned_to', $deptUserIds)->where('status', 'completed')->count();

            // Chart data for this department only
            $dept = Department::with('users')->find($deptId);
            if ($dept) {
                // Pick a distinct color: DME = yellow, HA = indigo
                $color = stripos($dept->name, 'dme') !== false ? '#eab308' : '#6366f1';

                // Dept bar/doughnut chart (single department)
                $data['dept_labels']      = [$dept->name];
                $data['dept_task_counts'] = [Task::whereIn('assigned_to', $deptUserIds)->count()];

                // Monthly line chart (single department line)
                $currentYear = now()->year;
                $monthNames  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                $monthlyCounts = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyCounts[] = Task::whereIn('assigned_to', $deptUserIds)
                        ->whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $m)
                        ->count();
                }
                $data['monthly_month_names'] = $monthNames;
                $data['monthly_datasets']    = [[
                    'label'            => $dept->name,
                    'data'             => $monthlyCounts,
                    'borderColor'      => $color,
                    'backgroundColor'  => $color . '22',
                    'tension'          => 0.4,
                    'fill'             => false,
                    'pointRadius'      => 4,
                    'pointHoverRadius' => 6,
                    'borderWidth'      => 2,
                ]];
                $data['chart_year'] = $currentYear;
            }
        } else {
            $data['total_tasks'] = Task::where('assigned_to', $user->id)->count();
            $data['pending_tasks'] = Task::where('assigned_to', $user->id)->where('status', 'pending')->count();
            $data['completed_tasks'] = Task::where('assigned_to', $user->id)->where('status', 'completed')->count();
        }

        // Fetch recent tasks for the user based on role
        $recentTasksQuery = Task::with(['assigner', 'assignee']);
        if ($role === RoleType::SUPER_ADMIN) {
            // Unrestricted for super admins
        } elseif (in_array($role, [RoleType::DME_HEAD, RoleType::HA_HEAD, RoleType::CREATIVES_HEAD])) {
            $recentTasksQuery->whereIn('assigned_to', $deptUserIds ?? User::where('department_id', $user->department_id)->pluck('id'));
        } else {
            $recentTasksQuery->where('assigned_to', $user->id);
        }
        $data['recent_tasks'] = $recentTasksQuery->latest()->take(5)->get();

        return view('dashboard', compact('data', 'role'));
    }
}
