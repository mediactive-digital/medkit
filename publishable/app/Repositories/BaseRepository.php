<?php

namespace App\Repositories;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


abstract class BaseRepository {

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app) {

        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    abstract public function getFieldsSearchable();

    /**
     * Configure the Model
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel() {

        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {

            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Paginate records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $columns = ['*']) {

        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allQuery($search = [], $skip = null, $limit = null) {

        $query = $this->model->newQuery();

        if (count($search)) {

            foreach($search as $key => $value) {

                if (in_array($key, $this->getFieldsSearchable())) {

                    $query->where($key, $value);
                }
            }
        }

        if (!is_null($skip)) {

            $query->skip($skip);
        }

        if (!is_null($limit)) {

            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($search = [], $skip = null, $limit = null, $columns = ['*']) {

        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Create model record
     *
     * @param \Illuminate\Http\Request|array $request
     *
     * @return \Illuminate\Database\Eloquent\Model $model
     */
    public function create($request) {

        $datas = $request instanceof Request ? $request->validated() : $request;
        $model = $this->model->newInstance($datas);

        $model->save();

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find($id, $columns = ['*']) {

        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * Update model record
     *
     * @param \Illuminate\Http\Request|array $request
     * @param \Illuminate\Database\Eloquent\Model|int $model
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($request, $model) {

        $datas = $request instanceof Request ? $request->validated() : $request;
        $model = $model instanceof Model ? $model : $this->model->newQuery()->findOrFail($model);
        
        $model->fill($datas);
        $model->save();

        return $model;
    }

    /**
     * Delete model record
     *
     * @param \Illuminate\Database\Eloquent\Model|int $model
     *
     * @return bool|null|mixed $deleted
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete($model) {

        $model = $model instanceof Model ? $model : $this->model->newQuery()->findOrFail($model);

        $deleted = $model->delete();

        return $deleted;
    }
}
