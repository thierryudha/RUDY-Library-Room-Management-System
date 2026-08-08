<?php

namespace App\Http\Controllers\Api;

// Karena kita menggunakan Service & Repository pattern, tambahkan use service layer nanti
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
    // gunakan dependency injection: constructor injection untuk service dan method injection untuk form request 

}
