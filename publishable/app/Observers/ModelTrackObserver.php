<?php

namespace App\Observers;
  
use App\Traits\TracksHistoryTrait;
use Illuminate\Support\Facades\Auth;

class ModelTrackObserver
{
    use TracksHistoryTrait;

    public function updated($model) {
        if (!Auth::check()) {
            return;
        }
        $this->track($model, function ($value, $field) {

            if ($field == 'password') {

                return [
                    'body' => "{$field} : hidden ",
                ];
            } 
            /* elseif ($field == 'updated by') {

                throw new \Exception();
            } */
            elseif ($field == 'created by') {

                throw new \Exception();
            } 

            if ($field == 'deleted by') {

                return [
                    'body' => 'Deleted'
                ];
            } 
            else {

                return [
                    'body' => "Updated {$field} : ${value}",
                ];
            }
        });
    }

    public function created($model) {
        if (!Auth::check()) {
            return;
        }
        $this->track($model, function ($value, $field) {
            if($field == 'password') {
                $value = "hidden";
            }
            return [
                'body' => "Created {$field} : ${value}",
            ];
        });
    }
}
