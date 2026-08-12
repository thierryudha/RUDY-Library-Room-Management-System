<?php

namespace App\Repositories;

use App\Models\Facility;

class FacilityRepository extends BaseRepository
{
    /**
     * FacilityRepository constructor.
     *
     * @param Facility $model
     */
    public function __construct(Facility $model)
    {
        parent::__construct($model);
    }
}
