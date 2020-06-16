<?php

namespace App\Observers;
  
use MediactiveDigital\MedKit\Traits\TracksHistoryTrait;
use Illuminate\Support\Facades\Auth;

class ModelTrackObserver
{
    use TracksHistoryTrait;

    public function updated($model) {

        if (!Auth::check()) {

            return;
        }

        $this->track($model, function ($value, $field) use ($model) {

            if ($field == 'password') {

                return [
                    'body' => "{$field} : hidden ",
                ];
            } 
            elseif ($field == 'created by' && !$model->wasRecentlyCreated) {

                throw new \Exception();
            } 
            elseif ($field == 'deleted by') {

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
