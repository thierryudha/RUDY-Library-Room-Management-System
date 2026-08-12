<?php

namespace App\Repositories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RoomRepository extends BaseRepository
{
    /**
     * RoomRepository constructor.
     *
     * @param Room $model
     */
    public function __construct(Room $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all rooms with their facilities.
     *
     * @return Collection
     */
    public function getWithFacilities(): Collection
    {
        return $this->model->with('facilities')->get();
    }

    /**
     * Find a room by its ID with its facilities or fail.
     *
     * @param int|string $id
     * @return Model
     * 
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailWithFacilities($id): Model
    {
        return $this->model->with('facilities')->findOrFail($id);
    }
}
