<?php

namespace App\DataTables;

use App\Models\Permission;

use Yajra\DataTables\Services\DataTable as YajraDataTable;
use Yajra\DataTables\EloquentDataTable;

use App\Traits\DataTable;

class PermissionDataTable extends YajraDataTable {

    use DataTable;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {

        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'permissions.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Permission $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Permission $model) {

        $this->query = $model->newQuery();

        return $this->query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {

        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'asc']],
                'buttons' => [
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner'],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner']
                ]
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns() {

        return [
            _i('Nom') => [
                'name' => 'name',
                'data' => 'name'
            ],
            _i('Guard name') => [
                'name' => 'guard_name',
                'data' => 'guard_name'
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {

        return 'permissionsdatatable_' . time();
    }
}