<?php

namespace App\DataTables;

use App\Models\MailTemplate;

use Yajra\DataTables\Services\DataTable as YajraDataTable;
use Yajra\DataTables\EloquentDataTable;

use App\Traits\DataTable;

use DB;
use LaravelGettext;

class MailTemplateDataTable extends YajraDataTable {

    use DataTable;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {

        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'mail_templates.datatables_actions')
            ->editColumn('subject', function(MailTemplate $mailTemplate) {

                return $mailTemplate->subject;
            })
            ->editColumn('html_template', function(MailTemplate $mailTemplate) {

                return $mailTemplate->html_template;
            })
            ->editColumn('text_template', function(MailTemplate $mailTemplate) {

                return $mailTemplate->text_template;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\MailTemplate $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MailTemplate $model) {

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {

        $user = \Auth::user();
        $disabledCreate = "";
        if ($user->cannot('mail-templates_create')) {
            $disabledCreate = " disabled";
        }

        $aBtn = [
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
            _i('Mailable') => [
                'name' => 'mailable',
                'data' => 'mailable'
            ],
            _i('Subject') => [
                'name' => 'subject->' . LaravelGettext::getLocale(),
                'data' => 'subject'
            ],
            _i('Html template') => [
                'name' => 'html_template->' . LaravelGettext::getLocale(),
                'data' => 'html_template'
            ],
            _i('Text template') => [
                'name' => 'text_template->' . LaravelGettext::getLocale(),
                'data' => 'text_template'
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {

        return 'mail_templatesdatatable_' . time();
    }
}
