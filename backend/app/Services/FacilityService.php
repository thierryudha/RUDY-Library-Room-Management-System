<?php

namespace App\Services;

use App\Repositories\FacilityRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class FacilityService
{
    protected FacilityRepository $facilityRepository;

    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;
    }

    public function getAllFacilities(): Collection
    {
        return $this->facilityRepository->all();
    }

    public function getFacilityById(int $id): Model
    {
        return $this->facilityRepository->findOrFail($id);
    }

    public function createFacility(array $data): Model
    {
        return $this->facilityRepository->create($data);
    }

    public function updateFacility(int $id, array $data): bool
    {
        return $this->facilityRepository->update($id, $data);
    }

    public function deleteFacility(int $id): bool
    {
        return $this->facilityRepository->delete($id);
    }
}
