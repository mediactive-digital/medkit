<?php

namespace App\Http\Controllers\Back;

 
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use Carbon\Carbon; 
use App\Http\Controllers\Controller; 
 

class HistoryController extends Controller {

	public function index() {
		return view('back.history.list');
	}

    /**
     * @return mixed
     * @throws \Exception
     */
	public function list() {
        $query = History::select([
            'reference_table as table',
            'reference_id as identifiant',
            'actor_id as utilisateur',
            DB::raw('SUBSTRING_INDEX(`body`, " ", 1) as action'),
            DB::raw('
            GROUP_CONCAT(
                REPLACE(
                     REPLACE(
                        REPLACE(
                            body,
                            "Updated ",
                            ""
                        ),
                        "Deleted ",
                        ""
                    ),
                    "Created ",
                    ""
                )
            SEPARATOR "::"
            ) as modification'),
            'created_at',
            'updated_at',
        ])->groupBy(['reference_table','reference_id','action','utilisateur']);

		return Datatables::of($query)
            ->editColumn('utilisateur', function (History $history) {
                $user = User::find($history->utilisateur);
                return $user->name;
            })->editColumn('created_at', function (History $history) {
                return $history->created_at ? Carbon::parse($history->created_at)->format('d/m/Y H:i') : '';
            })->editColumn('updated_at', function (History $history) {
                return $history->updated_at ? Carbon::parse($history->updated_at)->format('d/m/Y H:i') : '';
            })
            ->make();
	}

}
