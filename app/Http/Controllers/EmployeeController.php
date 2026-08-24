<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user.roles')->orderBy('created_at', 'desc')->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'employee_code' => 'required|string|max:50|unique:employees',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $request->employee_code,
                'designation' => $request->designation,
                'department' => $request->department,
                'phone' => $request->phone,
                'hire_date' => now(),
                'status' => 'active',
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $employee->load('user.roles');
        $roles = Role::orderBy('name')->get();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->user_id)],
            'employee_code' => ['required', 'string', 'max:50', Rule::unique('employees')->ignore($employee->id)],
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|exists:roles,name',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $user = $employee->user;
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);
            
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->syncRoles([$request->role]);

            $employee->update([
                'employee_code' => $request->employee_code,
                'designation' => $request->designation,
                'department' => $request->department,
                'phone' => $request->phone,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            $employee->user()->delete();
            $employee->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Employee removed successfully.');
    }
}
