<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Facility\StoreFacilityRequest;
use App\Http\Requests\Api\Facility\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Services\FacilityService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FacilityController extends Controller
{
    use ApiResponse;

    protected FacilityService $facilityService;

    public function __construct(FacilityService $facilityService)
    {
        $this->facilityService = $facilityService;
    }

    public function index(): JsonResponse
    {
        $facilities = $this->facilityService->getAllFacilities();
        return $this->successResponse('Facilities retrieved successfully.', FacilityResource::collection($facilities));
    }

    public function store(StoreFacilityRequest $request): JsonResponse
    {
        $facility = $this->facilityService->createFacility($request->validated());
        return $this->successResponse('Facility created successfully.', new FacilityResource($facility), 201);
    }

    public function show($id): JsonResponse
    {
        $facility = $this->facilityService->getFacilityById($id);
        return $this->successResponse('Facility retrieved successfully.', new FacilityResource($facility));
    }

    public function update(UpdateFacilityRequest $request, $id): JsonResponse
    {
        $this->facilityService->updateFacility($id, $request->validated());
        // Reload model to get updated data
        $facility = $this->facilityService->getFacilityById($id);
        return $this->successResponse('Facility updated successfully.', new FacilityResource($facility));
    }

    public function destroy($id): JsonResponse
    {
        $this->facilityService->deleteFacility($id);
        return $this->successResponse('Facility deleted successfully.');
    }
}
