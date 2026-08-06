<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Facility;
use App\Models\Role;
use App\Models\StaffUnit;
use App\Models\StudyProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function roles(): JsonResponse
    {
        return response()->json(Role::all());
    }

    public function storeRole(Request $request): JsonResponse
    {
        $request->validate(['role' => 'required|string|max:20|unique:roles']);
        $role = Role::create($request->only('role'));

        return response()->json($role, 201);
    }

    public function updateRole(Request $request, Role $role): JsonResponse
    {
        $request->validate(['role' => 'required|string|max:20|unique:roles,role,' . $role->id]);
        $role->update($request->only('role'));

        return response()->json($role);
    }

    public function destroyRole(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted']);
    }

    public function departments(): JsonResponse
    {
        return response()->json(Department::all());
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $request->validate(['department_name' => 'required|string|max:50|unique:departments']);
        $dept = Department::create($request->only('department_name'));

        return response()->json($dept, 201);
    }

    public function updateDepartment(Request $request, Department $department): JsonResponse
    {
        $request->validate(['department_name' => 'required|string|max:50|unique:departments,department_name,' . $department->id]);
        $department->update($request->only('department_name'));

        return response()->json($department);
    }

    public function destroyDepartment(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json(['message' => 'Department deleted']);
    }

    public function studyPrograms(): JsonResponse
    {
        return response()->json(StudyProgram::with('department')->get());
    }

    public function storeStudyProgram(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'program_name' => 'required|string|max:100',
        ]);
        $sp = StudyProgram::create($request->only('department_id', 'program_name'));

        return response()->json($sp->load('department'), 201);
    }

    public function updateStudyProgram(Request $request, StudyProgram $studyProgram): JsonResponse
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'program_name' => 'required|string|max:100',
        ]);
        $studyProgram->update($request->only('department_id', 'program_name'));

        return response()->json($studyProgram->load('department'));
    }

    public function destroyStudyProgram(StudyProgram $studyProgram): JsonResponse
    {
        $studyProgram->delete();

        return response()->json(['message' => 'Study program deleted']);
    }

    public function staffUnits(): JsonResponse
    {
        return response()->json(StaffUnit::all());
    }

    public function storeStaffUnit(Request $request): JsonResponse
    {
        $request->validate(['unit_name' => 'required|string|max:50|unique:staff_units']);
        $unit = StaffUnit::create($request->only('unit_name'));

        return response()->json($unit, 201);
    }

    public function updateStaffUnit(Request $request, StaffUnit $staffUnit): JsonResponse
    {
        $request->validate(['unit_name' => 'required|string|max:50|unique:staff_units,unit_name,' . $staffUnit->id]);
        $staffUnit->update($request->only('unit_name'));

        return response()->json($staffUnit);
    }

    public function destroyStaffUnit(StaffUnit $staffUnit): JsonResponse
    {
        $staffUnit->delete();

        return response()->json(['message' => 'Staff unit deleted']);
    }

    public function facilities(): JsonResponse
    {
        return response()->json(Facility::all());
    }

    public function storeFacility(Request $request): JsonResponse
    {
        $request->validate(['facility_name' => 'required|string|max:50|unique:facilities']);
        $facility = Facility::create($request->only('facility_name'));

        return response()->json($facility, 201);
    }

    public function updateFacility(Request $request, Facility $facility): JsonResponse
    {
        $request->validate(['facility_name' => 'required|string|max:50|unique:facilities,facility_name,' . $facility->id]);
        $facility->update($request->only('facility_name'));

        return response()->json($facility);
    }

    public function destroyFacility(Facility $facility): JsonResponse
    {
        $facility->delete();

        return response()->json(['message' => 'Facility deleted']);
    }
}
