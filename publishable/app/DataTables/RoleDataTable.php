<?php

namespace App\DataTables;

use App\Models\Role;

use Yajra\DataTables\Services\DataTable as YajraDataTable;
use Yajra\DataTables\EloquentDataTable;

use App\Traits\DataTable;

class RoleDataTable extends YajraDataTable {

    use DataTable;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {

        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'roles.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model) {

        $this->query = $model->newQuery();

        return $this->query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {

		$user	 = \Auth::user();
		$disabledCreate = "";
		if ($user->cannot('roles_create')) {
			$disabledCreate = " disabled";
		}
			 
		$aBtn	 = [
			['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner'.$disabledCreate],
			['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner'],
			['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner'],
			['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner'],
			['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner']
		]; 
		
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom' => 'Bfrtip',
                'stateSave' => true,
                'order' => [[0, 'asc']],
                'buttons' => $aBtn
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
    protected function filename(): string {

        return 'rolesdatatable_' . time();
    }
}
