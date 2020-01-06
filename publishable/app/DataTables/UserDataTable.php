<?php

namespace App\DataTables;

use App\Models\User;

use Yajra\DataTables\Services\DataTable as YajraDataTable;
use Yajra\DataTables\EloquentDataTable;

use App\Traits\DataTable;

class UserDataTable extends YajraDataTable {

    use DataTable;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {

        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'users.datatables_actions')
            ->editColumn('theme', function(User $user) {

                return $this->editBooleanColumn($user->theme);
            })
            ->filterColumn('theme', function($query, $keyword) {

                $this->filterBooleanColumn('users.theme', $keyword);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model) {

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
		if ($user->cannot('users_create')) {
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
            _i('Prénom') => [
                'name' => 'first_name',
                'data' => 'first_name'
            ],
            _i('Adresse email') => [
                'name' => 'email',
                'data' => 'email'
            ],
            _i('Login') => [
                'name' => 'login',
                'data' => 'login'
            ],
            _i('Theme') => [
                'name' => 'theme',
                'data' => 'theme'
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {

        return 'usersdatatable_' . time();
    }
}
