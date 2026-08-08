<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records.
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Find a record by its ID.
     *
     * @param int|string $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Find a record by its ID or fail.
     *
     * @param int|string $id
     * @param array $columns
     * @return Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing record.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $record = $this->findOrFail($id);
        return $record->update($data);
    }

    /**
     * Delete a record.
     *
     * @param int|string $id
     * @return bool|null
     */
    public function delete($id): ?bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    /**
     * Get paginated records.
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }
}
